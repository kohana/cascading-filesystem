<?php

namespace Kohana\Modules\Autoloader;

use Kohana\Modules\CascadingFilesystem;

abstract class AbstractModules
{
    protected $cfs;
    protected $src_path = 'classes';
    
    public function __construct(CascadingFilesystem $cfs)
    {
        $this->cfs = $cfs;
    }
}