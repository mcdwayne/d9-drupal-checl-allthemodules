<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitaa62c8e72081c3741ec1b3ba66c43a55
{
    public static $prefixesPsr0 = array (
        'R' => 
        array (
            'Requests' => 
            array (
                0 => __DIR__ . '/..' . '/rmccue/requests/library',
            ),
        ),
    );

    public static $classMap = array (
        'Culqi\\Cards' => __DIR__ . '/..' . '/culqi/culqi-php/lib/Culqi/Cards.php',
        'Culqi\\Charges' => __DIR__ . '/..' . '/culqi/culqi-php/lib/Culqi/Charges.php',
        'Culqi\\Client' => __DIR__ . '/..' . '/culqi/culqi-php/lib/Culqi/Client.php',
        'Culqi\\Culqi' => __DIR__ . '/..' . '/culqi/culqi-php/lib/Culqi/Culqi.php',
        'Culqi\\Customers' => __DIR__ . '/..' . '/culqi/culqi-php/lib/Culqi/Customers.php',
        'Culqi\\Error\\AuthenticationError' => __DIR__ . '/..' . '/culqi/culqi-php/lib/Culqi/Error/Errors.php',
        'Culqi\\Error\\CulqiException' => __DIR__ . '/..' . '/culqi/culqi-php/lib/Culqi/Error/Errors.php',
        'Culqi\\Error\\InputValidationError' => __DIR__ . '/..' . '/culqi/culqi-php/lib/Culqi/Error/Errors.php',
        'Culqi\\Error\\InvalidApiKey' => __DIR__ . '/..' . '/culqi/culqi-php/lib/Culqi/Error/Errors.php',
        'Culqi\\Error\\MethodNotAllowed' => __DIR__ . '/..' . '/culqi/culqi-php/lib/Culqi/Error/Errors.php',
        'Culqi\\Error\\NotFound' => __DIR__ . '/..' . '/culqi/culqi-php/lib/Culqi/Error/Errors.php',
        'Culqi\\Error\\UnableToConnect' => __DIR__ . '/..' . '/culqi/culqi-php/lib/Culqi/Error/Errors.php',
        'Culqi\\Error\\UnhandledError' => __DIR__ . '/..' . '/culqi/culqi-php/lib/Culqi/Error/Errors.php',
        'Culqi\\Events' => __DIR__ . '/..' . '/culqi/culqi-php/lib/Culqi/Events.php',
        'Culqi\\Iins' => __DIR__ . '/..' . '/culqi/culqi-php/lib/Culqi/Iins.php',
        'Culqi\\Plans' => __DIR__ . '/..' . '/culqi/culqi-php/lib/Culqi/Plans.php',
        'Culqi\\Refunds' => __DIR__ . '/..' . '/culqi/culqi-php/lib/Culqi/Refunds.php',
        'Culqi\\Resource' => __DIR__ . '/..' . '/culqi/culqi-php/lib/Culqi/Resource.php',
        'Culqi\\Subscriptions' => __DIR__ . '/..' . '/culqi/culqi-php/lib/Culqi/Subscriptions.php',
        'Culqi\\Tokens' => __DIR__ . '/..' . '/culqi/culqi-php/lib/Culqi/Tokens.php',
        'Culqi\\Transfers' => __DIR__ . '/..' . '/culqi/culqi-php/lib/Culqi/Transfers.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixesPsr0 = ComposerStaticInitaa62c8e72081c3741ec1b3ba66c43a55::$prefixesPsr0;
            $loader->classMap = ComposerStaticInitaa62c8e72081c3741ec1b3ba66c43a55::$classMap;

        }, null, ClassLoader::class);
    }
}
