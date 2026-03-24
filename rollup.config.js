import resolve from '@rollup/plugin-node-resolve';
import summary from 'rollup-plugin-summary';
import terser from '@rollup/plugin-terser';
import typescript from '@rollup/plugin-typescript';
import scss from 'rollup-plugin-scss';
import clean from '@rollup-extras/plugin-clean';
import replace from '@rollup/plugin-replace';
import dotenv from 'dotenv';
import { transform as lightningTransform } from 'lightningcss';
import { optimize as optimizeSvg } from 'svgo';

import fg from 'fast-glob';
import fs from 'node:fs/promises';
import path from 'node:path';

dotenv.config();

const envReplacements = {
    preventAssignment: true,
    'process.env.APP_ENV': JSON.stringify(process.env.APP_ENV)
};

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

function copyDirectory({
   srcDir,
   destDir,
   pattern = '**/*'
}) {
    return {
        name: `copy-directory:${srcDir}`,

        buildStart() {
            this.addWatchFile(srcDir);
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
    return {
        name: 'copy-optimised-svgs',

        buildStart() {
            this.addWatchFile(srcDir);
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

function makeScssConfig(input, outputDir, fileName, watch = []) {
    return {
        input,
        output: {
            dir: outputDir
        },
        plugins: [
            scss({
                fileName,
                outputStyle: 'expanded',
                processor: (css) => minifyCssWithLightning(css, fileName),
                watch
            }),
            removeEmptyCssJs(),
            summary()
        ]
    };
}

const config = [
    {
        input: 'resources/typescript/app.ts',
        output: {
            dir: './public/dist/js/',
            format: 'es'
        },
        plugins: [
            clean(),
            copyDirectory({
                srcDir: 'resources/images',
                destDir: 'public/dist/images'
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
            typescript(),
            resolve(),
            replace(envReplacements),
            summary(),
        ],
        preserveEntrySignatures: false,
    },

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
            'resources/sass/admin.scss',
            'resources/sass/admin/'
        ]
    ),

    makeScssConfig(
        'resources/sass/widgets/panels.scss',
        './public/dist/css/widgets',
        'panels.css',
        ['resources/sass/widgets/panels.scss']
    ),

    makeScssConfig(
        'resources/sass/widgets/hero.scss',
        './public/dist/css/widgets',
        'hero.css',
        ['resources/sass/widgets/hero.scss']
    ),

    makeScssConfig(
        'resources/sass/widgets/decorativeFrames.scss',
        './public/dist/css/widgets',
        'decorativeFrames.css',
        ['resources/sass/widgets/decorativeFrames.scss']
    ),

    makeScssConfig(
        'resources/sass/widgets/randomQuotation.scss',
        './public/dist/css/widgets',
        'randomQuotation.css',
        ['resources/sass/widgets/randomQuotation.scss']
    ),

    makeScssConfig(
        'resources/sass/widgets/tables.scss',
        './public/dist/css/widgets',
        'tables.css',
        ['resources/sass/widgets/tables.scss']
    ),

    makeScssConfig(
        'resources/sass/widgets/tabs.scss',
        './public/dist/css/widgets',
        'tabs.css',
        ['resources/sass/widgets/tabs.scss']
    ),

    makeScssConfig(
        'resources/sass/widgets/accordions.scss',
        './public/dist/css/widgets',
        'accordions.css',
        ['resources/sass/widgets/accordions.scss']
    ),

    makeScssConfig(
        'resources/sass/widgets/badges.scss',
        './public/dist/css/widgets',
        'badges.css',
        ['resources/sass/widgets/badges.scss']
    ),

    makeScssConfig(
        'resources/sass/widgets/feats.scss',
        './public/dist/css/widgets',
        'feats.css',
        ['resources/sass/widgets/feats.scss']
    ),

    makeScssConfig(
        'resources/sass/widgets/slideshow.scss',
        './public/dist/css/widgets',
        'slideshow.css',
        ['resources/sass/widgets/slideshow.scss']
    ),

    makeScssConfig(
        'resources/sass/widgets/cards.scss',
        './public/dist/css/widgets',
        'cards.css',
        ['resources/sass/widgets/cards.scss']
    )
];

if (process.env.NODE_ENV !== 'development') {
    config[0].plugins.push(terser(terserOptions));
}

export default config;
