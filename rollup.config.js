import resolve from '@rollup/plugin-node-resolve';
import summary from 'rollup-plugin-summary';
import terser from '@rollup/plugin-terser';
import typescript from '@rollup/plugin-typescript';
import scss from 'rollup-plugin-scss';
import replace from '@rollup/plugin-replace';
import dotenv from 'dotenv';
import { transform as lightningTransform } from 'lightningcss';
import { optimize as optimizeSvg } from 'svgo';
import sharp from 'sharp';
import tsconfigPaths from 'rollup-plugin-tsconfig-paths';

import fg from 'fast-glob';
import { existsSync, statSync } from 'node:fs';
import fs from 'node:fs/promises';
import path from 'node:path';

dotenv.config();

const envReplacements = {
    preventAssignment: true,
    'process.env.APP_ENV': JSON.stringify(process.env.APP_ENV)
};

const terserOptions = {
    ecma: 2021,
    module: true,
    warnings: true,
    keep_fnames: true,
    mangle: {
        properties: {
            regex: /^__/,
        },
    },
};

const widgetScssEntries = [
    'hero',
    'decorativeFrames',
    'randomQuotation',
    'tables',
    'tabs',
    'accordions',
    'badges',
    'feats',
    'slideshow',
    'cards',
    'userCards'
];

function resolveInternalAlias(source) {
    const aliases = [
        {
            prefix: '@shared/',
            directory: 'shared'
        }
    ];

    for (const alias of aliases) {
        if (!source.startsWith(alias.prefix)) {
            continue;
        }

        const relativePath = source.slice(alias.prefix.length);
        const basePath = path.resolve(alias.directory, relativePath);
        const candidates = [
            basePath,
            `${basePath}.ts`,
            `${basePath}.tsx`,
            path.join(basePath, 'index.ts'),
            path.join(basePath, 'index.tsx')
        ];

        const resolvedPath = candidates.find((candidate) => existsSync(candidate));

        if (resolvedPath !== undefined) {
            return resolvedPath;
        }
    }

    return null;
}

function internalAliasPlugin() {
    return {
        name: 'internal-alias-plugin',

        async resolveId(source, importer, options) {
            const resolvedAliasPath = resolveInternalAlias(source);

            if (resolvedAliasPath === null) {
                return null;
            }

            return this.resolve(resolvedAliasPath, importer, {
                ...options,
                skipSelf: true
            });
        }
    };
}

function minifyCssWithLightning(css, filePath = 'unknown.css') {
    const { code } = lightningTransform({
        filename: filePath,
        code: Buffer.from(css),
        minify: true,
        sourceMap: false
    });

    return code.toString();
}

function optimiseSvg(svg, filePath = 'unknown.svg') {
    const result = optimizeSvg(svg, {
        path: filePath,
        multipass: true,
        plugins: [
            { name: 'preset-default' },
            'removeDimensions'
        ]
    });

    return result.data;
}

function cleanDirectory(dir) {
    let cleaned = false;

    return {
        name: `clean-directory:${dir}`,

        async buildStart() {
            if (cleaned) {
                return;
            }

            await fs.rm(dir, { recursive: true, force: true });
            cleaned = true;
        }
    };
}

function copyDirectory({
                           srcDir,
                           destDir,
                           pattern = '**/*'
                       }) {
    const resolvedSrcDir = path.resolve(srcDir);

    return {
        name: `copy-directory:${srcDir}`,

        buildStart() {
            this.addWatchFile(srcDir);
        },

        watchChange(id) {
            if (!id.startsWith(resolvedSrcDir)) {
                return;
            }

            this.warn(`Asset changed: ${path.relative(process.cwd(), id)}`);
        },

        async writeBundle() {
            const files = await fg(pattern, {
                cwd: srcDir,
                onlyFiles: true,
                dot: false
            });

            await Promise.all(
                files.map(async (relativePath) => {
                    const sourcePath = path.join(srcDir, relativePath);
                    const outputPath = path.join(destDir, relativePath);

                    await fs.mkdir(path.dirname(outputPath), { recursive: true });
                    await fs.copyFile(sourcePath, outputPath);
                })
            );
        }
    };
}

