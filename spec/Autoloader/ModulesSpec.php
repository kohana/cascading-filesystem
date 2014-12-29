<?php

namespace spec\Kohana\Modules\Autoloader;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Kohana\Modules\CascadingFilesystem;

class ModulesSpec extends ObjectBehavior
{
    function let(CascadingFilesystem $cfs)
    {
        $this->beConstructedWith($cfs);
    }
    
    function it_loads_a_class($cfs)
    {
        $absolute_path = '/absolute/classes/Foo/Bar.php';
        
        $cfs->getPath('classes/Foo/Bar')->willReturn($absolute_path);
        
        $cfs->load($absolute_path)->shouldBeCalled(1);
        
        $this->load('Foo_Bar')->shouldReturn(true);
    }
    
    function it_returns_false_when_failing_to_load_a_class($cfs)
    {
        $cfs->getPath('classes/Foo/Bar')->willReturn(false);
        
        $this->load('Foo_Bar')->shouldReturn(false);
    }
}
