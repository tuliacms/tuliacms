'use strict';

const webpack = require('webpack');
const { VueLoaderPlugin } = require('vue-loader');
const FileManagerPlugin = require('filemanager-webpack-plugin');
const path = require('path');

module.exports = {
    mode: 'development',
    entry: {
        'tulia-lisa-blocks.js': './src/index.js',
    },
    output: {
        filename: '[name]',
        path: path.resolve(__dirname, 'dist'),
        library: {
            name: 'TuliaLisaThemeBlocks',
            type: 'window'
        }
    },
    module: {
        rules: [
            {
                test: /\.vue$/,
                use: 'vue-loader'
            },
            {
                test: /\.s[ac]ss$/i,
                use: [
                    'css-loader',
                    'sass-loader'
                ]
            },
            {
                test: /\.css$/i,
                use: ["css-loader"],
            },
        ]
    },
    watchOptions: {
        aggregateTimeout: 200,
        poll: 1000,
        ignored: ['**/dist', '**/node_modules'],
    },
    externals: {
        vue: 'Vue',
        TuliaEditor: 'TuliaEditor'
    },
    resolve: {
        extensions: ['.js', '.scss'],
    },
    devtool: 'source-map',
    plugins: [
        new VueLoaderPlugin(),
        new FileManagerPlugin({
            events: {
                onEnd: {
                    copy: [
                        { source: './dist/**/*', destination: './../../../../../../../public/assets/theme/tulia/lisa/tulia-editor-blocks' },
                    ],
                },
            },
        }),
    ]
};

if (process.env.NODE_ENV === 'production') {
    module.exports.devtool = 'source-map';
    module.exports.optimization = {
        minimize: true
    };
    // http://vue-loader.vuejs.org/en/workflow/production.html
    module.exports.plugins = (module.exports.plugins || []).concat([
        new webpack.DefinePlugin({
            'process.env': {
                NODE_ENV: '"production"'
            }
        }),
        new webpack.LoaderOptionsPlugin({
            minimize: true
        })
    ]);
}