function copyOptimisedSvgs({
                               srcDir = 'resources/svgs',
                               destDir = 'public/dist/svgs'
                           } = {}) {
    const resolvedSrcDir = path.resolve(srcDir);

    async function watchSvgFiles(context) {
        const files = await fg('**/*.svg', {
            cwd: srcDir,
            onlyFiles: true
        });

        for (const relativePath of files) {
            context.addWatchFile(path.resolve(srcDir, relativePath));
        }
    }

    return {
        name: 'copy-optimised-svgs',

        async buildStart() {
            await watchSvgFiles(this);
        },

        async watchChange(id) {
            if (!id.endsWith('.svg') || !id.startsWith(resolvedSrcDir)) {
                return;
            }

            this.warn(`Asset changed: ${path.relative(process.cwd(), id)}`);
            await watchSvgFiles(this);
        },

        async writeBundle() {
            const files = await fg('**/*.svg', {
                cwd: srcDir,
                onlyFiles: true
            });

            await Promise.all(
                files.map(async (relativePath) => {
                    const sourcePath = path.join(srcDir, relativePath);
                    const outputPath = path.join(destDir, relativePath);

                    const raw = await fs.readFile(sourcePath, 'utf8');
                    const optimised = optimiseSvg(raw, sourcePath);

                    await fs.mkdir(path.dirname(outputPath), { recursive: true });
                    await fs.writeFile(outputPath, optimised, 'utf8');
                })
            );
        }
    };
}

function generateWebpImages({
                                srcDir = 'resources/images',
                                destDir = 'public/dist/images',
                                subDir = null,
                                pattern = '**/*.{png,jpg,jpeg}',
                                quality = 82
                            } = {}) {
    const resolvedSrcDir = path.resolve(srcDir);

    return {
        name: 'generate-webp-images',

        buildStart() {
            this.addWatchFile(srcDir);
        },

        watchChange(id) {
            if (!id.startsWith(resolvedSrcDir)) {
                return;
            }

            this.warn(`Asset changed: ${path.relative(process.cwd(), id)}`);
        },

        async writeBundle() {
            const files = await fg(pattern, {
                cwd: srcDir,
                onlyFiles: true,
                caseSensitiveMatch: false
            });

            await Promise.all(
                files.map(async (relativePath) => {
                    const sourcePath = path.join(srcDir, relativePath);
                    const outputRelativePath = relativePath
                        .replace(/\.(png|jpe?g)$/i, '.webp')
                        .split(path.sep)
                        .map((segment) => segment.toLowerCase())
                        .join(path.sep);

                    const finalDestDir = subDir ? path.join(destDir, subDir) : destDir;
                    const outputPath = path.join(finalDestDir, outputRelativePath);

                    await fs.mkdir(path.dirname(outputPath), { recursive: true });

                    await sharp(sourcePath)
                        .webp({
                            quality,
                            effort: 6
                        })
                        .toFile(outputPath);
                })
            );
        }
    };
}

function removeEmptyCssJs() {
    return {
        name: 'remove-empty-css-js',

        generateBundle(options, bundle) {
            for (const [fileName, file] of Object.entries(bundle)) {
                if (
                    file.type === 'chunk' &&
                    file.isEntry &&
                    file.code.trim() === '' &&
                    Object.keys(file.modules).length === 1 &&
                    /\.(scss|sass|css)$/.test(Object.keys(file.modules)[0])
                ) {
                    delete bundle[fileName];
                }
            }
        }
    };
}

