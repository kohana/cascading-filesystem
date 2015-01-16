<?php

namespace Kohana\Modules\Autoloader;

use Kohana\Modules\Filesystem\CascadingFilesystem;

/**
 * Abstract modules autoloader.
 */
abstract class AbstractModulesAutoloader
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
     * @param $cfs Cascading filesytem object
     */
    public function __construct(CascadingFilesystem $cfs)
    {
        $this->cfs = $cfs;
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
}
