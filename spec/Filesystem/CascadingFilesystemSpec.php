<?php

namespace spec\Kohana\Modules\Filesystem;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Kohana\Modules\Cache;
use org\bovigo\vfs\vfsStream;

class CascadingFilesystemSpec extends ObjectBehavior
{
    function let(Cache\FileCache $cache)
    {
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
        $this->shouldThrow('\Exception');
        
        $this->beConstructedWith($cache, [
            vfsStream::url('root/dir1/'),
            vfsStream::url('root/nonexistent/path'),
        ]);
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
    
    function it_gets_a_path_with_highest_precedence()
    {
        $this->getPath('src/Window/Circular.php')->shouldReturn(
            vfsStream::url('root/dir1/src/Window/Circular.php')
        );
        
        $this->getPath('src/House.php')->shouldReturn(
            vfsStream::url('root/dir4/src/House.php')
        );
        
        $this->getPath('images/background.jpg')->shouldReturn(
            vfsStream::url('root/dir2/images/background.jpg')
        );
    }
    
    function it_returns_false_when_a_path_is_not_found()
    {
        $this->getPath('nonexistent/file.txt')->shouldReturn(false);
    }
    
    function it_gets_all_paths()
    {
        $this->getAllPaths('src/Door.php')->shouldReturn([
            vfsStream::url('root/dir4/src/Door.php'),
        ]);
        
        $this->getAllPaths('src/House.php')->shouldReturn([
            vfsStream::url('root/dir4/src/House.php'),
            vfsStream::url('root/dir1/src/House.php'),
        ]);
    }
    
    function it_returns_an_empty_array_when_no_paths_are_found()
    {
        $this->getAllPaths('nonexistent/file.md')->shouldReturn([]);
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
    
    function it_returns_an_empty_array_when_no_files_were_listed()
    {
        $this->listFiles(vfsStream::url('nonexistent/dir'))->shouldReturn([]);
    }
    
    function it_loads_a_php_file()
    {
        $this->load(vfsStream::url('root/dir4/src/House.php'));
    }
}
