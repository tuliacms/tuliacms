let gulp = require('gulp');
let sass = require('gulp-sass');
let sourcemaps = require('gulp-sourcemaps');
let concat = require('gulp-concat');

gulp.task('sass', () => {
    return gulp.src('src/sass/*.scss')
        .pipe(sourcemaps.init())
        .pipe(sass())
        .pipe(sourcemaps.write('../maps', {
            includeContent: true,
            sourceRoot: 'src/sass'
        }))
        .pipe(concat('bundle.min.css'))
        .pipe(gulp.dest('dist/css'));
});

gulp.task('css', gulp.series(['sass']));

gulp.task('js-global', function () {
    return gulp
        .src([
            'src/js/frontend-toolbar.js'
        ])
        .pipe(concat('bundle.min.js'))
        //.pipe(uglify())
        .pipe(gulp.dest('dist/js'));
});

gulp.task('js', gulp.series(['js-global']));

gulp.task('publish', () => {
    return gulp.src(['dist/**/*'])
        .pipe(gulp.dest('../../../../../../public/assets/core/frontend-toolbar'));
});

gulp.task('default', gulp.series(['css', 'js', 'publish'], () => {
    gulp.watch('src/sass/**/*.scss', gulp.series(['css', 'publish']));
    gulp.watch('src/js/**/*', gulp.series(['js', 'publish']));
}));
