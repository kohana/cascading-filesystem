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
     * @var string Path to class files
     */
    protected $src_path = 'classes';

    /**
     * @param $cfs Cascading filesytem object
     */
    public function __construct(CascadingFilesystem $cfs)
    {
        $this->cfs = $cfs;
    }
}
