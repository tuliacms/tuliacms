/*var
    gulp = require('gulp'),
    webpack = require('webpack'),
    path = require('path');

gulp.task('webpack', function(done) {
    webpack({
        entry: './src/main.js',
        output: {
            path: path.resolve(__dirname, './dist'),
            publicPath: '/dist/',
            filename: 'build.js'
        },
        module: {
            rules: [
                {
                    test: /\.css$/,
                    use: [
                        'vue-style-loader',
                        'css-loader'
                    ],
                },
                {
                    test: /\.scss$/,
                    use: [
                        'vue-style-loader',
                        'css-loader',
                        'sass-loader'
                    ],
                },
                {
                    test: /\.sass$/,
                    use: [
                        'vue-style-loader',
                        'css-loader',
                        'sass-loader?indentedSyntax'
                    ],
                },
                {
                    test: /\.vue$/,
                    loader: 'vue-loader',
                    options: {
                        loaders: {
                            // Since sass-loader (weirdly) has SCSS as its default parse mode, we map
                            // the "scss" and "sass" values for the lang attribute to the right configs here.
                            // other preprocessors should work out of the box, no loader config like this necessary.
                            'scss': [
                                'vue-style-loader',
                                'css-loader',
                                'sass-loader'
                            ],
                            'sass': [
                                'vue-style-loader',
                                'css-loader',
                                'sass-loader?indentedSyntax'
                            ]
                        }
                        // other vue-loader options go here
                    }
                },
                {
                    test: /\.js$/,
                    loader: 'babel-loader',
                    exclude: /node_modules/
                },
                {
                    test: /\.(png|jpg|gif|svg)$/,
                    loader: 'file-loader',
                    options: {
                        name: '[name].[ext]?[hash]'
                    }
                }
            ]
        },
        resolve: {
            alias: {
                'vue$': 'vue/dist/vue.esm.js'
            },
            extensions: ['*', '.js', '.vue', '.json']
        },
        devServer: {
            historyApiFallback: true,
            noInfo: true,
            overlay: true
        },
        performance: {
            hints: false
        },
        devtool: '#eval-source-map'
    }, function(error) {
        var pluginError;

        if (error) {
            pluginError = new gulpUtil.PluginError('webpack', error);

            if (done) {
                done(pluginError);
            } else {
                gulpUtil.log('[webpack]', pluginError);
            }

            return;
        }

        if (done) {
            done();
        }
    });
});

gulp.task('default', gulp.series(['webpack'], (done) => {
    done();
}));*/


let gulp = require('gulp');
let sass = require('gulp-sass');
let sourcemaps = require('gulp-sourcemaps');
let concat = require('gulp-concat');
let webpack = require('webpack-stream');
let path = require('path');

gulp.task('sass', () => {
    return gulp.src('src/sass/*.scss')
        .pipe(sourcemaps.init())
        .pipe(sass())
        /*.pipe(sourcemaps.write('../maps', {
            includeContent: true,
            sourceRoot: 'app/src/sass'
        }))*/
        .pipe(concat('bundle.min.css'))
        .pipe(gulp.dest('dist/css'));
});

gulp.task('js-global', function () {
    return gulp
        .src([
            'src/js/main.js'
        ])
        .pipe(
            webpack({
                entry: './src/js/main.js',
                output: {
                    path: path.resolve(__dirname, './dist'),
                    publicPath: '/dist/js/',
                    filename: 'build.js'
                },
                module: {
                    rules: [
                        {
                            test: /\.css$/,
                            use: [
                                'vue-style-loader',
                                'css-loader'
                            ],
                        },
                        {
                            test: /\.scss$/,
                            use: [
                                'vue-style-loader',
                                'css-loader',
                                'sass-loader'
                            ],
                        },
                        {
                            test: /\.sass$/,
                            use: [
                                'vue-style-loader',
                                'css-loader',
                                'sass-loader?indentedSyntax'
                            ],
                        },
                        {
                            test: /\.vue$/,
                            loader: 'vue-loader',
                            options: {
                                loaders: {
                                    // Since sass-loader (weirdly) has SCSS as its default parse mode, we map
                                    // the "scss" and "sass" values for the lang attribute to the right configs here.
                                    // other preprocessors should work out of the box, no loader config like this necessary.
                                    'scss': [
                                        'vue-style-loader',
                                        'css-loader',
                                        'sass-loader'
                                    ],
                                    'sass': [
                                        'vue-style-loader',
                                        'css-loader',
                                        'sass-loader?indentedSyntax'
                                    ]
                                }
                                // other vue-loader options go here
                            }
                        },
                        {
                            test: /\.js$/,
                            loader: 'babel-loader',
                            exclude: /node_modules/
                        },
                        {
                            test: /\.(png|jpg|gif|svg)$/,
                            loader: 'file-loader',
                            options: {
                                name: '[name].[ext]?[hash]'
                            }
                        }
                    ]
                },
                resolve: {
                    alias: {
                        'vue$': 'vue/dist/vue.esm.js'
                    },
                    extensions: ['*', '.js', '.vue', '.json']
                },
                devServer: {
                    historyApiFallback: true,
                    noInfo: true,
                    overlay: true
                },
                performance: {
                    hints: false
                },
                devtool: '#eval-source-map'
            })
        )
        .pipe(gulp.dest('dist/js'));
});

gulp.task('js', gulp.series(['js-global']));
gulp.task('css', gulp.series(['sass']));

gulp.task('publish', () => {
    return gulp.src([
        'dist/**'
    ]).pipe(gulp.dest('../../../../../../../public/assets/core/contact-forms'));
});

gulp.task('default', gulp.series(['css', 'js', 'publish'], () => {
    gulp.watch('src/sass/**/*.scss', gulp.series(['css', 'publish']));
    gulp.watch('src/js/**/*', gulp.series(['js', 'publish']));
}));
