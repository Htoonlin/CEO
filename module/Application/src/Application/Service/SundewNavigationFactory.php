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

/**
 * Class SundewNavigationFactory
 * @package Application\Service
 */
class SundewNavigationFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocatorInterface
     * @return \Zend\Navigation\Navigation
     */
    public function createService(ServiceLocatorInterface $serviceLocatorInterface)
    {
        $navigation = new SundewNavigation();
        return $navigation->createService($serviceLocatorInterface);
    }
}