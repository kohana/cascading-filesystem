<?php

namespace Kohana\CascadingFilesystem\Filesystem;

use Doctrine\Common\Cache\Cache;

/**
 * The cascading filesystem.
 */
class CascadingFilesystem
{
    /**
     * @var Cache Cache object
     */
    protected $cache;

    /**
     * @var array Base paths which are merged together to form the CFS
     */
    protected $base_paths;

    /**
     * @param $cache Cache object
     * @param $base_paths Paths to directories, latter paths have precedence
     */
    public function __construct(Cache $cache, array $base_paths)
    {
        $this->cache = $cache;
        $this->setBasePaths($base_paths);
    }

    /**
     * Sets the cascading filesystem's base paths.
     *
     * @param $base_paths Paths to directories, latter paths have precedence
     * @throws \InvalidArgumentException If path is invalid
     * @return void
     */
    protected function setBasePaths(array $base_paths)
    {
        // Validate base paths
        foreach ($base_paths as $path) {
            if (! is_dir($path)) {
                throw new \InvalidArgumentException('Invalid base path: "'.$path.'"');
            }
        }

        $this->base_paths = $base_paths;
    }

    /**
     * Gets all base paths currently in use.
     *
     * @return array Base paths
     */
    public function getBasePaths()
    {
        return $this->base_paths;
    }

    /**
     * Finds a file in the cascading filesystem which matches the path and has
     * the highest precedence.
     *
     * @param string $relative_path Path to a file
     * @return string Real file path
     */
    public function getPath($relative_path)
    {
        // Generate cache key
        $cache_key = 'getPath_'.$relative_path;

        // Return cached result if it exists
        if (($cached_data = $this->cache->fetch($cache_key)) !== false) {
            return $cached_data;
        }

        // Search base paths for matching path
        foreach (array_reverse($this->base_paths) as $base_path) {
            $absolute_path = $base_path.$relative_path;

            // If file was found
            if (is_file($absolute_path)) {
                // Cache the file path
                $this->cache->save($cache_key, $absolute_path);

                return $absolute_path;
            }
        }

        return false;
    }

    /**
     * Finds all real file paths in the cascading filesystem which match the
     * path.
     *
     * @param string $relative_path Path to a file
     * @return array All real file paths found, ordered by precedence descending
     */
    public function getAllPaths($relative_path)
    {
        // Generate cache key
        $cache_key = 'getAllPaths_'.$relative_path;

        // Return cached result if it exists
        if (($cached_data = $this->cache->fetch($cache_key)) !== false) {
            return $cached_data;
        }

        // Search base paths for matching path
        $found = [];

        foreach (array_reverse($this->base_paths) as $base_path) {
            $absolute_path = $base_path.$relative_path;

            // Add to array if file exists
            if (is_file($absolute_path)) {
                $found[] = $absolute_path;
            }
        }

        // Cache the result
        $this->cache->save($cache_key, $found);

        return $found;
    }

    /**
     * Gets all files in the specified directory of the cascading filesystem.
     *
     * @param string $relative_dir_path Path to a directory
     * @return array Real paths of all files found with their CFS paths as keys, sorted alphabetically
     */
    public function listFiles($relative_dir_path, $hidden_files = false)
    {
        // Append directory seperatory if path doesn't end with one already
        if (substr($relative_dir_path, -1) !== '/') {
            $relative_dir_path .= '/';
        }

        // Find all files in the directory
        $found = [];

        foreach (array_reverse($this->base_paths) as $base_path) {
            try {
                $files = new \DirectoryIterator($base_path.$relative_dir_path);
            } catch (\UnexpectedValueException $e) {
                // Skip because directory doesn't exist
                continue;
            }

            // Iterate through the contents of the directory
            foreach ($files as $file) {
                $filename = $file->getFilename();

                // Skip if file is hidden or a UNIX backup file
                if (! $hidden_files && $this->isHiddenFile($filename)) {
                    continue;
                }

                $file_path = $relative_dir_path.$filename;

                // Add file to array if it hasn't already been found
                if (! isset($found[$file_path])) {
                    $found[$file_path] = $file->getPathName();
                }
            }
        }

        // Sort the results alphabetically
        ksort($found);

        return $found;
    }

    /**
     * Checks whether a file is hidden from its filename.
     *
     * @param string $filename Name of file
     * @return bool Whether it is a hidden file or not
     */
    protected function isHiddenFile($filename)
    {
        if (in_array($filename[0], ['.', '~'])) {
            return true;
        }

        return false;
    }

    /**
     * Loads a PHP file in isolation - wrapper for require_once function.
     *
     * @param string $file_path Path to PHP file
     * @return mixed Result of require_once function
     */
    public function load($file_path)
    {
        return require_once $file_path;
    }
}
