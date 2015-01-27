<?php

namespace spec\Kohana\CascadingFilesystem\Autoloader;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Kohana\CascadingFilesystem\Filesystem\CascadingFilesystem;

class LegacyModulesAutoloaderSpec extends ObjectBehavior
{
    function let(CascadingFilesystem $cfs)
    {
        $this->beConstructedWith($cfs);
    }

    function it_prepends_the_classes_directory_and_appends_php_file_extension($cfs)
    {
        $path = 'classes/foo.php';
        $real_path = '/real/'.$path;

        $cfs->getPath($path)->willReturn($real_path);

        $cfs->load($real_path)->shouldBeCalled();
        $this->autoload('foo')->shouldReturn(true);
    }

    function it_translates_underscores_to_directory_separators($cfs)
    {
        $path = 'classes/foo/bar/baz.php';
        $real_path = '/real/'.$path;

        $cfs->getPath($path)->willReturn($real_path);

        $cfs->load($real_path)->shouldBeCalled();
        $this->autoload('foo_bar_baz')->shouldReturn(true);
    }

    function it_returns_false_when_failing_to_load_a_class($cfs)
    {
        $cfs->getPath('classes/foo.php')->willReturn(false);

        $this->autoload('foo')->shouldReturn(false);
    }
}
