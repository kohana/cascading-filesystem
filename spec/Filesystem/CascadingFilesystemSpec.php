<?php

namespace spec\Kohana\CascadingFilesystem\Filesystem;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Doctrine\Common\Cache\ArrayCache;
use org\bovigo\vfs\vfsStream;
use PhpSpec\Exception\Example\SkippingException;

class CascadingFilesystemSpec extends ObjectBehavior
{
    function let(ArrayCache $cache)
    {
        if (defined('HHVM_VERSION')) {
            throw new SkippingException(
                'Skipped due to incomplete vfsStream support on HHVM - https://github.com/facebook/hhvm/issues/1971'
            );
        }

        vfsStream::setup('root', null, [
            'dir1' => [
                'src' => [
                    'Window' => [
                        'Circular.php' => '',
                    ],
                    '.backups' => [],
                    'House.php' => '',
                    '~tmp' => '',
                ],
                'images' => [
                    'background.jpg' => '',
                ],
            ],
            'dir2' => [
                'images' => [
                    'background.jpg' => '',
                ],
                'empty' => [],
            ],
            'dir3' => [],
            'dir4' => [
                'src' => [
                    'House.php' => '',
                    'Door.php' => '',
                    '.htaccess' => '',
                ]
            ],
        ]);

        $this->beConstructedWith($cache, [
            vfsStream::url('root/dir1/'),
            vfsStream::url('root/dir2/'),
            vfsStream::url('root/dir3/'),
            vfsStream::url('root/dir4/'),
        ]);
    }

    function it_throws_exception_when_a_base_path_does_not_exist($cache)
    {
        $base_paths = [vfsStream::url('root/nonexistent/path')];

        $exception = new \InvalidArgumentException(
            'Invalid base path: "'.$base_paths[0].'"'
        );

        $this->shouldThrow($exception)
            ->during('__construct', [$cache, $base_paths]);
    }

    function it_exposes_base_paths($cache)
    {
        $base_paths = [
            vfsStream::url('root/dir1/'),
            vfsStream::url('root/dir2/'),
        ];

        $this->beConstructedWith($cache, $base_paths);

        $this->getBasePaths()->shouldEqual($base_paths);
    }

    function it_gets_a_real_file_path($cache)
    {
        $path = 'src/House.php';
        $cache_id = 'getRealPath_'.$path;
        $real_path = vfsStream::url('root/dir4/src/House.php');

        $cache->fetch($cache_id)->willReturn(false);
        $cache->save($cache_id, $real_path)->willReturn(true);

        $this->getRealPath($path)->shouldReturn($real_path);
    }

    function it_returns_false_when_a_real_file_path_is_not_found($cache)
    {
        $path = 'nonexistent/file.txt';
        $cache_id = 'getRealPath_'.$path;

        $cache->fetch($cache_id)->willReturn(false);

        $this->getRealPath($path)->shouldReturn(false);
    }

    function it_gets_all_real_file_paths($cache)
    {
        $path = 'src/House.php';
        $cache_id = 'getAllRealPaths_'.$path;
        $real_paths = [
            vfsStream::url('root/dir4/src/House.php'),
            vfsStream::url('root/dir1/src/House.php'),
        ];

        $cache->fetch($cache_id)->willReturn(false);
        $cache->save($cache_id, $real_paths)->willReturn(true);

        $this->getAllRealPaths($path)->shouldReturn($real_paths);
    }

    function it_returns_an_empty_array_when_no_real_file_paths_are_found($cache)
    {
        $path = 'nonexistent/file.md';
        $cache_id = 'getAllRealPaths_'.$path;
        $real_paths = [];

        $cache->fetch($cache_id)->willReturn(false);
        $cache->save($cache_id, $real_paths)->willReturn(true);

        $this->getAllRealPaths($path)->shouldReturn($real_paths);
    }

    function it_lists_files()
    {
        $this->listFiles('src')->shouldReturn([
            'src/Door.php' => vfsStream::url('root/dir4/src/Door.php'),
            'src/House.php' => vfsStream::url('root/dir4/src/House.php'),
            'src/Window' => vfsStream::url('root/dir1/src/Window'),
        ]);
    }

    function it_lists_hidden_files()
    {
        $files = $this->listFiles('src', true);

        $files->shouldContain(vfsStream::url('root/dir1/src/.backups'));
        $files->shouldContain(vfsStream::url('root/dir1/src/~tmp'));
        $files->shouldContain(vfsStream::url('root/dir4/src/.htaccess'));
    }

    function it_returns_an_empty_array_when_no_files_are_listed()
    {
        $this->listFiles('empty')->shouldReturn([]);
    }

    function it_loads_a_php_file()
    {
        $this->load(vfsStream::url('root/dir4/src/House.php'));
    }

}
