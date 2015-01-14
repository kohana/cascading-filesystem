Installation
============

Add this package as a dependency in your project's composer.json configuration:

```json
require: {
    "kohana/modules": "~1.0"
}
```

Now run `composer install` in the terminal at the root of your project.

You must then enable composer autoloading (if you haven't already) by requiring the autoload file:

```php
// Enable composer autoloading
require 'vendor/autoload.php';
```

For more information on composer please refer to its [documentation](https://getcomposer.org/doc/).

Cascading Filesystem
====================

Concept
-------

The cascading filesystem (often abbreviated to CFS) is a collection of separate directory paths. The contents of these directories are virtually merged together creating the illusion that all of the the files are in the same directory. Files that are in latter defined directories take precedence over (and replace) files of the same location. This makes it is possible to overload any file simply by placing it in the same location of a latter directory.

For example, let's say we had these three directories in the cascading filesystem:

```
directory 1
|-- cat.png
|-- code
|    |-- Foo.php
|    |-- Bar.php
|    +-- Baz.php
+-- music
     +-- White & Nerdy.mp3

directory 2
|-- dog.bmp
|-- mouse.jpg
+-- code
     +-- Foo.php

directory 3
|-- cat.png
+-- code
     |-- Foo.php
     +-- Baz.php
```

This would be the result when the directory's contents are virtually merged together:

```
root
|-- cat.png (From directory 3)
|-- dog.bmp (From directory 2)
|-- mouse.jpg (From directory 2)
|-- code
|    |-- Foo.php (From directory 3)
|    |-- Bar.php (From directory 1)
|    +-- Baz.php (From directory 3)
+-- music
     +-- White & Nerdy.mp3 (From directory 1)
```

Instantiation
-------------

```php
// Instantiate cache
$cache = new Kohana\Modules\Cache\FileCache('/path/to/cache/dir');

// Instantiate CFS
$cfs = Kohana\Modules\CascadingFilesystem($cache, [
    'directory/path/one',
    'directory/path/two',
    'directory/path/three',
]);
```

Finding Files
-------------

This finds the file path with the highest precedence in the cascading filesystem and returns its absolute location:

```php
// Get absolute path
$path = $cfs->getPath('code/foo.php');
```

You can also retrieve every location that the file exists, this would return an array of absolute file paths:

```php
// Get all absolute paths
$paths = $cfs->getAllPaths('code/foo.php');
```

This lists the files of a directory in the merged CFS:

```php
// List directory contents
$files = $cfs->listFiles('code');
```

Kohana Modules
==============

Kohana modules are really just an extended concept of a directory in the cascading filesystem. The additional functionality a module gains is that it can be initialized and classes within it can be autoloaded. Modules provide an easy way to organize your code and make any part more shareable or transportable across different applications.

Initialization
--------------

To initialize all of the enabled modules in the cascading filesystem:

```php
// Initialize all modules
(new Kohana\Modules\Initializer\ModulesInitializer($cfs))->initialize();
```

This should be done before you start using the modules as they may have prerequisites which are fulfilled by initialization.

Autoloading
-----------

To enable the autoloading of classes inside of modules you must first register the autoloader class using [spl_autoload_register](http://php.net/spl_autoload_register):

```php
// Enable kohana module autoloader
spl_autoload_register([new Kohana\Modules\Autoloader\ModulesAutoloader($cfs), 'load']);
```

There is also a backwards compatibility autoloader for module classes which still use the old file naming convention (lowercase):

```php
// Enable legacy kohana module autoloader
spl_autoload_register([new Kohana\Modules\Autoloader\LegacyModulesAutoloader($cfs), 'load']);
```

Now the autoloader is registered you can go ahead and use any classes as if they're already included.

Creating a Module
-----------------

To create your own Kohana module you must follow this specification. Other than following these rules, you may add as many custom files and folders to your module as you like.

### Init.php

If you would like your module to be initialized you must add an `init.php` file at the root. When the user initializes all of their modules, this init file will be included into the PHP script. This is the ideal place to execute any PHP code necessary for the module to function.

### Classes

All classes that need to be autoloaded must follow this specification:

 1. All classes must be placed in a `classes/` directory at the root of the module.
 2. All classes must follow the [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md) standard.
  - All class names must be capitalized, e.g. `Foo_Bar_Baz`, `Teapot`, `Hello_World`.
  - The class's filename and location must match the class's name exactly (including case). For example, the class `Foo_Bar_Baz` must be located at `classes/Foo/Bar/Baz.php`.
  - Any underscore characters in the class name are converted to slashes.

If your classes do not follow this convention, they cannot be autoloaded by this package. You would have to manually include your classes or create your own autoloader.

Where to Find Modules
---------------------

 - [kohana-modules](http://www.kohana-modules.com) - A website dedicated to indexing all Kohana modules with an excellent search and refine system; created by Andrew Hutchings.

 - [kohana-universe](http://github.com/kolanos/kohana-universe/tree/master/modules/) - A fairly comprehensive list of modules that are available on GitHub created by Kolanos. To get your module listed there, send him a message via GitHub.