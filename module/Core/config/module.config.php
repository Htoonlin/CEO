<?php
/**
 * Created by PhpStorm.
 * User: Htoonlin
 * Date: 2015-08-30
 * Time: 05:28 PM
 */
return array(
    'service_manager' => array(
        'factories' => array(
            'SundewDbAdapter' => 'Core\Factory\SundewAdapterFactory',
            'NavigationManager' => 'Core\Factory\SundewNavigationFactory',
            'ConfigManager' => 'Core\Factory\SundewConfigFactory',
            'SundewApiStrategy' => 'Core\Factory\SundewApiFactory',
        ),
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'formTreeView' => 'Core\Helper\View\FormTreeView',
            'formRow' => 'Core\Helper\View\FormRow',
            'formHorizontal' => 'Core\Helper\View\FormHorizontal',
            'formcheckbox' => 'Core\Helper\View\FormCheckBox',
            'formnumber' => 'Core\Helper\View\FormNumber',
            'formdate' => 'Core\Helper\View\FormDate',
            'formLoader' => 'Core\Helper\View\FormLoader',
        ),
    ),
);