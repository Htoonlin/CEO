<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 3/16/2015
 * Time: 11:12 AM
 */

namespace Application\Service;


use Application\DataAccess\MenuDataAccess;
use Zend\Navigation\Service\DefaultNavigationFactory;
use Zend\ServiceManager\Exception\InvalidArgumentException;
use Zend\ServiceManager\ServiceLocatorInterface;

class SundewNavigation extends DefaultNavigationFactory
{
    protected function getPages(ServiceLocatorInterface $serviceLocatorInterface)
    {
        if(null === $this->pages){
            $dbAdapter = $serviceLocatorInterface->get('Sundew\Db\Adapter');
            $menuTable = new MenuDataAccess($dbAdapter);

            $roleId = 0;

            $authService = $serviceLocatorInterface->get('AuthService');
            if($authService->hasIdentity()){
                $roleId = $authService->getIdentity()->userRole;
            }

            $configuration['navigation'][$this->getName()] = $menuTable->getMenuList(null, $roleId);
            if(!isset($configuration['navigation'])){
                throw new InvalidArgumentException('Could not find navigation configuration key');
            }
            if (!isset($configuration['navigation'][$this->getName()])) {

                throw new InvalidArgumentException(sprintf(
                    'Failed to find a navigation container by the name "%s"',
                    $this->getName()
                ));
            }

            $pages = $this->getPagesFromConfig($configuration['navigation'][$this->getName()]);
            $this->pages = $this->preparePages($serviceLocatorInterface, $pages);
        }
        return $this->pages;
    }

}