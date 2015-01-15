<?php

namespace Kohana\Modules\Autoloader;

use Kohana\Modules\Filesystem\CascadingFilesystem;

abstract class AbstractModulesAutoloader
{
    protected $cfs;
    protected $src_path = 'classes';
    
    public function __construct(CascadingFilesystem $cfs)
    {
        $this->cfs = $cfs;
    }
}
