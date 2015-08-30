<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
    'version' => array(
        'major' => 1,
        'minor' => 0,
        'build' => 2
    ),
    'service_manager' => array(
        'factories' => array(
            'Sundew\Db\Adapter' => 'Core\Factory\SundewAdapterFactory',
            'navigation' => 'Core\Factory\SundewNavigationFactory',
            'ConfigManager' => 'Core\Factory\SundewConfigFactory',
        ),
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    'session' => array(
        'remember_me_seconds' => 24129200,
        'use_cookies' => true,
        'cookie_httponly' => true,
        'auth_storage' => 'ceo_dev'
    ),
);
