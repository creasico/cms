'use strict';

const fs = require('fs');
const gutil = require('gulp-util');

class Helpers {

    constructor (gulp, sync) {
        this._gulp = gulp;
        this._sync = sync;

        const stats = fs.statSync(__dirname + '/../.env');

        if (stats.isFile()) {
            require('dotenv').config();
        }

        this.configs = require(__dirname + '/assets');
        this.paths = {
            src: this.configs.paths.src,
            dest: this.configs.paths.dest
        };

        // Determine build mode, default is 'dev'
        this.configs.mode = 'dev';
        // If mode is invalid, back to 'dev' mode
        if (['dev', 'prod'].indexOf(process.env.MODE) !== -1) {
            this.configs.mode = process.env.MODE;
        }

        this.production = this.configs.mode !== 'dev';

        // Declaring 'serve' config
        this.configs.port = process.env.APP_PORT || this.configs.serve.port; // 8080;
        this.configs.host = process.env.APP_HOST || this.configs.serve.host; // 'localhost';
        this.configs.url  = process.env.APP_URL  || this.configs.serve.url;  // 'localhost:8000';

        for (let key in this.configs.patterns) {
            this.paths[key] = [
                this.configs.paths.src + this.configs.patterns[key]
            ];

            if (key === 'fonts') {
                this.paths[key].push('./node_modules/*/' + this.configs.patterns[key]);
            }
        }
    }

    /**
     * Print out something replacing default `console.log`
     *
     * @param  {String} message
     * @param  {String} color
     * @return {Mixed}
     */
    echo (message, color) {
        color = color && color in gutil.colors ? color : 'green';

        const cb = gutil.colors[color];

        return gutil.log(cb(message));
    }

    /**
     * Simple helper to finalize each tasks
     *
     * @param  {Object}   build Gulp pipe object
     * @param  {Function} build Gulp done function
     * @return {Object}
     */
    asset (build, done) {
        const stream = build.pipe(this._gulp.dest(this.paths.dest))
            .pipe(this._sync.stream());

        if (done) {
            return done();
        }

        return stream;
    }

    /**
     * Simple error handler
     *
     * @param  {Object} err Error instance
     * @return {Mixed}
     */
    errorHandler (err) {
        const message = err.message.replace(err.fileName + ': ', '');
        const filename = err.fileName.replace(__dirname, '');

        if (err.lineNumber) {
            filename += ` (${err.lineNumber})`;
        }

        helpers.echo([
            `[Error]`,
            `  message: ${message}`,
            ` filename: ${filename}`
        ].join('\n'), 'red');
    }

};

module.exports = (gulp, sync) => {
    return new Helpers(gulp, sync);
};
