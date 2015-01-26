<?php

namespace spec\Kohana\CascadingFilesystem\Initializer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Kohana\CascadingFilesystem\Filesystem\CascadingFilesystem;

class ModulesInitializerSpec extends ObjectBehavior
{
    function let(CascadingFilesystem $cfs)
    {
        $this->beConstructedWith($cfs);
    }

    function it_initializes_the_modules($cfs)
    {
        $paths = [
            'first/path',
            'second/path',
            'third/path',
        ];

        $cfs->getAllPaths('init.php')->willReturn($paths);

        foreach ($paths as $path) {
            $cfs->load($path)->shouldBecalled(1);
        }

        $this->initialize();
    }
}
