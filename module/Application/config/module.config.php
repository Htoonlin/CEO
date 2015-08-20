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
            'system_install' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/install[/:action]',
                    'constraints' => array(
                        'action' => 'install|saveDb',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Preferences',
                        'action'     => 'install',
                    ),
                ),
            ),
            'auth' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/auth[/:action][/:id]',
                    'constraints' => array(
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
            'Application\Controller\Preferences' => 'Application\Controller\PreferencesController',
        ),
    ),
    'view_manager' => array(
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
    ),
);
