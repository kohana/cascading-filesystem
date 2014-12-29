<?php

namespace spec\Kohana\Modules\Cache;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use org\bovigo\vfs\vfsStream;

class FileSpec extends ObjectBehavior
{
    function let()
    {
        vfsStream::setup('root', null, [
            'cache' => [
                '56' => ['569097e78e8356fda3b6942670a3884d4bbe4b9e.txt' => 's:3:"foo";'],
                '8b' => ['8b75d19a80352bbc463403e29afcac32ef4b5a4c.txt' => 'i:1337;'],
                '39' => ['39ed573d326722860e603b86faa5f402dac9d646.txt' => 'a:2:{i:0;s:3:"foo";i:1;i:1337;}'],
            ],
        ]);
        
        $this->beConstructedWith(vfsStream::url('root/cache/'));
    }
    
    function it_stores_a_string()
    {
        $this->store('store_one', 'foo');
        
        // TODO: Check cache file was created
        // cache/e4/e417c1bafdf639d7660efc3e25157c822c1febf6.txt
    }
    
    function it_stores_an_int()
    {
        $this->store('store_two', 1337);
        
        // TODO: Check cache file was created
        // cache/52/52818236dda57c0c0f5beeb59159beb67610821b.txt
    }
    
    function it_stores_an_array()
    {
        $this->store('store_three', ['foo', 1337]);
        
        // TODO: Check cache file was created
        // cache/f2/f2aa27089ee23d5b526638aeb859454e32b03d67.txt
    }
    
    function it_retrieves_a_string()
    {
        $this->retrieve('retrieve_one')->shouldReturn('foo');
    }
    
    function it_retrieves_an_int()
    {
        $this->retrieve('retrieve_two')->shouldReturn(1337);
    }
    
    function it_retrieves_an_array()
    {
        $this->retrieve('retrieve_three')->shouldReturn(['foo', 1337]);
    }
    
    function it_returns_null_when_cache_data_does_not_exist()
    {
        $this->retrieve('nonexistent')->shouldReturn(null);
    }
}
