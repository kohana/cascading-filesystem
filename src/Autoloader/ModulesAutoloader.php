<?php

namespace Kohana\Modules\Autoloader;

/**
 * Provides autoloading support of classes that follow Kohana's [class
 * naming conventions](kohana/conventions#class-names-and-file-location).
 * See [Loading Classes](kohana/autoloading) for more information.
 */
class ModulesAutoloader extends AbstractModulesAutoloader implements Autoloader
{
    public function load($class_name)
    {
        // Transform the class name according to PSR-0
        $class_name     = ltrim($class_name, '\\');
        $file      = '';
        $namespace = '';
        
        if ($last_namespace_position = strripos($class_name, '\\')) {
            $namespace = substr($class_name, 0, $last_namespace_position);
            $class_name = substr($class_name, $last_namespace_position + 1);
            $file = str_replace('\\', '/', $namespace).'/';
        }
        
        $file .= str_replace('_', '/', $class_name);
        
        $absolute_path = $this->cfs->getPath($this->src_path.DIRECTORY_SEPARATOR.$file);
        
        // If class exist
        if ($absolute_path) {
            // Load the class file
            $this->cfs->load($absolute_path);
            
            return true;
        }
        
        return false;
    }
}
