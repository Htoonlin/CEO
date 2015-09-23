<?php
/**
 * Created by PhpStorm.
 * User: Htoonlin
 * Date: 2015-09-22
 * Time: 10:18 AM
 */

namespace Core;

use Application\DataAccess\UserDataAccess;
use Application\Entity\User;
use Core\Model\ApiModel;
use Zend\Crypt\BlockCipher;
use Zend\File\Transfer\Adapter\Http;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractController;
use Zend\Mvc\Exception\DomainException;
use Zend\Mvc\Exception\InvalidArgumentException;
use Zend\Mvc\MvcEvent;
use Zend\Db\Adapter\Adapter;
use Zend\Stdlib\Parameters;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\Http\Request as HttpRequest;

class SundewApiController extends AbstractController
{
    const CONTENT_TYPE_JSON = 'json';

    /**
     * @var Adapter
     */
    private $dbAdapter;

    /**
     * @return array|Adapter|object
     */
    protected function getDbAdapter()
    {
        if(!$this->dbAdapter){
            $this->dbAdapter = $this->getServiceLocator()->get('SundewDbAdapter');
        }
        return $this->dbAdapter;
    }

    /**
     * @var ArrayObject
     */
    private $user;

    /**
     * @return AdapterInterface
     */
    protected function getUser()
    {
        if(!$this->user){
            $this->user = new User();
        }
        return $this->user;
    }

    /**
     * @param User $user
     */
    protected function setUser(User $user){
        $this->user = $user;
    }

    /**
     * @var string
     */
    protected $eventIdentifier = __CLASS__;

    /**
     * @var bool
     */
    protected $ajaxOnly = false;

    /**
     * @var array
     */
    protected $allowMethods = ['GET' , 'POST'];

    /**
     * @var bool
     */
    protected $allowFlashRequest = false;

    /**
     * @var array
     */
    protected $allowMediaTypes = ['application/json','application/x-www-form-urlencoded'];

    /**
     * @var bool
     */
    protected $requireToken = true;

    /**
     * @param $key
     * @return BlockCipher
     */
    protected function getBlockCipher($key){
        $blockCipher = BlockCipher::factory('mcrypt', array('alg' => 'res'));
        $blockCipher->setKey($key);
        return $blockCipher;
    }

    /**
     * @param $result
     * @return ApiModel
     */
    public function ValidateRequest($result){
        $request = $this->getRequest();
        $mediaType = $request->getHeader('Content-Type');
        $model = new ApiModel();

        if(!$mediaType || !$mediaType->match($this->allowMediaTypes)){
            $model->setStatusCode(415);
            $model->setStatusMessage(('Sorry! Doesn\'t support media type.'));
        }else if(!in_array(strtoupper($request->getMethod()), $this->allowMethods)){
            $model->setStatusCode(405);
            $model->setStatusMessage('Sorry! Doesn\'t supports ' . $request->getMethod() . ' method.');
        }else if(!$this->allowFlashRequest && $request->isFlashRequest()){
            $model->setStatusCode(400);
            $model->setStatusMessage('Sorry! Doesn\'t supports Flash Request.');
        }else if($this->ajaxOnly && !$request->isXmlHttpRequest()){
            $model->setStatusCode(400);
            $model->setStatusMessage('Sorry! Doesn\'t supports other request except AJAX.');
        }else{
            if($this->requireToken){
                $model = $this->checkToken($model);
            }else{
                $model = true;
            }
            if($model === true){
                if($result instanceof ApiModel){
                    $model = $result;
                }else{
                    $model = new ApiModel($result);
                }
            }
        }

        return $model;
    }

