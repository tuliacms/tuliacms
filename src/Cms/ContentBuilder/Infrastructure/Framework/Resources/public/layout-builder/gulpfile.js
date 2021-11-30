let gulp = require('gulp');
let sass = require('gulp-sass');
let sourcemaps = require('gulp-sourcemaps');
let concat = require('gulp-concat');
let webpack = require('webpack-stream');

process.env.NODE_ENV = 'production';

let webpackConfig = require('./webpack.config');

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
        .pipe(webpack(webpackConfig))
        .pipe(gulp.dest('dist/js'));
});

gulp.task('js', gulp.series(['js-global']));
gulp.task('css', gulp.series(['sass']));

gulp.task('publish', () => {
    return gulp.src([
        'dist/**'
    ]).pipe(gulp.dest('../../../../../../../../public/assets/core/content-builder-layout-builder'));
});

gulp.task('default', gulp.series(['css', 'js', 'publish'], () => {
    gulp.watch('src/sass/**/*.scss', gulp.series(['css', 'publish']));
    gulp.watch('src/js/**/*', gulp.series(['js', 'publish']));
}));
