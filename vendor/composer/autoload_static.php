<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit2cdcb7dc8a809a18a2b60e9f35d65683
{
    public static $files = array (
        'cfec4c0d7bb7eee418b96027ea06c7fb' => __DIR__ . '/../..' . '/app/Config/app.php',
    );

    public static $prefixLengthsPsr4 = array (
        'a' => 
        array (
            'app\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'app\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit2cdcb7dc8a809a18a2b60e9f35d65683::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit2cdcb7dc8a809a18a2b60e9f35d65683::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit2cdcb7dc8a809a18a2b60e9f35d65683::$classMap;

        }, null, ClassLoader::class);
    }
}
