/**
 * Gulpfile.
 *
 * Gulp with WordPress.
 *
 * Implements:
 *      1. Live reloads browser with BrowserSync.
 *      2. CSS: Sass to CSS conversion, error catching, Autoprefixing, Sourcemaps,
 *         CSS minification, and Merge Media Queries.
 *      3. JS: Concatenates & uglifies Vendor and Custom JS files.
 *      4. Images: Minifies PNG, JPEG, GIF and SVG images.
 *      5. Watches files for changes in CSS or JS.
 *      6. Watches files for changes in PHP.
 *      7. Corrects the line endings.
 *      8. InjectCSS instead of browser page reload.
 *      9. Generates .pot file for i18n and l10n.
 *
 */

/**
 * Load Gulp Configuration.
 *
 */
const config = require( './gulp.config.js' );

/**
 * Load Plugins.
 *
 * Load gulp plugins and passing them semantic names.
 */
const gulp = require( 'gulp' ); // Gulp of-course.
const fs = require( 'fs' );
const argv = require( 'yargs' ).argv;
const semver = require( 'semver' );

// Package config.
let pkgConfig = JSON.parse(fs.readFileSync(config.packageJSON));

// CSS related plugins.
const sass = require( 'gulp-sass' ); // Gulp plugin for Sass compilation.
const minifycss = require( 'gulp-uglifycss' ); // Minifies CSS files.
const autoprefixer = require( 'gulp-autoprefixer' ); // Autoprefixing magic.
const mmq = require( 'gulp-merge-media-queries' ); // Combine matching media queries into one.
const rtlcss = require( 'gulp-rtlcss' ); // Generates RTL stylesheet.
const compass = require( 'gulp-compass' );
const path = require( 'path' );

// JS related plugins.
const concat = require( 'gulp-concat' ); // Concatenates JS files.
const uglify = require( 'gulp-uglify' ); // Minifies JS files.
const babel = require( 'gulp-babel' ); // Compiles ESNext to browser compatible JS.

// Image related plugins.
const imagemin = require( 'gulp-imagemin' ); // Minify PNG, JPEG, GIF and SVG images with imagemin.

// Utility related plugins.
const rename = require( 'gulp-rename' ); // Renames files E.g. style.css -> style.min.css.
const replace = require( 'gulp-string-replace' );
const lineec = require( 'gulp-line-ending-corrector' ); // Consistent Line Endings for non UNIX systems. Gulp Plugin for Line Ending Corrector (A utility that makes sure your files have consistent line endings).
const filter = require( 'gulp-filter' ); // Enables you to work on a subset of the original files by filtering them using a glob.
const sourcemaps = require( 'gulp-sourcemaps' ); // Maps code in a compressed file (E.g. style.css) back to it’s original position in a source file (E.g. structure.scss, which was later combined with other css files to generate style.css).
const notify = require( 'gulp-notify' ); // Sends message notification to you.
const browserSync = require( 'browser-sync' ).create(); // Reloads browser and injects CSS. Time-saving synchronized browser testing.
const wpPot = require( 'gulp-wp-pot' ); // For generating the .pot file.
const sort = require( 'gulp-sort' ); // Recommended to prevent unnecessary changes in pot-file.
const cache = require( 'gulp-cache' ); // Cache files in stream for later use.
const remember = require( 'gulp-remember' ); //  Adds all the files it has ever seen back into the stream.
const plumber = require( 'gulp-plumber' ); // Prevent pipe breaking caused by errors from gulp plugins.
const beep = require( 'beepbeep' );
const zip = require( 'gulp-zip' );
const del = require( 'del' );
const copy = require( 'gulp-copy' );
const checktxtdomain = require( 'gulp-checktextdomain' ); // Checks gettext function calls for missing or incorrect text domain.
const git = require('gulp-git');

// Google fonts task.
const jeditor = require( 'gulp-json-editor' );
const _ = require( 'lodash' );
const exec = require( 'gulp-exec' );
const download = require( 'gulp-download' );

// Deploy task.
const rsync = require( 'gulp-rsync' );
const buildDestination = `./build/${ pkgConfig.name }/`;

