<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit86b8825329c41aa091713a307aafd599
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'MyShortlinkPlugin\\includes\\' => 27,
            'MyShortlinkPlugin\\Admin\\' => 24,
            'MyShortlinkPlugin\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'MyShortlinkPlugin\\includes\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes',
        ),
        'MyShortlinkPlugin\\Admin\\' => 
        array (
            0 => __DIR__ . '/../..' . '/admin',
        ),
        'MyShortlinkPlugin\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit86b8825329c41aa091713a307aafd599::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit86b8825329c41aa091713a307aafd599::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit86b8825329c41aa091713a307aafd599::$classMap;

        }, null, ClassLoader::class);
    }
}
