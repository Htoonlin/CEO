<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;


use Application\DataAccess\RouteDataAccess;
use Application\DataAccess\UserRoleDataAccess;
use Core\SundewAuthStorage;
use Core\SundewLogger;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Zend\Authentication\AuthenticationService;
use Core\Helper\View\BackButton;
use Core\Helper\View\ConstantConverter;
use Core\Helper\View\GridFilter;
use Core\Helper\View\GridHeader;
use Core\Helper\View\GridHeaderCell;
use Application\DataAccess\ConstantDataAccess;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\Segment;
use Zend\Mvc\Router\RouteMatch;

class Module implements ConfigProviderInterface, AutoloaderProviderInterface
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
                if($data["is_api"] == 1){
                    array_push($this->publicRoutes, $data['name']);
                }
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
                        $dbAdapter = $sm->getServiceLocator()->get('SundewDbAdapter');
                        $constantDA = new ConstantDataAccess($dbAdapter, 0);
                        return new ConstantConverter($constantDA);
                    }
                ),
            );
        }
        return $this->viewHelperConfig;
    }


    private $serviceConfig;
    public function getServiceConfig()
    {
        if($this->serviceConfig == null){
            $this->serviceConfig = array(
                'factories' => array(
                    'SundewAuthStorage' => function($sm){
                        $config = $sm->get('ConfigManager');
                        $namespace = $config->get('session')['auth_storage'];
                        return new SundewAuthStorage($namespace);
                    },
                    'AuthService' => function($sm)
                    {
                        $dbAdapter = $sm->get('SundewDbAdapter');
                        $authService = new AuthenticationService();
                        $authService->setAdapter(new CredentialTreatmentAdapter($dbAdapter, 'tbl_user', 'username',
                            'password', 'MD5(?) AND status="A"'));
                        $authService->setStorage($sm->get('SundewAuthStorage'));
                        return $authService;
                    },
                    'RouteData' => function($sm)
                    {
                        $dbAdapter = $sm->get('SundewDbAdapter');
                        $authService = $sm->get('AuthService');
                        $routeDataAccess = new RouteDataAccess($dbAdapter, 0);
                        $routeData = $routeDataAccess->getRouteApi()->toArray();
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
                        }
                        return $routeData;
                    },
                    'AppErrorHandling' =>  function($sm) {
                        $authStorage = $sm->get('SundewAuthStorage');
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
}
