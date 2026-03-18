import resolve from '@rollup/plugin-node-resolve';
import summary from 'rollup-plugin-summary';
import terser  from '@rollup/plugin-terser';
import copy from 'rollup-plugin-copy'
import typescript from '@rollup/plugin-typescript';
import scss from 'rollup-plugin-scss';
import sass from 'rollup-plugin-sass';
import clean from '@rollup-extras/plugin-clean';
import replace from '@rollup/plugin-replace';
import dotenv from 'dotenv';

dotenv.config();

const envReplacements = {
    preventAssignment: true,
    'process.env.APP_ENV': JSON.stringify(process.env.APP_ENV)
};

const copyConfig = {
  targets: [
  	{
        src: 'resources/images',
        dest: 'public/dist'
    },
    {
        src: 'resources/fonts',
        dest: 'public/dist'
    },
    {
        src: 'resources/audio',
        dest: 'public/dist'
    }
  ]
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
const config = [
    {

        input: 'resources/typescript/app.ts',
        output: {
            dir: './public/dist/js/',
            format: 'es'
        },

        plugins: [
            copy(copyConfig),
            typescript(),
            resolve(),
            clean(),
            replace(envReplacements),
            summary(),
        ],
        preserveEntrySignatures: false,
    },
    {
        input: 'resources/sass/critical.scss',
        output: {
            dir: './public/dist/css/'
        },
        plugins: [
            scss({
                processor: (css) => {
                    return css;
                },
                fileName: 'critical.css',
                outputStyle: 'compressed',
                watch: [
                    'resources/sass/critical.scss',
                    'resources/sass/critical/'
                ],
            }),
            removeEmptyCssJs(),

            summary()
        ]
    },
    /**
     * Widgets
     */
    {
        input: 'resources/sass/widgets/panels.scss',
        output: {
            dir: './public/dist/css/widgets'
        },
        plugins: [
            scss({
                fileName: 'panels.css',
                outputStyle: 'compressed',
                watch: ['resources/sass/widgets/panels.scss']

            }),
            removeEmptyCssJs(),
            summary()
        ]
    },
    {
        input: 'resources/sass/widgets/hero.scss',
        output: {
            dir: './public/dist/css/widgets'
        },
        plugins: [
            scss({
                fileName: 'hero.css',
                outputStyle: 'compressed',
                watch: ['resources/sass/widgets/hero.scss']
            }),
            removeEmptyCssJs(),
            summary()
        ]
    },
    {
        input: 'resources/sass/widgets/decorativeFrames.scss',
        output: {
            dir: './public/dist/css/widgets'
        },
        plugins: [
            scss({
                fileName: 'decorativeFrames.css',
                outputStyle: 'compressed',
                watch: ['resources/sass/widgets/decorativeFrames.scss']
            }),
            removeEmptyCssJs(),
            summary()
        ]
    },
    {
        input: 'resources/sass/widgets/randomQuotation.scss',
        output: {
            dir: './public/dist/css/widgets'
        },
        plugins: [
            scss({
                fileName: 'randomQuotation.css',
                outputStyle: 'compressed',
                watch: ['resources/sass/widgets/randomQuotation.scss']
            }),
            removeEmptyCssJs(),
            summary()
        ]
    },
    {
        input: 'resources/sass/widgets/tables.scss',
        output: {
            dir: './public/dist/css/widgets'
        },
        plugins: [
            scss({
                fileName: 'tables.css',
                outputStyle: 'compressed',
                watch: ['resources/sass/widgets/tables.scss']
            }),
            removeEmptyCssJs(),
            summary()
        ]
    },
    {
        input: 'resources/sass/widgets/tabs.scss',
        output: {
            dir: './public/dist/css/widgets'
        },
        plugins: [
            scss({
                fileName: 'tabs.css',
                outputStyle: 'compressed',
                watch: ['resources/sass/widgets/tabs.scss']
            }),
            removeEmptyCssJs(),
            summary()
        ]
    },
    {
        input: 'resources/sass/widgets/accordions.scss',
        output: {
            dir: './public/dist/css/widgets'
        },
        plugins: [
            scss({
                fileName: 'accordions.css',
                outputStyle: 'compressed',
                watch: ['resources/sass/widgets/accordions.scss']

            }),
            removeEmptyCssJs(),
            summary()
        ]
    },
    {
        input: 'resources/sass/widgets/badges.scss',
        output: {
            dir: './public/dist/css/widgets'
        },
        plugins: [
            scss({
                fileName: 'badges.css',
                outputStyle: 'compressed',
                watch: ['resources/sass/widgets/badges.scss']

            }),
            removeEmptyCssJs(),
            summary()
        ]
    },
    {
        input: 'resources/sass/widgets/feats.scss',
        output: {
            dir: './public/dist/css/widgets'
        },
        plugins: [
            scss({
                fileName: 'feats.css',
                outputStyle: 'compressed',
                watch: ['resources/sass/widgets/feats.scss']
            }),
            removeEmptyCssJs(),
            summary()
        ]
    },
    {
        input: 'resources/sass/widgets/slideshow.scss',
        output: {
            dir: './public/dist/css/widgets'
        },
        plugins: [
            scss({
                fileName: 'slideshow.css',
                outputStyle: 'compressed',
                watch: ['resources/sass/widgets/slideshow.scss']
            }),
            removeEmptyCssJs(),
            summary()
        ]
    },
    {
        input: 'resources/sass/widgets/wikieditor.scss',
        output: {
            dir: './public/dist/css/widgets'
        },
        plugins: [
            scss({
                fileName: 'wikieditor.css',
                outputStyle: 'compressed',
                watch: ['resources/sass/widgets/wikieditor.scss']
            }),
            removeEmptyCssJs(),
            summary()
        ]
    },
    {
        input: 'resources/sass/widgets/cards.scss',
        output: {
            dir: './public/dist/css/widgets'
        },
        plugins: [
            scss({
                fileName: 'cards.css',
                outputStyle: 'compressed',
                watch: ['resources/sass/widgets/cards.scss']
            }),
            removeEmptyCssJs(),
            summary()
        ]
    },
    /**
     * Puzzles
     */
    {
        input: 'resources/typescript/puzzles/hammer/hammerBoot.ts',
        output: {
            dir: './public/dist/js/',
            format: 'es',
        },

        plugins: [
            typescript(),
            resolve()
        ],
        preserveEntrySignatures: false,
    },
    {
        input: 'resources/typescript/puzzles/lightforge/lightforgeBoot.ts',
        output: {
            dir: './public/dist/js/',

            format: 'es',
        },

        plugins: [
            typescript(),
            resolve()
        ],
        preserveEntrySignatures: false,
    },
    {
        input: 'resources/typescript/puzzles/casebook/casebookBoot.ts',
        output: {
            dir: './public/dist/js/',
            format: 'es',
        },

        plugins: [
            typescript(),
            replace(envReplacements),
            sass(),
            resolve()
        ],
        preserveEntrySignatures: false,
    },
    {
        input: 'resources/typescript/puzzles/buttons/buttonsBoot.ts',
        output: {
            dir: './public/dist/js/',
            format: 'es',
        },

        plugins: [
            typescript(),
            replace(envReplacements),
            sass(),
            resolve()
        ],
        preserveEntrySignatures: false,
    },
    {
        input: 'resources/sass/puzzles/lightforge.scss',
        output: {
            dir: './public/dist/css/puzzles'
        },
        plugins: [
            scss({
                fileName: 'lightforge.css',
                outputStyle: 'compressed',
                watch: ['resources/sass/puzzles/lightforge.scss']

            }),
            summary()
        ]
    },
    {
        input: 'resources/sass/puzzles/hammer.scss',
        output: {
            dir: './public/dist/css/puzzles'
        },
        plugins: [
            scss({
                fileName: 'hammer.css',
                outputStyle: 'compressed',
                watch: ['resources/sass/puzzles/hammer.scss']
            }),
            summary()
        ]
    },
    {
        input: 'resources/sass/puzzles/casebook.scss',
        output: {
            dir: './public/dist/css/casebook'
        },
        plugins: [
            scss({
                fileName: 'casebook.css',
                outputStyle: 'compressed',
                watch: ['resources/sass/puzzles/casebook.scss']
            }),
            summary()
        ]
    },
    {
        input: 'resources/sass/puzzles/casebook-clue.scss',
        output: {
            dir: './public/dist/css/casebook'
        },
        plugins: [
            scss({
                fileName: 'casebook-clue.css',
                outputStyle: 'compressed',
                watch: ['resources/sass/puzzles/casebook-clue.scss']
            }),
            summary()
        ]
    },
    {
        input: 'resources/sass/admin/admin.scss',
        output: {
            dir: './public/dist/css/'
        },
        plugins: [
            scss({
                fileName: 'admin.css',
                outputStyle: 'compressed',
                watch: [
                    'resources/sass/admin/admin.scss',
                    'resources/sass/admin/',
                    'resources/sass/criticalOld/'
                ],
            }),

            summary()
        ]
    },

];



if (process.env.NODE_ENV !== 'development') {
    config[0].plugins.push(terser(terserOptions));
    config[13].plugins.push(terser(terserOptions)); // hammerBoot
    config[14].plugins.push(terser(terserOptions)); // lightforgeBoot
    config[15].plugins.push(terser(terserOptions)); // casebookBoot
    config[16].plugins.push(terser(terserOptions)); // buttonsBoot
}

export default config;
