<?php

namespace Kohana\Modules\Autoloader;

/**
 * Provides autoloading support of classes that follow Kohana's old class
 * naming conventions. This is included for compatibility purposes with
 * older modules.
 */
class ModulesLowercase extends AbstractModules implements Autoloader
{
    public function load($class_name)
    {
        // Transform the class name into a path
        $path = str_replace('_', '/', strtolower($class_name));
        
        // Get absolute path
        $absolute_path = $this->cfs->getPath($this->src_path.'/'.$path);
        
        // If class was found in modules
        if ($absolute_path) {
            // Load the class file
            $this->cfs->load($absolute_path);
            
            return true;
        }
        
        return false;
    }
}
