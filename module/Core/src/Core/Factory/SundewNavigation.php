<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 3/16/2015
 * Time: 11:12 AM
 */

namespace Core\Factory;


use Application\DataAccess\MenuDataAccess;
use Application\DataAccess\UserRoleDataAccess;
use Interop\Container\ContainerInterface;
use Zend\Navigation\Service\DefaultNavigationFactory;
use Zend\ServiceManager\Exception\InvalidArgumentException;
use Zend\ServiceManager\ServiceLocatorInterface;

class SundewNavigation extends DefaultNavigationFactory
{

    protected function getPages(ContainerInterface $containerInterface)
    {
        if(null === $this->pages){

            $userId = 0;
            $authService = $containerInterface->get('AuthService');
            $dbAdapter = $containerInterface->get('SundewDbAdapter');
            if($authService->hasIdentity()){
                $userId = $authService->getIdentity()->userId;
            }
            $menuTable = new MenuDataAccess($dbAdapter, $userId);
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

            $configuration['NavigationManager'][$this->getName()] = $menuList;
            if(!isset($configuration['NavigationManager'])){
                throw new InvalidArgumentException('Could not find navigation configuration key');
            }
            if (!isset($configuration['NavigationManager'][$this->getName()])) {

                throw new InvalidArgumentException(sprintf(
                    'Failed to find a navigation container by the name "%s"',
                    $this->getName()
                ));
            }

            $pages = $this->getPagesFromConfig($configuration['NavigationManager'][$this->getName()]);
            $this->pages = $this->preparePages($containerInterface, $pages);
        }

        return $this->pages;
    }

}