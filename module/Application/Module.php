<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Application\DataAccess\ConstantDataAccess;
use Application\DataAccess\RouteDataAccess;
use Application\DataAccess\UserRoleDataAccess;
use Application\Helper\View\BackButton;
use Application\Helper\View\ConstantConverter;
use Application\Helper\View\GridFilter;
use Application\Helper\View\GridHeader;
use Application\Helper\View\GridHeaderCell;
use Application\Service\SundewAuthStorage;
use Application\Service\SundewLogger;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\Segment;
use Zend\Mvc\Router\RouteMatch;

class Module implements ConfigProviderInterface, AutoloaderProviderInterface, ServiceProviderInterface, ViewHelperProviderInterface
{
    protected $publicRoutes = array('auth', 'system_install');
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $this->generateRoute($e);
        $eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'checkAuth'), -100);
        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'error_handling'));
        $eventManager->attach(MvcEvent::EVENT_RENDER_ERROR, array($this, 'error_handling'));
    }

    private $cacheRouteData;
    private function generateRoute(MvcEvent $e)
    {
        if($this->cacheRouteData == null){
            $sm = $e->getApplication()->getServiceManager();
            $this->cacheRouteData = $sm->get('RouteData');
        }

        try{
            $router = $e->getRouter();
            foreach($this->cacheRouteData as $data)
            {
                $constraints = json_decode($data['constraints'], true);
                $data['constraints'] = $constraints;
                $data['defaults'] = array(
                    'controller' => $data['controller'],
                    'action' => 'index',
                );
                $route = Segment::factory($data);
                $router->addRoute($data['name'], $route);
            }

            $this->hasRoute = true;
        }catch(\Exception $ex){
            throw $ex;
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

    public function error_handling(MvcEvent $e){
        $exception = $e->getResult()->exception;
        if($exception){
            $sm = $e->getApplication()->getServiceManager();
            $service = $sm->get('AppErrorHandling');
            $service->logException($exception);
        }
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
                    'Sundew\AuthStorage' => function($sm){
                        $config = $sm->get('ConfigManager');
                        $namespace = $config->get('session')['auth_storage'];
                        return new SundewAuthStorage($namespace);
                    },
                    'AuthService' => function($sm)
                    {
                        $dbAdapter = $sm->get('Sundew\Db\Adapter');
                        $authService = new AuthenticationService();
                        $authService->setAdapter(new CredentialTreatmentAdapter($dbAdapter, 'tbl_user', 'username',
                                                                        'password', 'MD5(?) AND status="A"'));
                        $authService->setStorage($sm->get('Sundew\AuthStorage'));
                        return $authService;
                    },
                    'RouteData' => function($sm)
                    {
                        $dbAdapter = $sm->get('Sundew\Db\Adapter');
                        $routeDataAccess = new RouteDataAccess($dbAdapter);
                        $authService = $sm->get('AuthService');
                        if($authService->hasIdentity()){
                            $userId = $authService->getIdentity()->userId;
                            $cache_ns = 'route_cache_' . $userId;
                            $routeData = $routeDataAccess->getCache()->getItem($cache_ns);
                            if(empty($routeData) || $routeData == null){
                                $userRoleDA = new UserRoleDataAccess($dbAdapter);
                                $roles = array();
                                foreach($userRoleDA->grantRoles($userId) as $role){
                                    array_push($roles,(int)$role->roleId);
                                }
                                $routeData = $routeDataAccess->getRouteData($roles)->toArray();
                                $routeDataAccess->getCache()->setItem($cache_ns, $routeData);
                            }
                            return $routeData;
                        }

                        return array();
                    },
                    'AppErrorHandling' =>  function($sm) {
                        $authStorage = $sm->get('Sundew\AuthStorage');
                        $user = array();
                        if(!$authStorage->isEmpty()){
                            $user = $authStorage->read();
                        }
                        $filename = 'Error' . date('Ymd') . '.log';
                        $service = new SundewLogger($filename, $user);
                        return $service;
                    },
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
    private $viewHelperConfig;
    public function getViewHelperConfig()
    {
        if($this->viewHelperConfig == null){
            $this->viewHelperConfig = array(
                'factories' => array(
                    'gridHeaderCell' => function($sm){
                        $app = $sm->getServiceLocator()->get('Application');
                        return new GridHeaderCell($app->getRequest());
                    },
                    'gridHeader' => function($sm){
                        $app = $sm->getServiceLocator()->get('Application');
                        return new GridHeader($app->getRequest());
                    },
                    'gridFilter' => function($sm){
                        $app = $sm->getServiceLocator()->get('Application');
                        return new GridFilter($app->getRequest());
                    },
                    'backButton' => function($sm){
                        $app = $sm->getServiceLocator()->get('Application');
                        return new BackButton($app->getRequest(), $app->getMvcEvent()->getRouteMatch());
                    },
                    'constantConverter' => function($sm){
                        $dbAdapter = $sm->getServiceLocator()->get('Sundew\Db\Adapter');
                        $constantDA = new ConstantDataAccess($dbAdapter);
                        return new ConstantConverter($constantDA);
                    }
                ),
                'invokables' => array(
                    'formTreeView' => 'Application\Helper\View\FormTreeView',
                    'formRow' => 'Application\Helper\View\FormRow',
                    'formHorizontal' => 'Application\Helper\View\FormHorizontal',
                    'formcheckbox' => 'Application\Helper\View\FormCheckBox',
                    'formnumber' => 'Application\Helper\View\FormNumber',
                    'formdate' => 'Application\Helper\View\FormDate',
                    'formLoader' => 'Application\Helper\View\FormLoader',
                ),
            );
        }
        return $this->viewHelperConfig;
    }
}
