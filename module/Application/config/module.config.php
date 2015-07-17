<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'router' => array(
        'routes' => array(
            'auth' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/auth[/:action][/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                        'action' => '[a-zA-Z][a-zA-Z0-9_]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Auth',
                        'action'     => 'index',
                    ),
                ),
            ),
            'user' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/user[/:action][/image/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                        'action' => 'profile|password|image',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\User',
                        'action'     => 'profile',
                    ),
                ),
            ),
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'factories' => array(
            'Sundew\Db\Adapter' => 'Application\Service\SundewAdapterFactory',
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
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\User' => 'Application\Controller\UserController',
            'Application\Controller\Role' => 'Application\Controller\RoleController',
            'Application\Controller\Constant' => 'Application\Controller\ConstantController',
            'Application\Controller\Menu' => 'Application\Controller\MenuController',
            'Application\Controller\Auth' => 'Application\Controller\AuthController',
            'Application\Controller\Controller' => 'Application\Controller\AppController',
            'Application\Controller\Route' => 'Application\Controller\RouteController',
            'Application\Controller\MenuPermission' => 'Application\Controller\MenuPermissionController',
            'Application\Controller\RoutePermission' => 'Application\Controller\RoutePermissionController',
            'Application\Controller\Dashboard' => 'Application\Controller\DashboardController',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/error-layout'=> __DIR__ . '/../view/layout/empty.phtml',
            'layout/layout'           => __DIR__ . '/../view/layout/main.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
            'shared/'                 => __DIR__ . '/../view/shared/',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);
