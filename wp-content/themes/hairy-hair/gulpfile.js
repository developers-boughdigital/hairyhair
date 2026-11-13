const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const browserSync = require('browser-sync').create();
const concat = require('gulp-concat');
const terser = require('gulp-terser');
const cleanCSS = require('gulp-clean-css');
const uglify = require('gulp-uglify');

function copyFonts() {
  return gulp.src('src/fonts/**/*.{woff,woff2,ttf,otf,eot,svg}')
    .pipe(gulp.dest('dist/fonts'))
    .pipe(browserSync.stream());
}

function compileCustomCss() {
  return gulp.src('src/scss/style.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(gulp.dest('dist/css/vendor'))
    .pipe(browserSync.stream());
}

function compileThirdPartyCss() {
  return gulp.src([
    'node_modules/bootstrap/dist/css/bootstrap.min.css',
    'node_modules/slick-carousel/slick/slick.css',
    'node_modules/slick-carousel/slick/slick-theme.css'
  ])
    .pipe(gulp.dest('dist/css/vendor'))
    .pipe(browserSync.stream());
}

function mergeAndMinifyCss() {
  return gulp.src(['dist/css/vendor/*.css'])
    .pipe(concat('style.min.css'))
    .pipe(cleanCSS())
    .pipe(gulp.dest('dist/css'))
    .pipe(browserSync.stream());
}
function compileThirdPartyJs() {
  return gulp.src([
    'node_modules/jquery/dist/jquery.min.js',
    'node_modules/bootstrap/dist/js/bootstrap.bundle.min.js',
    'node_modules/slick-carousel/slick/slick.min.js',
    'src/js/custom.js'])
    .pipe(concat('custom.min.js'))
    .pipe(uglify())
    .pipe(gulp.dest('dist/js'));
}

function mergeAndMinifyJs() {
  return gulp.src(['dist/js/vendor/*.js'])
    .pipe(concat('custom.min.js'))
    .pipe(uglify())
    .pipe(gulp.dest('dist/js'))
    .pipe(browserSync.stream());
}

function watchFiles() {
  browserSync.init({
    server: {
      baseDir: './',
    },
    open: false,
  });


  gulp.watch('src/scss/**/*.scss', compileCustomCss);
  gulp.watch(['node_modules/bootstrap/dist/css/bootstrap.min.css'], compileThirdPartyCss);
  gulp.watch(['node_modules/jquery/dist/jquery.min.js', 'node_modules/bootstrap/dist/js/bootstrap.min.js'], compileThirdPartyJs);
  gulp.watch(['dist/css/vendor/style.css', 'dist/css/vendor/bootstrap.min.css'], mergeAndMinifyCss);
  gulp.watch(['dist/js/vendor/custom.js', 'dist/js/vendor/bootstrap.min.js'], mergeAndMinifyJs);
  gulp.watch('*.html').on('change', browserSync.reload);
}

exports.buildCss = gulp.series(compileCustomCss, compileThirdPartyCss, mergeAndMinifyCss);
exports.buildJs = gulp.series(compileThirdPartyJs, mergeAndMinifyJs);
exports.default = gulp.series(exports.buildCss, exports.buildJs, watchFiles);
exports.buildAssets = gulp.series(copyFonts);
exports.default = gulp.series(copyFonts, exports.buildCss, exports.buildJs, watchFiles);

