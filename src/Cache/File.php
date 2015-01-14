<?php

namespace Kohana\Modules\Cache;

/**
 * File based cache.
 */
class File implements Cache
{
	protected $base_path;
	protected $cache_life;

    /**
     * @param string $base_path Path to cache directory
     * @param int $cache_life Lifespan of the data
     */
	public function __construct($base_path, $cache_life = 60)
	{
		$this->base_path = $base_path;
		$this->cache_life = $cache_life;
	}
    
	public function store($key, $data)
	{
		$filename = $this->generateFilename($key);
		$dir = $this->generateDirectory($filename);

        // If directory doesn't already exist
		if (! is_dir($dir)) {
            // Create directory
			$this->createDirectory($dir);
		}

		// Serialize data into a storable string
		$serialized_data = serialize($data);

		return (bool) file_put_contents($dir.$filename, $serialized_data/*, LOCK_EX*/);
	}
    
    public function retrieve($key, $lifetime = null)
	{
        $filename = $this->generateFilename($key);
		$file_path = $this->generateDirectory($filename).$filename;
        
        if ($lifetime == null) {
			$lifetime = $this->cache_life;
		}
        
        // If cache file exists
		if (is_file($file_path)) {
            // If file hasn't expired
            if ((time() - filemtime($file_path)) < $lifetime) {
                // Return the cache
                try {
                    return unserialize(file_get_contents($file_path));
                } catch (\Exception $e) {
                    // Cache is corrupt, let return happen normally.
                }
            } else {
                try {
                    // Cache has expired
                    unlink($file_path);
                } catch (\Exception $e) {
                    // Cache has mostly likely already been deleted,
                    // let return happen normally.
                }
            }
        }
        
        return null;
	}
    
	protected function generateFilename($name)
	{
		return sha1($name).'.txt';
	}

	protected function generateDirectory($filename)
	{
		return $this->base_path.$filename[0].$filename[1].'/';
	}
    
    protected function createDirectory($path)
    {
        // Create directory
        mkdir($path, 0777, TRUE);

        // Set permissions (must be manually set to fix umask issues)
        chmod($path, 0777);
    }
}
