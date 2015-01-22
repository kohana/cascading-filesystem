<?php

namespace Kohana\Modules\Autoloader;

/**
 * Autoloader interface.
 */
interface Autoloader
{
    /**
     * Attempts to autoload a class file from its name. This method is
     * intended for enablement using spl_autoload_register().
     *
     * @param string $class_name Class name
     * @return bool Whether a class was loaded
     */
    public function autoload($class_name);

    /**
     * Registers the autoload method.
     *
     * @return bool Success
     */
    public function register();

    /**
     * Unregisters the autoload method.
     *
     * @return bool Success
     */
    public function unregister();
}
