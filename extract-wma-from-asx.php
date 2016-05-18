<?php
/**
 * Extract WMA files from ASX files
 */
set_time_limit( 0 );
$files = scandir( __DIR__ );
$urls = [];

foreach ( $files as $file ) {
    if ( false === strpos( $file, '.asx' ) ) {
        continue;
    }

    $contents = file_get_contents( __DIR__ . '/' . $file );

    if ( empty( $contents ) ) {
        continue;
    } else {
        echo "Working on {$file}... \n";
    }

    preg_match_all('#[-a-zA-Z0-9@:%_\+.~\#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~\#?&//=]*)?#si', $contents, $matches);
    if ( empty( $matches[0] ) ) {
        continue;
    }

    foreach ( $matches[0] as $url ) {
        if ( false === strpos( $url, '.wma' ) ) {
            continue;
        } else {
            $urls[] = $url;
        }
    }
}

echo "\n\n==============================\n";
echo 'Found ' . count( $urls ) . " URLs \n";
echo "==============================\n\n";
$download_dir = __DIR__ . '/downloads';

// Make a download directory
if ( ! is_dir( $download_dir ) ) {
    mkdir( $download_dir );
}

foreach ( $urls as $url ) {
    $path = parse_url( $url, PHP_URL_PATH );
    $explode = array_filter( explode( '/', $path ) );
    $name = array_pop( $explode );

    if ( 'intro.wma' === $name ) {
        echo "Skipping a intro.wma... \n";
        continue;
    }

    echo "Downloading $url...\n";

    if ( file_exists( $download_dir . '/' . $name ) ) {
        echo "Skipping $name since it is already downloaded! \n";
        continue;
    } else {
        $command = sprintf(
            'cd "%s" && wget %s',
            $download_dir,
            $url
        );
        
        // Using `shell_exec()` in place of `file_put_contents()` due to performance; either works.
        // file_put_contents( $download_dir . '/' . $name, fopen( $url, 'r' ) );
        echo shell_exec( $command ) . PHP_EOL;
    }
    
    echo "DONE \n";
}

echo "\n\n==============================\n";
echo "Finished downloading! \n";
echo "==============================\n\n";
