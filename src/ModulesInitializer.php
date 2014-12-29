<?php

namespace Kohana\Modules;

class ModulesInitializer
{
    protected $cfs;
    protected $init_file_path = 'init.php';
    
    /**
     * @param $cfs The cascading filesystem
     */
    public function __construct(CascadingFilesystem $cfs)
    {
        $this->cfs = $cfs;
    }
    
    /**
     * Initializes all modules which have an init file.
     * 
     * @return void
     */
    public function initialize()
    {
        // Get all init file locations
        $init_file_paths = $this->cfs->getAllPaths($this->init_file_path);
        
        // Include init files
        foreach ($init_file_paths as $path) {
            $this->cfs->load($path);
        }
    }
}
