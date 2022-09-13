let gulp = require('gulp');
let sass = require('gulp-sass');
let sourcemaps = require('gulp-sourcemaps');
let concat = require('gulp-concat');

gulp.task('sass', () => {
    return gulp.src('src/sass/*.scss')
        .pipe(sourcemaps.init())
        .pipe(sass())
        /*.pipe(sourcemaps.write('../maps', {
            includeContent: true,
            sourceRoot: 'src/sass'
        }))*/
        .pipe(concat('bundle.min.css'))
        .pipe(gulp.dest('dist/css'));
});

gulp.task('css', gulp.series(['sass']));

gulp.task('js-global', function () {
    return gulp
        .src([
            'node_modules/jquery/dist/jquery.js',
            'node_modules/popper.js/dist/umd/popper.js',
            'node_modules/bootstrap/dist/js/bootstrap.js',
            'node_modules/js-cookie/src/js.cookie.js',
            'src/js/script.js'
        ])
        .pipe(concat('bundle.min.js'))
        //.pipe(uglify())
        .pipe(gulp.dest('dist/js'));
});

gulp.task('js-customizer', function () {
    return gulp
        .src([
            //'src/js/customizer.js',
            'src/js/editor-plugins.js'
        ])
        .pipe(gulp.dest('dist/js/'));
});

gulp.task('images', function () {
    return gulp
        .src('src/images/**/*')
        .pipe(gulp.dest('dist/images/'));
});

gulp.task('js', gulp.series(['js-global', 'js-customizer']));

gulp.task('publish', () => {
    return gulp.src(['dist/**/*'])
        .pipe(gulp.dest('../../../../../../../public/assets/theme/tulia/lisa/theme'));
});

gulp.task('default', gulp.series(['css', 'js', 'images', 'publish'], () => {
    gulp.watch('src/sass/**/*.scss', gulp.series(['css', 'publish']));
    gulp.watch('src/js/**/*', gulp.series(['js', 'publish']));
    gulp.watch('src/images/**/*', gulp.series(['images', 'publish']));
}));
