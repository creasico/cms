
/* Gulp set up
--------------------------------------------------------------------------------- */

var del   = require('del'),
    path  = require('path'),
    gulp  = require('gulp'),
    gutil = require('gulp-util'),

    sequence    = require('run-sequence'),
    browserSync = require('browser-sync'),

    // load all plugins with prefix 'gulp'
    $ = require('gulp-load-plugins')();


var configs = require('./config/assets');
var paths = {
    src: configs.paths.src,
    dest: configs.paths.dest
};

/**
 * Print out something replacing default `console.log`
 *
 * @param  {String} message
 * @param  {String} color
 * @return {Mixed}
 */
var echo = function (message, color) {
    var color = color && color in gutil.colors ? color : 'green',
        cb = gutil.colors[color],
        msg = cb(message);

    return gutil.log(msg);
};

/**
 * Simple error handler
 *
 * @param  {Object} err Error instance
 * @return {Mixed}
 */
configs.errorHandler = function (err) {
    var message = err.message.replace(err.fileName + ': ', ''),
        filename = err.fileName.replace(__dirname, '') + ' (' + err.lineNumber + ')';

    return echo([
        '[Error] ' + message,
        ' filename: ' + filename
    ].join('\n'), 'red');
};

for (var key in configs.patterns) {
    paths[key] = configs.paths.src + configs.patterns[key];
}

configs.port  = process.env.APP_PORT || 8080;
configs.host  = process.env.APP_HOST || 'localhost';
configs.proxy = 'localhost:8000';


/* Task: Compile SCSS
--------------------------------------------------------------------------------- */

gulp.task('build:styles', function () {
    return gulp.src(paths.styles, { base: paths.src })
        .pipe($.sass({ outputStyle: 'compressed' }).on('error', $.sass.logError))
        .pipe($.autoprefixer(configs.autoprefixer))
        .pipe($.cleanCss())
        .pipe($.rename({ suffix: '.min' }))
        .pipe(gulp.dest(paths.dest))
        .pipe(browserSync.stream());
});



/* Task: Minify JS
--------------------------------------------------------------------------------- */

gulp.task('build:scripts', function () {
    return gulp.src(paths.scripts, { base: paths.src })
        .pipe($.uglify().on('error', configs.errorHandler))
        .pipe($.rename({ suffix: '.min' }))
        .pipe(gulp.dest(paths.dest))
        .pipe(browserSync.stream());
});



/* Task: Optimize image
--------------------------------------------------------------------------------- */

gulp.task('build:images', function () {
    return gulp.src(paths.images, { base: paths.src })
        .pipe($.imagemin({ progressive: true }))
        .pipe(gulp.dest(paths.dest))
        .pipe(browserSync.stream());
});



/* Task: Watch
--------------------------------------------------------------------------------- */

gulp.task('watch', ['browsersync', 'build'], function (done) {
    // SCSS
    gulp.watch(paths.styles,  ['build:styles']);
    // Uglify
    gulp.watch(paths.scripts, ['build:scripts']);
    // Imagemin
    gulp.watch(paths.images,  ['build:images']);
    // Reload
    gulp.watch(configs.patterns.server)
        .on('change', browserSync.reload);

    // Done
    return done();
});



/* Task: Browsersync
--------------------------------------------------------------------------------- */

gulp.task('browsersync', function () {
    return browserSync.init({
        port: configs.port,
        host: configs.host,
        proxy: { target: configs.proxy },
        open: 'external',
        logConnections: false
    });
});



/* Task: Clean
--------------------------------------------------------------------------------- */

gulp.task('clean', function (done) {
    del(paths.dest + configs.patterns.front).then(function () {
        echo('Assets directory cleaned', 'green');
    });

    return done();
});



/* Task: Copy from Front-end
--------------------------------------------------------------------------------- */

gulp.task('copy:frontend', function () {
    var srcToCopy = configs.paths.front + configs.patterns.front;

    return gulp.src(srcToCopy, { base: configs.paths.front })
        .pipe(gulp.dest(paths.dest));
});



/* Task: Build
--------------------------------------------------------------------------------- */

gulp.task('build', ['build:styles', 'build:scripts', 'build:images']);



/* Task: Default
--------------------------------------------------------------------------------- */

gulp.task('default', function (done) {
    return sequence('clean', 'copy:frontend', 'build', done);
});
