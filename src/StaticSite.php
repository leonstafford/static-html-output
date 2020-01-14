<?php
/*
    StaticSite

    The resulting output of crawling the WordPress site

    Site URLs are all made absolute for easier rewriting during deployment
*/

namespace WP2Static;

class StaticSite {

    public $path;

    /**
     * StaticSite constructor
     *
     * @param string $path path to static site directory
     */
    public function __construct(string $path) {
        $this->path = $this->create_directory( $path );
    }

    /**
     * Add crawled resource to static site
     *
     */
    public function add(string $path, string $contents) {
        // simple file save, SiteCrawler holds logic for what/where to save
        // Crawler has already processed links, etc
        $full_path = "$this->path/$path";

        $directory = dirname( $full_path );

        // mkdir recursively
        if ( ! is_dir( $directory ) ) {
            mkdir( $directory, 0755, true );
        }

        file_put_contents( $full_path, $contents );
    }

    /**
     * Create  dir
     *
     * @param string $path static site directory
     * @throws WP2StaticException
     */
    private function create_directory( $path ) : string {
        if ( is_dir( $path ) ) {
            return $path;
        }

        if ( ! mkdir( $path ) ) {
            $err = "Couldn't create StaticSite directory:" . $path;
            WsLog::l( $err );
            throw new WP2StaticException( $err );
        }

        return $path;
    }

    /**
     * Delete StaticSite files
     *
     */
    public function delete() {
        error_log('deleting static site files');

        if ( is_dir( $this->path ) ) {
            FilesHelper::delete_dir_with_files( $this->path );

            error_log('truncating CrawlCache');
            CrawlCache::clear();
        }
    }
}
