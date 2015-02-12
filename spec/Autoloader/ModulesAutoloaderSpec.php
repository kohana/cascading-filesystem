<?php

namespace spec\Kohana\CascadingFilesystem\Autoloader;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Kohana\CascadingFilesystem\Filesystem\CascadingFilesystem;

class ModulesAutoloaderSpec extends ObjectBehavior
{
    function let(CascadingFilesystem $cfs)
    {
        $this->beConstructedWith($cfs);
    }

    function it_prepends_the_classes_directory_and_appends_php_file_extension($cfs)
    {
        $path = 'classes/Foo.php';
        $real_path = '/real/'.$path;

        $cfs->getRealPath($path)->willReturn($real_path);

        $cfs->load($real_path)->shouldBeCalled();
        $this->autoload('Foo')->shouldReturn(true);
    }

    function it_translates_namespace_separators_to_directory_separators($cfs)
    {
        $path = 'classes/Foo/Bar/Baz.php';
        $real_path = '/real/'.$path;

        $cfs->getRealPath($path)->willReturn($real_path);

        $cfs->load($real_path)->shouldBeCalled();
        $this->autoload('Foo\Bar\Baz')->shouldReturn(true);
    }

    function it_ignores_a_prepended_namespace_separator($cfs)
    {
        $path = 'classes/Foo.php';
        $real_path = '/real/'.$path;

        $cfs->getRealPath($path)->willReturn($real_path);

        $cfs->load($real_path)->shouldBeCalled();
        $this->autoload('\Foo')->shouldReturn(true);
    }

    function it_translates_underscores_in_the_class_name_to_directory_separators($cfs)
    {
        $path = 'classes/Name_Space/Foo/Bar/Baz.php';
        $real_path = '/real/'.$path;

        $cfs->getRealPath($path)->willReturn($real_path);

        $cfs->load($real_path)->shouldBeCalled();
        $this->autoload('Name_Space\Foo_Bar_Baz')->shouldReturn(true);
    }

    function it_returns_false_when_failing_to_find_a_class($cfs)
    {
        $cfs->getRealPath('classes/Foo.php')->willReturn(false);

        $this->autoload('Foo')->shouldReturn(false);
    }
}