function removeEmptyEntryChunks() {
    return {
        name: 'remove-empty-entry-chunks',

        generateBundle(options, bundle) {
            for (const [fileName, file] of Object.entries(bundle)) {
                if (
                    file.type === 'chunk' &&
                    file.isEntry &&
                    file.code.trim() === ''
                ) {
                    delete bundle[fileName];
                }
            }
        }
    };
}

function emptyEntryPlugin(entryId) {
    return {
        name: `empty-entry:${entryId}`,

        resolveId(source) {
            if (source === entryId) {
                return source;
            }

            return null;
        },

        load(id) {
            if (id === entryId) {
                return 'export {};';
            }

            return null;
        }
    };
}

function rewriteAssetUrls(css) {
    return css
        .replace(/url\((['"]?)svgs\//g, 'url($1/dist/svgs/')
        .replace(/url\((['"]?)images\//g, 'url($1/dist/images/')
        .replace(/url\((['"]?)fonts\//g, 'url($1/dist/fonts/');
}

function expandScssWatchEntries(entries) {
    const watchFiles = new Set();

    for (const entry of entries) {
        const hasGlob = /[*?[\]{}()!+@]/.test(entry);

        if (hasGlob) {
            fg.sync(entry, {
                onlyFiles: true
            }).forEach((filePath) => {
                watchFiles.add(path.resolve(filePath));
            });

            continue;
        }

        if (!existsSync(entry)) {
            continue;
        }

        const stats = statSync(entry);

        if (stats.isDirectory()) {
            fg.sync('**/*.scss', {
                cwd: entry,
                onlyFiles: true
            }).forEach((filePath) => {
                watchFiles.add(path.resolve(entry, filePath));
            });

            continue;
        }

        watchFiles.add(path.resolve(entry));
    }

    return Array.from(watchFiles);
}

function makeScssConfig(input, outputDir, fileName, watch = []) {
    const resolvedWatch = expandScssWatchEntries([
        ...watch,
        'resources/sass/**/*.scss',
        'puzzleTemplates/**/*.scss'
    ]);

    return {
        input,
        output: {
            dir: outputDir
        },
        plugins: [
            scss({
                fileName,
                outputStyle: 'expanded',
                includePaths: [
                    path.resolve('resources/sass'),
                    path.resolve('resources')
                ],
                processor: (css) => minifyCssWithLightning(rewriteAssetUrls(css), fileName),
                watch: resolvedWatch
            }),
            removeEmptyCssJs(),
            summary()
        ]
    };
}

function makeTypescriptConfig(input, outputDir, tsconfigPath = './tsconfig.app.json') {
    const plugins = [
        internalAliasPlugin(),
        typescript({
            tsconfig: tsconfigPath
        }),
        tsconfigPaths({
            tsConfigPath: tsconfigPath
        }),
        resolve(),
        replace(envReplacements),
        summary(),
    ];

    if (process.env.NODE_ENV !== 'development') {
        plugins.push(terser(terserOptions));
    }

    return {
        input,
        output: {
            dir: outputDir,
            format: 'es'
        },
        plugins,
        preserveEntrySignatures: false,
    };
}

function makeWebpCopyConfig(srcDir, outputDir, pattern = '**/*.{png,jpg,jpeg}', quality = 82) {
    const entryId = `virtual:webp:${srcDir}`;

    return {
        input: entryId,
        output: {
            dir: outputDir,
            format: 'es'
        },
        plugins: [
            emptyEntryPlugin(entryId),
            generateWebpImages({
                srcDir,
                destDir: outputDir,
                pattern,
                quality
            }),
            removeEmptyEntryChunks(),
            summary()
        ],
        preserveEntrySignatures: false,
    };
}

function getMainAppConfig() {
    return {
        input: 'resources/typescript/app.ts',
        output: {
            dir: './public/dist/js/',
            format: 'es'
        },
        plugins: [
            cleanDirectory('public/dist'),
            cleanDirectory('public/contrib'),
            copyDirectory({
                srcDir: 'resources/images',
                destDir: 'public/dist/images'
            }),
            generateWebpImages({
                srcDir: 'resources/images',
                destDir: 'public/dist/images',
                subDir: 'converted',
                quality: 82
            }),
            copyDirectory({
                srcDir: 'resources/fonts',
                destDir: 'public/dist/fonts'
            }),
            copyDirectory({
                srcDir: 'resources/audio',
                destDir: 'public/dist/audio'
            }),
            copyOptimisedSvgs({
                srcDir: 'resources/svgs',
                destDir: 'public/dist/svgs'
            }),
            internalAliasPlugin(),
            typescript({
                tsconfig: './tsconfig.app.json'
            }),
            tsconfigPaths({
                tsConfigPath: './tsconfig.app.json'
            }),
            resolve(),
            replace(envReplacements),
            summary(),
            ...(process.env.NODE_ENV !== 'development' ? [terser(terserOptions)] : [])
        ],
        preserveEntrySignatures: false,
    };
}

function getWidgetConfigs() {
    return widgetScssEntries.map((name) =>
        makeScssConfig(
            `resources/sass/widgets/${name}.scss`,
            './public/dist/css/widgets',
            `${name}.css`,
            [`resources/sass/widgets/${name}.scss`]
        )
    );
}

async function getPuzzleTemplateConfigs() {
    const puzzleRoot = 'puzzleTemplates';

    const puzzleDirs = await fg('*', {
        cwd: puzzleRoot,
        onlyDirectories: true
    }).catch(() => []);

    const configs = [];

    for (const puzzleName of puzzleDirs) {
        const puzzleDir = path.join(puzzleRoot, puzzleName);
        const tsDir = path.join(puzzleDir, 'typeScript');
        const sassDir = path.join(puzzleDir, 'sass');
        const imagesDir = path.join(puzzleDir, 'images');
        const outputDir = `./public/contrib/puzzles/${puzzleName}`;
        const cssOutputDir = `${outputDir}/css`;
        const jsOutputDir = `${outputDir}/js`;
        const imagesOutputDir = `${outputDir}/images`;

        const bootInput = path.join(tsDir, `${puzzleName}Boot.ts`);

        try {
            await fs.access(bootInput);

            configs.push(
                makeTypescriptConfig(
                    bootInput,
                    jsOutputDir,
                    './tsconfig.puzzles.json'
                )
            );
        } catch {
            // No boot file for this puzzle; skip TS build
        }

        const scssFiles = await fg('*.scss', {
            cwd: sassDir,
            onlyFiles: true,
            ignore: ['_*.scss']
        }).catch(() => []);

        for (const scssFile of scssFiles) {
            const scssInput = path.join(sassDir, scssFile);
            const cssFileName = scssFile.replace(/\.scss$/i, '.css');

            configs.push(
                makeScssConfig(
                    scssInput,
                    cssOutputDir,
                    cssFileName,
                    [scssInput, sassDir]
                )
            );
        }

        try {
            await fs.access(imagesDir);

            configs.push(makeWebpCopyConfig(imagesDir, imagesOutputDir));
        } catch {
            // No image directory for this puzzle; skip asset conversion
        }
    }

    return configs;
}

export default (async () => {
    const widgetConfigs = getWidgetConfigs();
    const puzzleConfigs = await getPuzzleTemplateConfigs();

    return [
        getMainAppConfig(),

        makeScssConfig(
            'resources/sass/critical.scss',
            './public/dist/css/',
            'critical.css',
            [
                'resources/sass/critical.scss',
                'resources/sass/critical/'
            ]
        ),
        makeScssConfig(
            'resources/sass/admin/admin.scss',
            './public/dist/css/',
            'admin.css',
            [
                'resources/sass/admin/admin.scss',
                'resources/sass/admin/',
                'resources/sass/critical/'
            ]
        ),

        ...widgetConfigs,
        ...puzzleConfigs
    ];
})();
