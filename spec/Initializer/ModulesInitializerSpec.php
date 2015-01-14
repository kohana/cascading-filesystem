<?php

namespace spec\Kohana\Modules\Initializer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Kohana\Modules\Filesystem\CascadingFilesystem;

class ModulesInitializerSpec extends ObjectBehavior
{
    function let(CascadingFilesystem $cfs)
    {
        $this->beConstructedWith($cfs);
    }
    
    function it_initializes_the_modules($cfs)
    {
        $init_file = 'init.php';
        
        $paths = [
            'first/path',
            'second/path',
            'third/path',
        ];
        
        $cfs->getAllPaths($init_file)->willReturn($paths);
        
        $cfs->getAllPaths($init_file)->shouldBeCalled(1);
        
        foreach ($paths as $path) {
            $cfs->load($path)->shouldBecalled(1);
        }
        
        $this->initialize();
    }
}
