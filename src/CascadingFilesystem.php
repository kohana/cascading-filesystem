<?php

namespace Kohana\Modules;

use Kohana\Modules\Cache\CacheInterface;

/**
 * The cascading filesystem.
 */
class CascadingFilesystem
{
    protected $cache;
    protected $base_paths;
    
    /**
     * @param $cache Cacher object
     * @param $base_paths Paths to directories, latter paths have precedence
     */
    public function __construct(CacheInterface $cache, array $base_paths)
    {
        $this->cache = $cache;
        $this->setBasePaths($base_paths);
    }
    
    /**
     * Sets the module's path.
     * 
     * @param string $base_paths Relative or absolute path to module's directory
     * @throws \Exception If path is invalid
     * @return void
     */
    protected function setBasePaths(array $base_paths)
    {
        // Validate base paths
        foreach ($base_paths as $path)
        {
            // If directory doesn't exist
            if (! is_dir($path)) {
                throw new \Exception('Invalid base path: "'.$path.'"');
            }
        }
        
        // Set base paths
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
     * @param string $relative_path Relative path to a file
     * @return string Absolute file path
     */
    public function getPath($relative_path)
    {
        // Generate cache key
        $cache_key = __METHOD__.'_'.$relative_path;
        
        // If result has been cached
        if (($cached_data = $this->cache->retrieve($cache_key)) !== null) {
            // Return cached result
            return $cached_data;
        }

        // Search base paths in reverse order
        foreach (array_reverse($this->base_paths) as $base_path) {
            // Form absolute path
            $absolute_path = $base_path.$relative_path;
            
            // If file exist
            if (is_file($absolute_path)) {
                // Add the path to the cache
                $this->cache->store($cache_key, $absolute_path);
                
                // Return absolute file path
                return $absolute_path;
            }
        }
        
        return false;
    }
    
    /**
     * Finds all files in the cascading filesystem which match the relative
     * path.
     * 
     * @param string $relative_path Relative path to a file
     * @return array All absolute file paths found, ordered by precendence descending
     */
    public function getAllPaths($relative_path)
    {
        // Generate cache key
        $cache_key = __METHOD__.'_'.$relative_path;

        // If result has been cached
        if (($cached_data = $this->cache->retrieve($cache_key)) !== null) {
            // Return cached result
            return $cached_data;
        }
        
        $found = [];

        // Search base paths in reverse order
        foreach (array_reverse($this->base_paths) as $base_path) {
            $absolute_path = $base_path.$relative_path;
            
            // If file exists
            if (is_file($absolute_path)) {
                // Add to array
                $found[] = $absolute_path;
            }
        }
        
        // Cache the result
        $this->cache->store($cache_key, $found);
        
        return $found;
    }
    
    /**
     * Gets all files in the specified directory at any location in the
     * cascading filesystem.
     *
     * @param string $relative_dir_path Relative path to a directory
     * @return array Absolute paths of all files found with relative paths as keys, sorted alphabetically
     */
    public function listFiles($relative_dir_path, $hidden_files = false)
    {
        // If path doesn't end with a seperatory
        if (substr($relative_dir_path, -1) !== DIRECTORY_SEPARATOR) {
            // Append one
            $relative_dir_path .= DIRECTORY_SEPARATOR;
        }
        
        // Create an array for the files
        $found = [];
        
        // Search base paths in reverse order
        foreach (array_reverse($this->base_paths) as $base_path) {
            try {
                $files = new \DirectoryIterator($base_path.$relative_dir_path);
            }
            catch (\UnexpectedValueException $e) {
                // Skip because directory doesn't exist
                continue;
            }
            
            // Iterate through the contents of the directory
            foreach ($files as $file) {
                // Get the file name
                $filename = $file->getFilename();

                // Skip if file is hidden or a UNIX backup file
                if (! $hidden_files && $this->isHiddenFile($filename)) {
                    continue;
                }
                
                $file_path = $relative_dir_path.$filename;
                
                // If path hasn't already been found
                if (! isset($found[$file_path])) {
                    // Add absolute file path to list
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
     * @return bool
     */
    protected function isHiddenFile($filename)
    {
        if (in_array($filename[0], ['.', '~'])) {
            return true;
        }
        
        return false;
    }

    /**
     * Loads a PHP file in isolation.
     * 
     * @param string $file_path Absolute path to PHP file
     * @return mixed
     */
    public function load($file_path)
    {
        return include $file_path;
    }
}
