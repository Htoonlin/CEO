<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Application\DataAccess\ControllerDataAccess;
use Application\DataAccess\RouteDataAccess;
use Application\Service\SundewAuthStorage;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Log\Logger;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ControllerProviderInterface;
use Zend\ModuleManager\Feature\FormElementProviderInterface;
use Zend\ModuleManager\Feature\RouteProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\Mvc\Application;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\Segment;
use Zend\Mvc\Router\RouteMatch;

class Module implements ConfigProviderInterface, AutoloaderProviderInterface, ServiceProviderInterface, ViewHelperProviderInterface
{
    protected $publicRoutes = array('auth');
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $this->generateRoute($e);
        $eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'checkAuth'), -100);
    }

    private function generateRoute(MvcEvent $e)
    {
        $sm = $e->getApplication()->getServiceManager();
        $routeData = $sm->get('RouteData');

        try{
            $router = $e->getRouter();
            foreach($routeData as $data)
            {
                $constraints = json_decode($data->getConstraints(), true);
                $route = Segment::factory(
                    array(
                        'route' => $data->getRoute(),
                        'constraints' => $constraints,
                        'defaults' => array(
                            'controller' => $data->getController(),
                            'action' => 'index',
                        )
                    )
                );
                $router->addRoute($data->getName(), $route);
            }
        }catch(\Exception $ex){
            Logger::ERR('Error from generateRoute(): ' . $ex->getMessage());
        }
    }

    public function checkAuth(MvcEvent $e)
    {
        $sm = $e->getApplication()->getServiceManager();
        $auth = $sm->get('AuthService');

        $match = $e->getRouteMatch();

        if(!$match instanceof RouteMatch){
            return;
        }

        $name = $match->getMatchedRouteName();
        if(in_array($name, $this->publicRoutes)){
            return;
        }

        //Check identity
        if($auth->hasIdentity()) {
            $viewModel = $e->getViewModel();
            $viewModel->current_user = $auth->getIdentity();
            return;
        }

        $router = $e->getRouter();
        $url = $router->assemble(array(), array(
            'name' => 'auth'
        ));

        $response = $e->getResponse();
        $response->getHeaders()->addHeaderLine('Location', $url);
        $response->setStatusCode(302);

        return $response;
    }

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

    private $serviceConfig;
    public function getServiceConfig()
    {
        if($this->serviceConfig == null){
            $this->serviceConfig = array(
                'factories' => array(
                    'navigation' => 'Application\Service\SundewNavigationFactory',
                    'Application\Service\SundewAuthStorage' => function($sm)
                    {
                        return new SundewAuthStorage('sundew_auth');
                    },
                    'AuthService' => function($sm)
                    {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $authService = new AuthenticationService();
                        $authService->setAdapter(new CredentialTreatmentAdapter($dbAdapter, 'tbl_user', 'username',
                                                                        'password', 'MD5(?) AND status="A"'));
                        $authService->setStorage($sm->get('Application\Service\SundewAuthStorage'));
                        return $authService;
                    },
                    'RouteData' => function($sm)
                    {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $routeDataAccess = new RouteDataAccess($dbAdapter);
                        $roleId = 0;

                        $authService = $sm->get('AuthService');
                        if($authService->hasIdentity()){
                            $roleId = $authService->getIdentity()->userRole;
                        }

                        return $routeDataAccess->getRouteData($roleId);
                    },
                    'ControllerData' => function($sm)
                    {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $dataAccess = new ControllerDataAccess($dbAdapter);
                        return $dataAccess->fetchAll();
                    }
                ),
            );
        }
        return $this->serviceConfig;
    }

    /**
     * Expected to return \Zend\ServiceManager\Config object or array to
     * seed such an object.
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getViewHelperConfig()
    {
        return array(
            'invokables' => array(
                'formTreeView' => 'Application\Helper\View\FormTreeView',
                'formRow' => 'Application\Helper\View\FormRow',
                'formHorizontal' => 'Application\Helper\View\FormHorizontal',
                'gridHeaderCell' => 'Application\Helper\View\GridHeaderCell',
                'formcheckbox' => 'Application\Helper\View\FormCheckBox',
                'formnumber' => 'Application\Helper\View\FormNumber',
                'formdate' => 'Application\Helper\View\FormDate',
                'formLoader' => 'Application\Helper\View\FormLoader',
            ),
        );
    }
}
