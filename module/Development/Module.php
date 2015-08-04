<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 8/4/2015
 * Time: 1:53 PM
 */

namespace Development;

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
                ),
            ),
        );
    }
}