const newThemeVersion = semver.inc( pkgConfig.version, argv.releaseType );

/**
 * Custom Error Handler.
 *
 * @param Mixed err
 */
const errorHandler = r => {
    notify.onError( '\n\n❌  ===> ERROR: <%= error.message %>\n' )( r );
    beep();

    // this.emit('end');
};

/**
 * Task: `browser-sync`.
 *
 * Live Reloads, CSS injections, Localhost tunneling.
 * @link http://www.browsersync.io/docs/options/
 *
 * @param {Mixed} done Done.
 */
const browsersync = done => {
    browserSync.init({
        proxy: config.projectURL,
        open: config.browserAutoOpen,
        injectChanges: config.injectChanges,
        watchEvents: [ 'change', 'add', 'unlink', 'addDir', 'unlinkDir' ]
    });
    done();
};

// Helper function to allow browser reload with Gulp 4.
const reload = done => {
    browserSync.reload();
    done();
};

/**
 * Task: `styles`.
 *
 * Compiles Sass, Autoprefixes it and Minifies CSS.
 *
 * This task does the following:
 *    1. Gets the source scss file
 *    2. Compiles Sass to CSS
 *    3. Writes Sourcemaps for it
 *    4. Autoprefixes it and generates style.css
 *    5. Renames the CSS file with suffix .min.css
 *    6. Minifies the CSS file and generates style.min.css
 *    7. Injects CSS or reloads the browser via browserSync
 */
gulp.task( 'styles', ( done ) => {
    config.stylesheets.map( function( file ) {
        return gulp
            .src( file.src, { allowEmpty: true })
            .pipe( plumber( errorHandler ) )
            .pipe( sourcemaps.init() )
            .pipe(
                compass({
                    config_file: './config.rb',
                    css: 'assets/css',
                    sass: 'assets/sass'
                })
            )
            .on( 'error', sass.logError )
            .pipe( sourcemaps.write({ includeContent: false }) )
            .pipe( sourcemaps.init({ loadMaps: true }) )
            .pipe( autoprefixer( config.BROWSERS_LIST ) )
            .pipe( sourcemaps.write( './' ) )
            .pipe( lineec() ) // Consistent Line Endings for non UNIX systems.
            .pipe( gulp.dest( file.dest ) )
            .pipe( filter( '**/*.css' ) ) // Filtering stream to only css files.
            .pipe( mmq({ log: true }) ) // Merge Media Queries only for .min.css version.
            .pipe( browserSync.stream() ) // Reloads style.css if that is enqueued.
            .pipe( rename({ suffix: '.min' }) )
            .pipe( minifycss({ maxLineLen: 10 }) )
            .pipe( lineec() ) // Consistent Line Endings for non UNIX systems.
            .pipe( gulp.dest( file.dest ) )
            .pipe( filter( '**/*.css' ) ) // Filtering stream to only css files.
            .pipe( browserSync.stream() ) // Reloads style.min.css if that is enqueued.
            .pipe(
                notify({
                    message: `\n\n✅  ===> STYLES — Stylesheet ${file.src} completed!\n`,
                    onLast: true
                })
            );
    });
    done();
});

/**
 * Task: `stylesRTL`.
 *
 * Compiles Sass, Autoprefixes it, Generates RTL stylesheet, and Minifies CSS.
 *
 * This task does the following:
 *    1. Gets the source scss file
 *    2. Compiles Sass to CSS
 *    4. Autoprefixes it and generates style.css
 *    5. Renames the CSS file with suffix -rtl and generates style-rtl.css
 *    6. Writes Sourcemaps for style-rtl.css
 *    7. Renames the CSS files with suffix .min.css
 *    8. Minifies the CSS file and generates style-rtl.min.css
 *    9. Injects CSS or reloads the browser via browserSync
 */
