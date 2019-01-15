<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit4a9743ddaf51f2a39af734a4468863b2
{
    public static $files = array (
        '23846a2512770688e91c6220c735f257' => __DIR__ . '/..' . '/yoast/wp-helpscout/src/functions.php',
    );

    public static $prefixesPsr0 = array (
        'x' => 
        array (
            'xrstf\\Composer52' => 
            array (
                0 => __DIR__ . '/..' . '/xrstf/composer-php52/lib',
            ),
        ),
    );

    public static $classMap = array (
        'WPSEO_Option_Woo' => __DIR__ . '/../..' . '/classes/option-woo.php',
        'WPSEO_WooCommerce_Beacon_Setting' => __DIR__ . '/../..' . '/classes/woocommerce-beacon-setting.php',
        'WPSEO_WooCommerce_Wrappers' => __DIR__ . '/../..' . '/classes/woocommerce-wrappers.php',
        'Yoast_HelpScout_Beacon' => __DIR__ . '/..' . '/yoast/wp-helpscout/src/class-helpscout-beacon.php',
        'Yoast_HelpScout_Beacon_Identifier' => __DIR__ . '/..' . '/yoast/wp-helpscout/src/class-helpscout-beacon-identifier.php',
        'Yoast_HelpScout_Beacon_Setting' => __DIR__ . '/..' . '/yoast/wp-helpscout/src/interface-helpscout-beacon-setting.php',
        'Yoast_I18n_WordPressOrg_v3' => __DIR__ . '/..' . '/yoast/i18n-module/src/i18n-module-wordpressorg.php',
        'Yoast_I18n_v3' => __DIR__ . '/..' . '/yoast/i18n-module/src/i18n-module.php',
        'Yoast_Product_WPSEO_WooCommerce' => __DIR__ . '/../..' . '/classes/product-wpseo-woocommerce.php',
        'xrstf\\Composer52\\AutoloadGenerator' => __DIR__ . '/..' . '/xrstf/composer-php52/lib/xrstf/Composer52/AutoloadGenerator.php',
        'xrstf\\Composer52\\Generator' => __DIR__ . '/..' . '/xrstf/composer-php52/lib/xrstf/Composer52/Generator.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixesPsr0 = ComposerStaticInit4a9743ddaf51f2a39af734a4468863b2::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit4a9743ddaf51f2a39af734a4468863b2::$classMap;

        }, null, ClassLoader::class);
    }
}
