var gulp = require('gulp');
var browserSync = require('browser-sync');

/**
 * Watch task
 */
gulp.task('default', function () {
    browserSync.init({
        proxy: "moowgly.local/moowgly",
        open: false
    });
});