gulp.task( 'stylesRTL', () => {
    return gulp
        .src( config.stylesheets[0].src, { allowEmpty: true })
        .pipe( plumber( errorHandler ) )
        .pipe( sourcemaps.init() )
        .pipe(
            compass({
                config_file: './config.rb',
                css: 'assets/css',
                sass: 'assets/sass'
            })
        )
        .on( 'error', sass.logError )
        .pipe( sourcemaps.write({ includeContent: false }) )
        .pipe( sourcemaps.init({ loadMaps: true }) )
        .pipe( autoprefixer( config.BROWSERS_LIST ) )
        .pipe( lineec() ) // Consistent Line Endings for non UNIX systems.
        .pipe( rename({ suffix: '-rtl' }) ) // Append "-rtl" to the filename.
        .pipe( rtlcss() ) // Convert to RTL.
        .pipe( sourcemaps.write( './' ) ) // Output sourcemap for style-rtl.css.
        .pipe( gulp.dest( config.stylesheets[0].dest ) )
        .pipe( filter( '**/*.css' ) ) // Filtering stream to only css files.
        .pipe( browserSync.stream() ) // Reloads style.css or style-rtl.css, if that is enqueued.
        .pipe( mmq({ log: true }) ) // Merge Media Queries only for .min.css version.
        .pipe( rename({ suffix: '.min' }) )
        .pipe( minifycss({ maxLineLen: 10 }) )
        .pipe( lineec() ) // Consistent Line Endings for non UNIX systems.
        .pipe( gulp.dest( config.stylesheets[0].dest ) )
        .pipe( filter( '**/*.css' ) ) // Filtering stream to only css files.
        .pipe( browserSync.stream() ) // Reloads style.css or style-rtl.css, if that is enqueued.
        .pipe(
            notify({
                message: '\n\n✅  ===> STYLES RTL — completed!\n',
                onLast: true
            })
        );
});

/**
 * Task: `vendorsJS`.
 *
 * Concatenate and uglify vendor JS scripts.
 *
 * This task does the following:
 *     1. Gets the source folder for JS vendor files
 *     2. Concatenates all the files and generates vendors.js
 *     3. Renames the JS file with suffix .min.js
 *     4. Uglifes/Minifies the JS file and generates vendors.min.js
 */
gulp.task( 'vendorsJS', () => {
    return gulp
        .src( config.jsVendorSRC, { since: gulp.lastRun( 'vendorsJS' ) }) // Only run on changed files.
        .pipe( plumber( errorHandler ) )
        .pipe(
            babel({
                presets: [
                    [
                        '@babel/preset-env', // Preset to compile your modern JS to ES5.
                        {
                            targets: { browsers: config.BROWSERS_LIST } // Target browser list to support.
                        }
                    ]
                ]
            })
        )
        .pipe( remember( config.jsVendorSRC ) ) // Bring all files back to stream.
        .pipe( concat( config.jsVendorFile + '.js' ) )
        .pipe( lineec() ) // Consistent Line Endings for non UNIX systems.
        .pipe( gulp.dest( config.jsVendorDestination ) )
        .pipe(
            rename({
                basename: config.jsVendorFile,
                suffix: '.min'
            })
        )
        .pipe( uglify() )
        .pipe( lineec() ) // Consistent Line Endings for non UNIX systems.
        .pipe( gulp.dest( config.jsVendorDestination ) )
        .pipe(
            notify({ message: '\n\n✅  ===> VENDOR JS — completed!\n', onLast: true })
        );
});

/**
 * Task: `customJS`.
 *
 * Concatenate and uglify custom JS scripts.
 *
 * This task does the following:
 *     1. Gets the source folder for JS custom files
 *     2. Concatenates all the files and generates custom.js
 *     3. Renames the JS file with suffix .min.js
 *     4. Uglifes/Minifies the JS file and generates custom.min.js
 */
gulp.task( 'customJS', () => {
    return gulp
        .src( config.jsCustomSRC, { since: gulp.lastRun( 'customJS' ) }) // Only run on changed files.
        .pipe( plumber( errorHandler ) )
        .pipe(
            babel({
                presets: [
                    [
                        '@babel/preset-env', // Preset to compile your modern JS to ES5.
                        {
                            targets: { browsers: config.BROWSERS_LIST } // Target browser list to support.
                        }
                    ]
                ]
            })
        )
        .pipe( remember( config.jsCustomSRC ) ) // Bring all files back to stream.
        .pipe( concat( config.jsCustomFile + '.js' ) )
        .pipe( lineec() ) // Consistent Line Endings for non UNIX systems.
        .pipe( gulp.dest( config.jsCustomDestination ) )
        .pipe(
            rename({
                basename: config.jsCustomFile,
                suffix: '.min'
            })
        )
        .pipe( uglify() )
        .pipe( lineec() ) // Consistent Line Endings for non UNIX systems.
        .pipe( gulp.dest( config.jsCustomDestination ) )
        .pipe(
            notify({ message: '\n\n✅  ===> CUSTOM JS — completed!\n', onLast: true })
        );
});

