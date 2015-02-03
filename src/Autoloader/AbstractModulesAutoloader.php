<?php

namespace Kohana\CascadingFilesystem\Autoloader;

use Kohana\CascadingFilesystem\Filesystem\CascadingFilesystem;

/**
 * Abstract modules autoloader.
 */
abstract class AbstractModulesAutoloader implements Autoloader
{
    /**
     * @var CascadingFilesystem The cascading filesystem object
     */
    protected $cfs;

    /**
     * @var string Path to class files (with trailing slash)
     */
    protected $src_path = 'classes/';

    /**
     * @var string Class file extension
     */
    protected $file_extension = '.php';

    /**
     * @param $cfs Cascading filesytem object
     */
    public function __construct(CascadingFilesystem $cfs)
    {
        $this->cfs = $cfs;
    }

    public function register()
    {
        return spl_autoload_register([$this, 'autoload']);
    }

    public function unregister()
    {
        return spl_autoload_unregister([$this, 'autoload']);
    }

    /**
     * Translates a class name's underscores to directory separators.
     *
     * @param string $class_name
     * @return string Translated class name
     */
    protected function translateUnderscores($class_name)
    {
        return str_replace('_', '/', $class_name);
    }

    /**
     * Finds and includes the class file from the cascading filesystem.
     *
     * @param string $file_path File path to class
     * @return bool Whether the class was successfully loaded
     */
    protected function loadClass($file_path)
    {
        // Get real file path
        $real_path = $this->cfs->getRealPath($file_path);

        // Load the file if class exists
        if ($real_path) {
            $this->cfs->load($real_path);

            return true;
        }

        return false;
    }
}
