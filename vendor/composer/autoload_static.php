<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf6e7840ac847d2e3348434f05e6025b2
{
    public static $prefixLengthsPsr4 = array (
        'H' => 
        array (
            'Hadi\\' => 5,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Hadi\\' => 
        array (
            0 => __DIR__ . '/..' . '/hadi/database/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf6e7840ac847d2e3348434f05e6025b2::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf6e7840ac847d2e3348434f05e6025b2::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitf6e7840ac847d2e3348434f05e6025b2::$classMap;

        }, null, ClassLoader::class);
    }
}