/**
 * Task: `images`.
 *
 * Minifies PNG, JPEG, GIF and SVG images.
 *
 * This task does the following:
 *     1. Gets the source of images raw folder
 *     2. Minifies PNG, JPEG, GIF and SVG images
 *     3. Generates and saves the optimized images
 *
 * This task will run only once, if you want to run it
 * again, do it with the command `gulp images`.
 *
 * Read the following to change these options.
 * @link https://github.com/sindresorhus/gulp-imagemin
 */
gulp.task( 'images', () => {
    return gulp
        .src( config.imgSRC )
        .pipe(
            cache(
                imagemin([
                    imagemin.gifsicle({ interlaced: true }),
                    imagemin.jpegtran({ progressive: true }),
                    imagemin.optipng({ optimizationLevel: 3 }), // 0-7 low-high.
                    imagemin.svgo({
                        plugins: [ { removeViewBox: true }, { cleanupIDs: false } ]
                    })
                ])
            )
        )
        .pipe( gulp.dest( config.imgDST ) )
        .pipe(
            notify({ message: '\n\n✅  ===> IMAGES — completed!\n', onLast: true })
        );
});

/**
 * Task: `clear-images-cache`.
 *
 * Deletes the images cache. By running the next "images" task,
 * each image will be regenerated.
 */
gulp.task( 'clearCache', function( done ) {
    return cache.clearAll( done );
});

/**
 * Task: `checktranslate`.
 *
 * Checks for missing translation strings.
 */
gulp.task( 'checktranslate', function() {
    return gulp.src( config.watchPhp ).pipe(
        checktxtdomain({
            text_domain: config.textDomain, // Specify allowed domain(s).
            keywords: [

                // List keyword specifications.
                '__:1,2d',
                '_e:1,2d',
                '_x:1,2c,3d',
                'esc_html__:1,2d',
                'esc_html_e:1,2d',
                'esc_html_x:1,2c,3d',
                'esc_attr__:1,2d',
                'esc_attr_e:1,2d',
                'esc_attr_x:1,2c,3d',
                '_ex:1,2c,3d',
                '_n:1,2,4d',
                '_nx:1,2,4c,5d',
                '_n_noop:1,2,3d',
                '_nx_noop:1,2,3c,4d'
            ]
        })
    );
});

/**
 * WP POT Translation File Generator.
 *
 * This task does the following:
 * 1. Gets the source of all the PHP files
 * 2. Sort files in stream by path or any custom sort comparator
 * 3. Applies wpPot with the variable set at the top of this file
 * 4. Generate a .pot file of i18n that can be used for l10n to build .mo file
 */
gulp.task( 'translate', () => {
    return gulp
        .src( config.watchPhp )
        .pipe( sort() )
        .pipe(
            wpPot({
                domain: config.textDomain,
                package: config.textDomain,
                bugReport: config.bugReport,
                lastTranslator: config.lastTranslator,
                team: config.team
            })
        )
        .pipe(
            gulp.dest( config.translationDestination + '/' + config.translationFile )
        )
        .pipe(
            notify({ message: '\n\n✅  ===> TRANSLATE — completed!\n', onLast: true })
        );
});

/**
 * Json massager for googlefonts tasks.
 */
gulp.task( 'jsonMassager', () => {
    return gulp
        .src( './googlefonts.json' )
        .pipe(
            jeditor( json => {
                var fonts = json.items,
                    newObj = {};

                _.forEach( fonts, function( data ) {
                    var label = data.family,
                        font = {
                            label: label,
                            variants: data.variants.sort(),
                            subsets: data.subsets.sort(),
                            category: data.category
                        };

                    newObj[label] = font;
                });

                return newObj;
            })
        )
        .pipe( gulp.dest( config.jsonFontsDST ) );
});

