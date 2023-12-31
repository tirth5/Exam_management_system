<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb5abd7d164194b89bee293bf95e1b29a
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitb5abd7d164194b89bee293bf95e1b29a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb5abd7d164194b89bee293bf95e1b29a::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitb5abd7d164194b89bee293bf95e1b29a::$classMap;

        }, null, ClassLoader::class);
    }
}
