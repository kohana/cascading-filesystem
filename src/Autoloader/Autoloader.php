<?php

namespace Kohana\Modules\Autoloader;

interface Autoloader
{
    /**
     * Checks for a class file to load. This function should be enabled using
     * spl_autoload_register().
     * 
     * @param string $class_name Class name
     * @return bool Whether a class was loaded
     */
    public function autoload($class_name);
}