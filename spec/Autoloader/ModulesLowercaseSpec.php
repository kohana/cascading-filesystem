<?php

namespace spec\Kohana\Modules\Autoloader;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Kohana\Modules\CascadingFilesystem;

class ModulesLowercaseSpec extends ObjectBehavior
{
    function let(CascadingFilesystem $cfs)
    {
        $this->beConstructedWith($cfs);
    }
    
    function it_loads_a_class($cfs)
    {
        $absolute_path = '/absolute/classes/foo/bar.php';
        
        $cfs->getPath('classes/foo/bar')->willReturn($absolute_path);
        
        $cfs->load($absolute_path)->shouldBeCalled(1);
        
        $this->load('foo_bar')->shouldReturn(true);
    }
    
    function it_returns_false_when_failing_to_load_a_class($cfs)
    {
        $cfs->getPath('classes/foo/bar')->willReturn(false);
        
        $this->load('foo_bar')->shouldReturn(false);
    }
}
