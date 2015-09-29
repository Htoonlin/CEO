<?php
/**
 * Created by PhpStorm.
 * User: Htoonlin
 * Date: 2015-09-29
 * Time: 11:43 AM
 */

namespace Application\ApiController;


use Application\DataAccess\UserDataAccess;
use Application\Entity\User;
use Application\Helper\AuthHelper;
use Core\Model\ApiModel;
use Core\SundewApiController;
use Zend\InputFilter\InputFilterInterface;
use Zend\Json\Json;
use Zend\Math\Rand;

class AuthApiController extends SundewApiController
{
    protected $allowMethods = ['POST'];

    protected $requireToken = false;

    private $authService;

    /**
     * @return array|object
     */
    public function getAuthService()
    {
        if(!$this->authService){
            $this->authService = $this->getServiceLocator()->get('AuthService');
        }

        return $this->authService;
    }
    /**
     * @return UserDataAccess
     */
    private function userTable()
    {
        return new UserDataAccess($this->getDbAdapter(), 0);
    }

    /**
     * @return ApiModel
     */
    public function postIndex()
    {
        $api = new ApiModel();
        $helper = new AuthHelper();
        $inputFilter = $helper->getInputFilter();
        $inputFilter->setData($this->getContent());
        $inputFilter->setValidationGroup(InputFilterInterface::VALIDATE_ALL);

        if($inputFilter->isValid()){
            $user = $this->getContent('username');
            $pass = $this->getContent('password');
            $authAdapter = $this->getAuthService()->getAdapter();
            $authAdapter->setIdentity($user)->setCredential($pass);
            $result = $authAdapter->authenticate();
            if($result->isValid()){
                $key = Rand::getString(32);
                $authData = (array)$authAdapter->getResultRowObject();
                $user = new User();
                $user->exchangeArray($authData);
                $expire = date('YmdHis', strtotime("+1 month"));
                $data = Json::encode(array(
                    'userId' => $user->getUserId(),
                    'userName' => $user->getUserName(),
                    'expire' => $expire,
                ));
                $user->setTokenKey($key);
                $tokenString = $this->getBlockCipher($key)->encrypt($data);
                $this->userTable()->saveUser($user);
                $api->setStatusCode(201);
                $api->setStatusMessage('Token has been created.');
                $api->setResponseData(array(
                    'userId' => $user->getUserId(),
                    'username' => $user->getUserName(),
                    'token' => $tokenString,
                    'expire' => $expire
                ));
            }else{
                $api->setStatusCode(401);
                $api->setStatusMessage('Invalid user or password.');
            }
        }else{
            $api->setResponseData($inputFilter->getMessages());
        }

        return $api;
    }
}