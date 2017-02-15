var gulp = require('gulp');
var sass = require('gulp-sass');

gulp.task('css', function(){
  return gulp.src('src/AppBundle/Resources/public/sass/**/*.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(gulp.dest('./web/css'));
});

// gulp.task('default', function () {});

gulp.task('sass:watch', function () {
  gulp.watch('./src/AppBundle/Resources/public/sass/**/*.scss', ['css']);
});