/**
 * Php massager for googlefonts tasks.
 */
gulp.task( 'phpMassager', () => {
    return gulp
        .src( config.jsonMassagerSRC )
        .pipe( exec( 'php -f google-fonts-array.php' ) )
        .pipe(
            notify({
                message: '\n\n✅  ===> FONTS — Google Fonts library updated!\n',
                onLast: true
            })
        );
});

/**
 * Complete Google fonts task.
 */
gulp.task(
    'googlefonts',
    gulp.series( function() {
        const api = config.fontsAPIKey;
        if ( api ) {
            const url = `https://www.googleapis.com/webfonts/v1/webfonts?sort=alpha&key=${api}`;

            return download( url )
                .pipe(
                    rename({
                        basename: 'googlefonts',
                        extname: '.json'
                    })
                )
                .pipe( gulp.dest( config.jsonFontsDST ) );
        }
        console.error('Ok, not building.'); // eslint-disable-line
        process.exit( 1 );
    }, gulp.parallel( 'jsonMassager', 'phpMassager' ) )
);

/**
 * Task: `clean`
 */
gulp.task( 'clean', function( done ) {
    return del([
        config.export.dest + `${pkgConfig.name}/`,
        config.export.dest + '*.zip'
    ]);
});

/**
 * Task: `copy`
 */
gulp.task( 'copy', function() {
    return gulp
        .src( config.export.src )
        .pipe( copy( config.export.dest + `${pkgConfig.name}/` ) );
});

/**
 * Task: `zip`.
 *
 * Bundles theme for distribution.
 */
gulp.task('zip', () => {
    pkgConfig = JSON.parse(fs.readFileSync(config.packageJSON));
    return gulp
        .src( config.export.dest + `${pkgConfig.name}/**`, { base: 'build' })
        .pipe(
            zip(
                pkgConfig.name + `-${pkgConfig.version}.zip`
            )
        )
        .pipe( gulp.dest( config.export.dest ) )
        .pipe(
            notify({
                message: '\n\n✅  ===> BUNDLE — Created in build folder!\n',
                onLast: true
            })
        );
});

/**
 * Task: `bundle`
 */
gulp.task( 'bundle', gulp.series( 'copy', 'zip' ) );

/**
 * Watch Tasks.
 *
 * Watches for file changes and runs specific tasks.
 */
gulp.task(
    'default',
    gulp.parallel(
        'clean',
        'styles',
        'vendorsJS',
        'customJS',
        'images',
        browsersync,
        () => {
            gulp.watch( config.watchPhp, reload ); // Reload on PHP file changes.
            gulp.watch( config.watchStyles, gulp.parallel( 'styles' ) ); // Reload on SCSS file changes.
            gulp.watch( config.watchJsVendor, gulp.series( 'vendorsJS', reload ) ); // Reload on vendorsJS file changes.
            gulp.watch( config.watchJsCustom, gulp.series( 'customJS', reload ) ); // Reload on customJS file changes.
            gulp.watch( config.imgSRC, gulp.series( 'images', reload ) ); // Reload on customJS file changes.
        }
    )
);

/**
 * Build task.
 */
gulp.task(
    'build',
    gulp.series(
        'clean',
        'images',
        'styles',
        'vendorsJS',
        'customJS',
        'checktranslate',
        'translate',
        'bundle',
        function( done ) {
            done();
        }
    )
);

/**
 * Log function.
 *
 * @param {string} type
 */
const log = type => {
    let changelog,
        newVersion = semver.inc( pkgConfig.version, type ),
        regex = new RegExp( '^## ' + newVersion, 'gm' ); // Match the version number (e.g., "= 1.2.3").

    // Get the new version
    changelog = fs.readFileSync( './changelog.txt' ).toString();
    if ( changelog.match( regex ) ) {
        console.log( 'v' + newVersion + ' changlelog entry found' );
        return true;
    } else {
        console.log(
            '\n\n❌  ===> Please enter a changelog entry for v' + newVersion
        );
        return false;
    }
};

