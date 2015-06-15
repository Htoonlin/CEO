<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 4/24/2015
 * Time: 11:39 AM
 */

namespace CustomerRelation;


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