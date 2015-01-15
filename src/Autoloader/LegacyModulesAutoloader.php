<?php

namespace Kohana\Modules\Autoloader;

/**
 * Provides autoloading support of classes that follow Kohana's old class
 * naming conventions. This is included for compatibility purposes with
 * older modules.
 */
class LegacyModulesAutoloader extends AbstractModulesAutoloader implements Autoloader
{
    public function autoload($class_name)
    {
        // Transform the class name into a path
        $path = str_replace('_', '/', strtolower($class_name));

        // Get real file path
        $absolute_path = $this->cfs->getPath($this->src_path.'/'.$path);

        // Load the file if class exists
        if ($absolute_path) {
            $this->cfs->load($absolute_path);

            return true;
        }

        return false;
    }
}
