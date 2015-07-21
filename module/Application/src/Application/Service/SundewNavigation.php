<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 3/16/2015
 * Time: 11:12 AM
 */

namespace Application\Service;


use Application\DataAccess\MenuDataAccess;
use Application\DataAccess\UserRoleDataAccess;
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
            $userId = 0;
            $authService = $serviceLocatorInterface->get('AuthService');
            if($authService->hasIdentity()){
                $userId = $authService->getIdentity()->userId;
            }
            $cache_ns = 'menu_cache_' . $userId;
            $menuList = $menuTable->getCache()->getItem($cache_ns);
            if(!$menuList){
                $userRoleDA = new UserRoleDataAccess($dbAdapter);
                $roles = array();
                foreach($userRoleDA->grantRoles($userId) as $role){
                    array_push($roles,(int)$role->roleId);
                }
                $menuList = $menuTable->getMenuList(null, $roles);
                $menuTable->getCache()->setItem($cache_ns, $menuList);
            }

            $configuration['navigation'][$this->getName()] = $menuList;
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