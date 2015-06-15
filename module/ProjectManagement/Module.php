<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 5/25/2015
 * Time: 12:59 PM
 */

namespace ProjectManagement;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                )
            )
        );
    }
}