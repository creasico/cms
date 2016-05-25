'use strict';

/* Gulp set up
--------------------------------------------------------------------------------- */

const fs    = require('fs');
const del   = require('del');
const path  = require('path');
const gulp  = require('gulp');
const gutil = require('gulp-util');

const connect     = require('gulp-connect-php');
const sequence    = require('run-sequence');
const browserSync = require('browser-sync');

// load all plugins with prefix 'gulp'
const $ = require('gulp-load-plugins')();

const configs = require('./config/assets');
const paths = {
    src: configs.paths.src,
    dest: configs.paths.dest
};

const stats = fs.statSync('./.env');
if (stats.isFile()) {
    require('dotenv').config();
}

// Determine build mode, default is 'dev'
configs.mode = 'dev';
// If mode is invalid, back to 'dev' mode
if (['dev', 'prod'].indexOf(process.env.MODE) !== -1) {
    configs.mode = process.env.MODE;
}

const production = configs.mode !== 'dev';

// Declaring 'serve' config
configs.port = process.env.APP_PORT || configs.serve.port; // 8080;
configs.host = process.env.APP_HOST || configs.serve.host; // 'localhost';
configs.url  = process.env.APP_URL  || configs.serve.url;  // 'localhost:8000';

/**
 * Print out something replacing default `console.log`
 *
 * @param  {String} message
 * @param  {String} color
 * @return {Mixed}
 */
const echo = (message, color) => {
    color = color && color in gutil.colors ? color : 'green';

    const cb = gutil.colors[color];
    const msg = cb(message);

    return gutil.log(msg);
};

/**
 * Simple helper to finalize each tasks
 *
 * @param  {Object} build Gulp pipe object
 * @return {Object}
 */
const asset = (build) => {
    return build.pipe(gulp.dest(paths.dest))
        .pipe(browserSync.stream());
};

/**
 * Simple error handler
 *
 * @param  {Object} err Error instance
 * @return {Mixed}
 */
const errorHandler = (err) => {
    const message = err.message.replace(err.fileName + ': ', '');
    const filename = err.fileName.replace(__dirname, '');

    if (err.lineNumber) {
        filename += ` (${err.lineNumber})`;
    }

    echo([
        `[Error] ${message}`,
        ` filename: ${filename}`
    ].join('\n'), 'red');

    this.emit('end');
};

for (let key in configs.patterns) {
    paths[key] = configs.paths.src + configs.patterns[key];
}


/* Task: Compile SCSS
--------------------------------------------------------------------------------- */

gulp.task('build:styles', () => {
    configs.sass.includePaths = [
        `${paths.src}vendor`
    ];

    const build = gulp.src(paths.styles, { base: paths.src })
        .pipe($.sass(configs.sass).on('error', $.sass.logError))
        .pipe($.autoprefixer(configs.autoprefixer));

    if (production) {
        build.pipe($.cleanCss())
            .on('error', errorHandler);
    }

    return asset(build);
});



/* Task: Minify JS
--------------------------------------------------------------------------------- */

gulp.task('build:scripts', () => {
    const build = gulp.src(paths.scripts, { base: paths.src })
        .pipe($.babel({ preset: ['es2015'] }))
        .on('error', errorHandler);

    if (production) {
        build.pipe($.uglify(configs.uglify))
            .on('error', errorHandler);
    }

    return asset(build);
});



/* Task: Optimize image
--------------------------------------------------------------------------------- */

gulp.task('build:images', () => {
    const build = gulp.src(paths.images, { base: paths.src });

    if (production) {
        build.pipe($.imagemin(configs.imagemin))
            .on('error', errorHandler);
    }

    return asset(build);
});



/* Task: Serve
--------------------------------------------------------------------------------- */

gulp.task('serve', () => {
    const sync = browserSync.init({
        port: configs.port,
        host: configs.host,
        proxy: { target: configs.url },
        open: 'open' in configs.serve ? configs.serve.open : false,
        logConnections: false
    });

    // Let's assume that you already setup your app server vhost
    if (configs.url.indexOf('localhost:8000') !== -1) {
        return connect.server({ base: './public' }, () => {
            return sync;
        });
    }

    return sync;
});



/* Task: Watch
--------------------------------------------------------------------------------- */

gulp.task('watch', ['serve'], (done) => {
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



/* Task: Serve
--------------------------------------------------------------------------------- */

gulp.task('wdio', (done) => {
    const conf = {
        user: process.env.BROWSERSTACK_USER,
        key: process.env.BROWSERSTACK_KEY,
        baseUrl: configs.url,
        host: 'hub.browserstack.com'
    };

    gulp.src('./config/webdriver.js')
        .pipe($.webdriver(conf));

    return done();
});



/* Task: Clean
--------------------------------------------------------------------------------- */

gulp.task('clean', (done) => {
    del(paths.dest + configs.patterns.assets).then(() => {
        echo('Assets directory cleaned', 'green');
    });

    return done();
});



/* Task: Build
--------------------------------------------------------------------------------- */

gulp.task('build', ['build:styles', 'build:scripts', 'build:images']);



/* Task: Default
--------------------------------------------------------------------------------- */

gulp.task('default', (done) => {
    return sequence('clean', 'build', done);
});
