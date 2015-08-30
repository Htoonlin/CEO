<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 7/16/2015
 * Time: 8:30 PM
 */

namespace Core\Factory;

use Zend\Db\Adapter\Adapter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\SundewLogger;

class SundewAdapterFactory implements FactoryInterface
{
    /**
     * Create db adapter service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return Adapter
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $adapter = new Adapter($config['db']);
        $authStorage = $serviceLocator->get('Sundew\AuthStorage');
        $user = array();
        if(!$authStorage->isEmpty()){
            $user = $authStorage->read();
        }
        $fileName = 'Query' . date('Ymd') . '.log';
        $logger = new SundewLogger($fileName, $user);
        $adapter->setProfiler($logger);
        return $adapter;
    }
}