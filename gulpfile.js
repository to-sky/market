'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass');
var watch = require('gulp-watch');
var autoPrefixer = require('gulp-autoprefixer');
var cleanCSS = require('gulp-clean-css');
var uglify = require('gulp-uglify');
var concat = require('gulp-concat');
var imagemin = require('gulp-imagemin');
var pngquant = require('imagemin-pngquant');
var rename = require('gulp-rename');
var rimraf = require('rimraf');
var mainBowerFiles = require('main-bower-files');
var livereload = require('gulp-livereload');
var assets = './app/Resources/assets/';
var path = {
    bower: {
        fonts: {
            bootstrap: './vendor/bower_components/bootstrap/dist/fonts/*.*',
            fontAwesome: './vendor/bower_components/font-awesome/fonts/*.*'
        }
    },
    src: {
        css: assets + 'sass/**/*.sass',
        js: assets + 'js/*.js',
        img: assets + 'img/**/*.*',
        fonts: assets + 'fonts/**/*.*'
    },
    build: {
        css: './web/assets/css',
        js: './web/assets/js',
        img: './web/images/app',
        fonts: './web/assets/fonts'
    },
    clean: './web/assets'
};

gulp.task('css', function () {
    return gulp.src(path.src.css)
        .pipe(sass().on('error', sass.logError))
        .pipe(autoPrefixer({
           browsers: ['last 3 version'],
           cascade: false
        }))
        .pipe(cleanCSS())
        .pipe(concat('app.min.css'))
        .pipe(gulp.dest(path.build.css));
});

gulp.task('css:vendors', function () {
    return gulp.src(mainBowerFiles('**/*.css',{
            "overrides": {
                "normalize-css": {
                    "main": [
                        "./normalize.css"
                    ]
                },
                "bootstrap": {
                    "main": [
                        "./dist/css/bootstrap.min.css",
                        "./dist/css/bootstrap-theme.min.css"
                    ]
                },
                "font-awesome": {
                    "main": [
                        "./css/font-awesome.min.css"
                    ]
                }
            }
        }))
        .pipe(cleanCSS())
        .pipe(concat('vendors.min.css'))
        .pipe(gulp.dest(path.build.css));
});

gulp.task('js', function () {
   return gulp.src(path.src.js)
       .pipe(uglify())
       .pipe(concat('app.min.js'))
       .pipe(gulp.dest(path.build.js));
});

gulp.task('js:vendors', function () {
    return gulp.src(mainBowerFiles('**/*.js',{
           "overrides": {
               "jquery": {
                   "main": [
                       "./dist/jquery.min.js"
                   ]
               },
               "bootstrap": {
                   "main": [
                       "./dist/js/bootstrap.min.js"
                   ]
               }
           }
        }))
        .pipe(uglify())
        .pipe(concat('vendors.min.js'))
        .pipe(gulp.dest(path.build.js));
});

gulp.task('img', function () {
    return gulp.src(path.src.img)
        .pipe(imagemin({
           interlaced: true,
           progressive: true,
           svgoPlugins: [{removeViewBox: false}],
           use: [pngquant()]
        }))
        .pipe(gulp.dest(path.build.img));
});

gulp.task('fonts', function () {
    return gulp.src([
            path.src.fonts,
            path.bower.fonts.bootstrap,
            path.bower.fonts.fontAwesome
        ])
        .pipe(gulp.dest(path.build.fonts));
});

gulp.task('watch', function () {
    livereload.listen({start: true});

    gulp.watch('app/Resources/**').on('change', livereload.changed);
    gulp.watch('app/config/**').on('change', livereload.changed);
    gulp.watch('src/**').on('change', livereload.changed);
    gulp.watch('web/assets/**').on('change', livereload.changed);

    gulp.watch(path.src.css, ['css']).on('change', livereload.changed);
    gulp.watch(path.src.js, ['js']).on('change', livereload.changed);
});

gulp.task('clean', function (cb) {
    return rimraf(path.clean, cb);
});

gulp.task('build', [
    'css',
    'css:vendors',
    'js',
    'js:vendors',
    'img',
    'fonts'
]);

gulp.task('default', ['build', 'watch']);
