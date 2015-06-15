<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 3/16/2015
 * Time: 11:36 AM
 */

namespace Application\Service;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SundewNavigationFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocatorInterface)
    {
        $navigation = new SundewNavigation();
        return $navigation->createService($serviceLocatorInterface);
    }
}