<?php
namespace Application\ApiController;

use Application\DataAccess\UserDataAccess;
use Application\Entity\User;
use Application\Helper\AuthHelper;
use Core\Model\ApiModel;
use Core\SundewApiController;
use Zend\Crypt\BlockCipher;
use Zend\InputFilter\InputFilterInterface;
use Zend\Json\Json;
use Zend\Math\Rand;

/**
 * Created by PhpStorm.
 * User: Htoonlin
 * Date: 2015-09-22
 * Time: 02:41 PM
 */
class UserApiController extends SundewApiController
{
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
        $page = (int) $this->getContent('page', 1);
        $sort = $this->getContent('sort', 'userName');
        $sortBy = $this->getContent('by', 'asc');
        $filter = $this->getContent('filter', '');
        $pageSize = (int)$this->getContent('size', 10);

        $paginator = $this->userTable()->fetchAll(true,$filter, $sort, $sortBy);

        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($pageSize);

        return $paginator;
    }

    public function postToken()
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