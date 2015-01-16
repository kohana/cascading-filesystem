<?php

namespace Kohana\Modules\Autoloader;

/**
 * Provides autoloading support for Kohana module classes.
 */
class ModulesAutoloader extends AbstractModulesAutoloader implements Autoloader
{
    public function autoload($class_name)
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

        $file .= $this->translateUnderscores($class_name).'.php';

        // Get real file path
        $absolute_path = $this->cfs->getPath($this->src_path.$file);

        // Load the file if class exists
        if ($absolute_path) {
            $this->cfs->load($absolute_path);

            return true;
        }

        return false;
    }
}