    /**
     * @param ApiModel $model
     * @return bool|ApiModel
     */
    public function checkToken(ApiModel $model)
    {
        try{
            $request = $this->getRequest();
            $authContent = $request->getHeader('authorization', array());
            if(!$authContent){
                $model->setStatusCode(400);
                $model->setStatusMessage('Sorry! This request need authorization.');
                return $model;
            }
            $authData = Json::decode($authContent->getFieldValue(), self::CONTENT_TYPE_JSON);
            if(empty($authData)){
                $model->setStatusCode(401);
                $model->setStatusMessage("Sorry! Invalid authorization data.");
                return $model;
            }

            $userDataAccess = new UserDataAccess($this->getDbAdapter(), $authData['userId']);
            $user = $userDataAccess->getUser($authData['userId']);
            if($user == null || empty($user->getTokenKey())){
                $model->setStatusCode(401);
                $model->setStatusMessage("Sorry! Invalid authorization data.");
                return $model;
            }
            $blockCipher = $this->getBlockCipher($user->getTokenKey());
            $data = $blockCipher->decrypt($authData['token']);

            if(!$data){
                $model->setStatusCode(401);
                $model->setStatusMessage("Sorry! Invalid authorization data.");
                return $model;
            }
            $data = Json::decode($data, self::CONTENT_TYPE_JSON);
            if(!isset($data['expire']) || !isset($data['userId'])){
                $model->setStatusCode(401);
                $model->setStatusMessage("Sorry! Invalid authorization data.");
                return $model;
            }

            $current = date('YmdHis', time());
            if($current > $data['expire']){
                $model->setStatusCode(401);
                $model->setStatusMessage("Sorry! Token has expired.");
                return $model;
            }
            if($data['userId'] != $user->getUserId() || $data['userName'] != $user->getUserName()){
                $model->setStatusCode(401);
                $model->setStatusMessage("Sorry! Invalid authorization data.");
                return $model;
            }
            $this->user = $user;
            return true;
        }catch(\Exception $ex){
            $model->setStatusCode($ex->getCode());
            $model->setStatusMessage($ex->getMessage());
        }

        return $model;
    }

    /**
     * @return ApiModel
     */
    public function notFoundAction()
    {
        $model = new ApiModel();
        $model->setStatusCode(404);
        return $model;
    }

    /**
     * Dispatch a request
     *
     * If the route match includes an "action" key, then this acts basically like
     * a standard action controller. Otherwise, it introspects the HTTP method
     * to determine how to handle the request, and which method to delegate to.
     *
     * @events dispatch.pre, dispatch.post
     * @param  Request $request
     * @param  null|Response $response
     * @return mixed|Response
     * @throws InvalidArgumentException
     */
    public function dispatch(Request $request, Response $response = null)
    {
        if (! $request instanceof HttpRequest) {
            throw new InvalidArgumentException(
                'Expected an HTTP request');
        }

        return parent::dispatch($request, $response);
    }

    /**
     * @var array
     */
    private $contentData = array();

    /**
     * @param MvcEvent $e
     * @return ApiModel
     */
    public function onDispatch(MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();
        if(!$routeMatch){
            throw new DomainException('Missing route matches; unsure how to retrieve action');
        }
        $request = $e->getRequest();
        $action = $routeMatch->getParam('action', false);

        $method = $this->getMethodByType($request->getMethod(), $action);
        if(!method_exists($this, $method)){
            $method = 'notFoundAction';
        }else{
            $jsonContent = $request->getContent();
            $content = Json::decode($jsonContent, self::CONTENT_TYPE_JSON);
            if($content == null){
                $content = array();
            }
            $postParam = $request->getPost()->toArray();
            $getParam = $request->getQuery()->toArray();
            $fileParam = $request->getFiles()->toArray();

            $this->contentData = array_merge($getParam, $postParam, $content, $fileParam);
        }

        $actionResponse = $this->ValidateRequest($this->$method());
        $e->setResult($actionResponse);
        return $actionResponse;
    }

    protected function getContent($name = null, $default = null)
    {
        $parameters = new Parameters($this->contentData);
        if($name == null){
            return $parameters;
        }

        return $parameters->get($name, $default);
    }

    /**
     * @param $type
     * @param $action
     * @return mixed|string
     */
    private function getMethodByType($type, $action)
    {
        $method = str_replace(array('.', '-', '_'), ' ', $action);
        $method = ucwords($method);
        $method = str_replace(' ', '', $method);
        $method = strtolower($type) . $method;
        return $method;
    }
}