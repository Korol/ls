var gulp = require('gulp');
var compass = require('gulp-compass');  // Плагин для компиляции SCSS в CSS
var webpack = require('gulp-webpack');  // Плагин для запуска Webpack
var concat = require('gulp-concat');    // Плагин для склейки файлов
var csso = require('gulp-csso');        // Плагин для сжатия CSS
var uglify = require('gulp-uglify');    // Плагин для сжатия JS
var ftp = require('vinyl-ftp');         // Плагин для загрузки файлов на FTP
// var sftp = require('gulp-sftp');        // Плагин для загрузки файлов на SFTP

/** SCSS файлы */
const SCSS_TARGET = './scss/*.scss';

/** CSS файлы */
const CSS_TARGET = [
    './src/public/css/assol.css',
    './src/public/bootstrap/dropdowns_enhancement/dropdowns-enhancement.min.css',
    './src/public/bootstrap/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css',
    './src/public/lightbox/css/lightbox.min.css'
];

/** JS файлы */
const JS_TARGET = [
    './src/public/jquery/jquery.numeric.min.js',
    './src/public/jquery/jquery.tmpl.min.js',
    './src/public/jquery/jquery.form.min.js',
    './src/public/fullcalendar/lib/moment.min.js',
    './src/public/ion.sound/ion.sound.min.js',
    './src/public/bootstrap/js/bootstrap.min.js',
    './src/public/bootstrap/bootstrap-filestyle/src/bootstrap-filestyle.min.js',
    './src/public/bootstrap/js/ie10-viewport-bug-workaround.js', // IE10 viewport hack for Surface/desktop Windows 8 bug
    './src/public/bootstrap/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js',
    './src/public/bootstrap/dropdowns_enhancement/dropdowns-enhancement.js',
    './src/public/bootstrap/bootbox.min.js',
    './src/public/fullcalendar/fullcalendar.min.js',
    './src/public/fullcalendar/lang/ru.js',
    './src/public/js/src/utils.js',
    './src/public/js/src/assol.system.events.js',
    './src/public/js/src/style-scripts.js',
    './src/public/lightbox/js/lightbox.min.js'
];

/** JS файлы ASSOL */
const ASSOL_JS_TARGET = './src/public/js/src/*.js';

const REACT_SRC = [
    'react/**/*.jsx',
    'react/**/*.js'
];

/** Файлы ReactJS для автозагрузки при изменение на FTP */
const FTP_REACT_TARGET = './src/public/build/bundle*';

/** Файлы CSS для автозагрузки при изменение на FTP */
const FTP_CSS_TARGET = './src/public/build/assol.min.css';

const FTP_SETTINGS = {
    host: 'a39003.ftp.mchost.ru',
    user: 'a39003_assol',
    pass: '4w0b0j11k9'
};

// Компиляция SCSS файлов
gulp.task('compass', function() {
    gulp.src(SCSS_TARGET)
        .pipe(compass({
            style       : 'expanded',
            comments    : true,
            sass        : 'scss',
            css         : 'src/public/css'
        }));
});

// Задача "css". Запускается командой "gulp css"
gulp.task('css', function () {
    gulp.src(CSS_TARGET)                            // файлы, которые обрабатываем
        .pipe(concat('assol.min.css'))              // склеиваем все CSS
        .pipe(csso({restructure: false}))           // сжатие CSS с запретом изменения структуры
        .pipe(gulp.dest('./src/public/build/'));    // результат пишем по указанному адресу
});

// Сборка JS проекта с помощью webpack
gulp.task('webpack', function() {
    return gulp.src('react/index.jsx')
        .pipe(webpack(require(('./webpack.config.js'))))
        .pipe(gulp.dest('src/public/build'));
});

// Задача "assol-js". Запускается командой "gulp assol-js"
gulp.task('assol-js', function() {
    gulp.src(ASSOL_JS_TARGET)                   // файлы, которые обрабатываем
        .pipe(uglify())                         // сжатие JS
        .pipe(gulp.dest('./src/public/js/'));   // результат пишем по указанному адресу
});

// Задача "js". Запускается командой "gulp js"
gulp.task('js', function() {
    gulp.src(JS_TARGET)                             // файлы, которые обрабатываем
        .pipe(concat('assol.min.js'))               // склеиваем все JS
        .pipe(uglify())                             // сжатие JS
        .pipe(gulp.dest('./src/public/build/'));    // результат пишем по указанному адресу
});

gulp.task('ftp-react', function () {
    var conn = ftp.create(FTP_SETTINGS);

    return gulp.src(FTP_REACT_TARGET)
        .pipe(conn.newer('/httpdocs/public/build'))
        .pipe(conn.dest('/httpdocs/public/build'));
});

gulp.task('ftp-css', function () {
    var conn = ftp.create(FTP_SETTINGS);

    return gulp.src(FTP_CSS_TARGET, { buffer: false })
        .pipe(conn.newer('/httpdocs/public/build'))
        .pipe(conn.dest('/httpdocs/public/build'));
});

// Сборка проекта
gulp.task('build', ['compass', 'css', 'assol-js', 'js', 'webpack']);

// Автосборка при изменение файлов
gulp.task('watch', function () {
    gulp.watch(SCSS_TARGET, ['compass']);           // Наблюдение за файлами SCSS
    gulp.watch(SCSS_TARGET, ['css']);               // Наблюдение за файлами CSS
    gulp.watch(ASSOL_JS_TARGET, ['assol-js']);      // Наблюдение за самописными JS
    gulp.watch(JS_TARGET, ['js']);                  // Наблюдение за JS различных библиотек
    gulp.watch(REACT_SRC, ['webpack']);             // Наблюдение за файлами проекта ReactJS
    gulp.watch(FTP_REACT_TARGET, ['ftp-react']);    // Наблюдение за файлами для отправки по FTP
    gulp.watch(FTP_CSS_TARGET, ['ftp-css']);        // Наблюдение за файлами для отправки по FTP
});

// Задача по умолчанию
gulp.task('default', ['build', 'watch']);