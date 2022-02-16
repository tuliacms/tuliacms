let gulp = require('gulp');
let sass = require('gulp-sass')(require('sass'));
let concat = require('gulp-concat');
let sourcemaps = require('gulp-sourcemaps');

gulp.task('theme.js', () => {
    return gulp.src([
        'src/theme/js/datetimepicker-defaults.js',
        'src/theme/js/search-anything.js',
        'src/theme/js/selected-elements-actions.js',
        'src/theme/js/script.js',
    ])
    .pipe(concat("backend-theme.bundle.min.js"))
    .pipe(gulp.dest('dist/theme/js'));
});

gulp.task('theme.sass', () => {
    return gulp.src('src/theme/sass/*.scss')
        .pipe(sourcemaps.init())
        .pipe(sass())
        .pipe(sourcemaps.write('maps', {
            includeContent: true,
            sourceRoot: 'src/theme/sass'
        }))
        .pipe(gulp.dest('dist/theme/css'));
});

gulp.task('theme.images', () => {
    return gulp.src(['src/theme/images/**/*', 'src/theme/images/*'])
        .pipe(gulp.dest('dist/theme/images'));
});

gulp.task('publish', () => {
    return gulp.src([
        'dist/**'
    ]).pipe(gulp.dest('../../../../../../../../public/assets/core/backend'));
});

gulp.task('default', gulp.series(['theme.js', 'theme.sass', 'theme.images', 'publish'], () => {
    gulp.watch('src/theme/sass/**/*.scss', gulp.series(['theme.sass', 'publish']));
    gulp.watch('src/theme/js/*.js', gulp.series(['theme.js', 'publish']));
    gulp.watch('src/theme/images/*/*', gulp.series(['theme.images', 'publish']));
}));
