<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit49b77a36225fab148e4efc4ef3ea1528
{
    public static $files = array (
        '7b11c4dc42b3b3023073cb14e519683c' => __DIR__ . '/..' . '/ralouphie/getallheaders/src/getallheaders.php',
        'c964ee0ededf28c96ebd9db5099ef910' => __DIR__ . '/..' . '/guzzlehttp/promises/src/functions_include.php',
        'a0edc8309cc5e1d60e3047b5df6b7052' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/functions_include.php',
        '320cde22f66dd4f5d3fd621d3e88b98f' => __DIR__ . '/..' . '/symfony/polyfill-ctype/bootstrap.php',
        '37a3dc5111fe8f707ab4c132ef1dbc62' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/functions_include.php',
        '874c2e99434288a0f58e8d945f6d28f0' => __DIR__ . '/..' . '/timersys/geotargetingwp/src/functions_include.php',
        '907ed27fb6572f2a5d69b507f4c0d25f' => __DIR__ . '/..' . '/timersys/geot-functions/src/functions_include.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Polyfill\\Ctype\\' => 23,
        ),
        'P' => 
        array (
            'Psr\\Http\\Message\\' => 17,
        ),
        'M' => 
        array (
            'MaxMind\\Db\\' => 11,
        ),
        'J' => 
        array (
            'Jaybizzle\\CrawlerDetect\\' => 24,
        ),
        'G' => 
        array (
            'GuzzleHttp\\Psr7\\' => 16,
            'GuzzleHttp\\Promise\\' => 19,
            'GuzzleHttp\\' => 11,
            'GeotWP\\' => 7,
            'GeotFunctions\\' => 14,
        ),
        'E' => 
        array (
            'EAMann\\Sessionz\\' => 16,
        ),
        'D' => 
        array (
            'Dotenv\\' => 7,
            'Defuse\\Crypto\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Polyfill\\Ctype\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-ctype',
        ),
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-message/src',
        ),
        'MaxMind\\Db\\' => 
        array (
            0 => __DIR__ . '/..' . '/maxmind-db/reader/src/MaxMind/Db',
        ),
        'Jaybizzle\\CrawlerDetect\\' => 
        array (
            0 => __DIR__ . '/..' . '/jaybizzle/crawler-detect/src',
        ),
        'GuzzleHttp\\Psr7\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/psr7/src',
        ),
        'GuzzleHttp\\Promise\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/promises/src',
        ),
        'GuzzleHttp\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/guzzle/src',
        ),
        'GeotWP\\' => 
        array (
            0 => __DIR__ . '/..' . '/timersys/geotargetingwp/src',
        ),
        'GeotFunctions\\' => 
        array (
            0 => __DIR__ . '/..' . '/timersys/geot-functions/src',
        ),
        'EAMann\\Sessionz\\' => 
        array (
            0 => __DIR__ . '/..' . '/ericmann/sessionz/php',
        ),
        'Dotenv\\' => 
        array (
            0 => __DIR__ . '/..' . '/vlucas/phpdotenv/src',
        ),
        'Defuse\\Crypto\\' => 
        array (
            0 => __DIR__ . '/..' . '/defuse/php-encryption/src',
        ),
    );

    public static $classMap = array (
        'EAMann\\WPSession\\DatabaseHandler' => __DIR__ . '/..' . '/timersys/geot-functions/src/Session/wp-session/DatabaseHandler.php',
        'EAMann\\WPSession\\Objects\\Option' => __DIR__ . '/..' . '/timersys/geot-functions/src/Session/wp-session/Option.php',
        'EAMann\\WPSession\\OptionsHandler' => __DIR__ . '/..' . '/timersys/geot-functions/src/Session/wp-session/OptionsHandler.php',
        'EAMann\\WPSession\\SessionHandler' => __DIR__ . '/..' . '/timersys/geot-functions/src/Session/wp-session/SessionHandler.php',
        'IP2Location\\Database' => __DIR__ . '/..' . '/ip2location/ip2location-php/IP2Location.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit49b77a36225fab148e4efc4ef3ea1528::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit49b77a36225fab148e4efc4ef3ea1528::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit49b77a36225fab148e4efc4ef3ea1528::$classMap;

        }, null, ClassLoader::class);
    }
}
