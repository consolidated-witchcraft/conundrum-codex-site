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
function myExample() {
    return {
        name: 'my-example', // this name will show up in logs and errors
        resolveId(source) {

            return null; // other ids should be handled as usually
        },
        load(id) {

            return null;
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
        input: 'resources/sass/critical.scss',
        output: {
            dir: './public/dist/css/'
        },
        plugins: [
            myExample(),
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

            summary()
        ]
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
                    'resources/sass/critical/'
                ],
            }),

            summary()
        ]
    },

];



if (process.env.NODE_ENV !== 'development') {
  config[0].plugins.push(terser(terserOptions));
  config[1].plugins.push(terser(terserOptions));
  config[2].plugins.push(terser(terserOptions));
}

export default config;
