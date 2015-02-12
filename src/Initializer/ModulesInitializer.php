<?php

namespace Kohana\CascadingFilesystem\Initializer;

use Kohana\CascadingFilesystem\Filesystem\CascadingFilesystem;

/**
 * Modules Initializer.
 */
class ModulesInitializer implements Initializer
{
    /**
     * @var CascadingFilesystem Cascading filesystem object
     */
    protected $cfs;

    /**
     * @var string Path to module's initialization file.
     */
    protected $init_file_path = 'init.php';

    /**
     * @param $cfs Cascading filesystem object
     */
    public function __construct(CascadingFilesystem $cfs)
    {
        $this->cfs = $cfs;
    }

    /**
     * Initializes all modules which have an initialization file.
     */
    public function initialize()
    {
        // Get all initialization file locations
        $init_file_paths = $this->cfs->getAllRealPaths($this->init_file_path);

        // Load initialization files
        foreach ($init_file_paths as $path) {
            $this->cfs->load($path);
        }
    }
}
