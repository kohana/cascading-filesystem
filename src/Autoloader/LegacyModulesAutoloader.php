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
        $path = strtolower($this->translateUnderscores($class_name)).$this->file_extension;

        return $this->loadClass($this->src_path.$path);
    }
}
