var gulp = require('gulp');
var gulpif = require('gulp-if');
var uglify = require('gulp-uglify');
var uglifycss = require('gulp-uglifycss');
var concat = require('gulp-concat');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var debug = require('gulp-debug');
var livereload = require('gulp-livereload');
var order = require('gulp-order');
var merge = require('merge-stream');
var env = 'prod';

var appRootPath = 'web/assets/app/';

var paths = {
    app: {
        js_head: [
            'web/assets/vendor/modernizr/modernizr.js',
            'web/assets/vendor/lazysizes/lazysizes.min.js'
        ],
        js: [
            'web/assets/vendor/jquery/jquery.min.js',
            'web/assets/vendor/jquery.easing/js/jquery.easing.min.js',
            'web/assets/vendor/bootstrap/dist/js/bootstrap.min.js',
            'web/assets/vendor/scrollreveal/dist/scrollreveal.min.js',
            'web/assets/vendor/classie/classie.js',
            'web/assets/vendor/wow/dist/wow.min.js',
            'web/bundles/appsite/js/script.js'
        ],
        js_ie: [
            'web/assets/vendor/html5shiv/dist/html5shiv.js',
            'web/assets/vendor/respond/src/respond.js'
        ],
        css: [
            'web/assets/vendor/bootstrap/dist/css/bootstrap.css',
            'web/assets/vendor/font-awesome/css/font-awesome.min.css',
            'web/assets/vendor/flag-icon-css/css/flag-icon.min.css',
            'web/bundles/appsite/css/main.css',
            'web/assets/vendor/wow/css/libs/animate.css'
        ],
        img: [
            'web/bundles/appsite/images/**'
        ],
        fonts: [
            'web/bundles/appsite/fonts/**',
            'web/assets/vendor/font-awesome/fonts/**'
        ],
        flags: [
            'web/assets/vendor/flag-icon-css/flags/**'
        ]
    }
};

gulp.task('app-js', function () {
    return gulp.src(paths.app.js)
        .pipe(concat('app.js'))
        .pipe(gulpif(env === 'prod', uglify()))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(appRootPath + 'js/'))
    ;
});

gulp.task('app-js-head', function () {
    return gulp.src(paths.app.js_head)
        .pipe(concat('app_head.js'))
        .pipe(gulpif(env === 'prod', uglify()))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(appRootPath + 'js/'))
        ;
});

gulp.task('app-js-ie', function () {
    return gulp.src(paths.app.js_head)
        .pipe(concat('app_ie.js'))
        .pipe(gulpif(env === 'prod', uglify()))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(appRootPath + 'js/'))
        ;
});

gulp.task('app-css', function() {
    return gulp.src(paths.app.css)
        .pipe(concat('style.css'))
        .pipe(gulpif(env === 'prod', uglifycss()))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(appRootPath + 'css/'))
        ;
});

gulp.task('app-img', function() {
    return gulp.src(paths.app.img)
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(appRootPath + 'img/'))
    ;
});
gulp.task('app-fonts', function() {
    return gulp.src(paths.app.fonts)
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(appRootPath + 'fonts/'))
        ;
});

gulp.task('app-flags', function() {
    return gulp.src(paths.app.flags)
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(appRootPath + 'flags/'))
        ;
});

gulp.task('app-watch', function() {
    livereload.listen();

    gulp.watch(paths.app.js, ['app-js']);
    gulp.watch(paths.app.js_head, ['app-js-head']);
    gulp.watch(paths.app.js_ie, ['app-js-ie']);
    gulp.watch(paths.app.css, ['app-css']);
    gulp.watch(paths.app.img, ['app-img']);
    gulp.watch(paths.app.fonts, ['app-fonts']);
});

gulp.task('default', ['app-js', 'app-js-head', 'app-js-ie', 'app-css', 'app-fonts', 'app-flags']);
gulp.task('watch', ['default', 'app-watch']);