/**
 * Tasks: `replaceStyleVersion` & `replaceThemeVersions`.
 *
 * Replaces version numbers according to release type.
 */
gulp.task( 'replaceStyleVersion', () => {
    return gulp
        .src( config.stylesheets[0].src )
        .pipe( replace( /^.*Version:.*$/m, `* Version: ${newThemeVersion}` ) )
        .pipe(
            notify({
                message: '\n\n✅  ===> REPLACE — Bumped Stylesheet version!\n',
                onLast: true
            })
        )
        .pipe( gulp.dest( `${config.stylesheets[0].dest}assets/sass/` ) );
});
gulp.task('replaceThemeVersions', (done) => {
    config.versionData.map(function(file) {
        switch (file.type) {
            case 'json':
                return gulp
                    .src(file.src)
                    .pipe(replace(/^.*"version":.*$/m, ` 	"version": "${newThemeVersion}",`))
                    .pipe(
                        notify({
                            message: `\n\n✅  ===> REPLACE — Bumped ${file.src} version!\n`,
                            onLast: true
                        })
                    )
                    .pipe(gulp.dest(file.dest));
                break;
            default:
                return gulp
                    .src(file.src)
                    .pipe(replace(/^define\( 'BLINK_VERSION'.*$/m, `define( 'BLINK_VERSION', '${newThemeVersion}' );`))
                    .pipe(
                        notify({
                            message: `\n\n✅  ===> REPLACE — Bumped ${file.src} version!\n`,
                            onLast: true
                        })
                    )
                    .pipe(gulp.dest(file.dest));
                break;
        }
    });
    done();
});
gulp.task('replace', gulp.series('replaceStyleVersion', 'replaceThemeVersions'));


/**
 * Task: Commit version
 *
 * Commits version for all the file changes
 */
gulp.task('trackAll', function(){
    return gulp.src('./')
        .pipe(git.add({args: '--all'}));
});
gulp.task('commitVersion', function(){
    return gulp.src('./')
        .pipe(git.commit(() => `Version ${newThemeVersion}`))
        .pipe(
            notify({
                message: '\n\n✅  ===> COMMIT SUCCESS — Committed files!\n',
                onLast: true
            })
        );
});


/**
 * Task: Tag version
 *
 * Tag version for the latest commit
 */
gulp.task('tagVersion', done => {
    git.tag(`${newThemeVersion}`, `Version ${newThemeVersion}`, function (err) {
        if (err) throw err;
    });
    console.log(
        `\n\n✅  ===> TAG SUCCESS — Tagged version ${newThemeVersion}!\n`
    );
    done();
});

/**
 * Task: Release
 *
 * Release task for creating releases.
 */
gulp.task( 'release', done => {
    if (
        'minor' !== argv.releaseType &&
        'major' !== argv.releaseType &&
        'patch' !== argv.releaseType
    ) {
        console.log(
            '\n\n❌  ===> Please specify the release type (e.g., "npm run release:patch")'
        );
    } else {

        // Check to make sure the log exists.
        const checkLog = log( argv.releaseType );
        if ( ! checkLog ) {
            return;
        }

        gulp.series(
            'clean',
            'replace',
            'styles',
            'checktranslate',
            'translate',
            'bundle',
            'trackAll',
            'commitVersion',
            'tagVersion'
        )();
    }
    done();
});

/**
 * Task: Deploy to demo server
 */
gulp.task( 'deploy', gulp.series(
    'build',
    function() {
        // Dirs and Files to sync
        const rsyncPaths = [ buildDestination ];

        // Default options for rsync
        const rsyncConf = {
            emptyDirectories: true,
            compress: true,
            archive: true,
            progress: true,
            root: './build/',
            exclude: ['node_modules', '.svn', '.git'],
            hostname: config.demo.hostname,
            username: config.demo.username,
            destination: `~/files/wp-content/themes/`,
        };

        // Use gulp-rsync to sync the files
        return gulp.src( rsyncPaths ).pipe( rsync( rsyncConf ) );
    }
) );