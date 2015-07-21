<?php

namespace Application\Controller;

use Application\DataAccess\UserDataAccess;
use Application\DataAccess\UserRoleDataAccess;
use Application\Entity\User;
use Application\Helper\AuthHelper;
use Application\Service\SundewController;
use Zend\View\Model\ViewModel;

class AuthController extends SundewController
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
     * @return UserRoleDataAccess
     */
    private function userRoleTable()
    {
        return new UserRoleDataAccess($this->getDbAdapter());
    }

    /**
     * @return UserDataAccess
     */
    private function userTable()
    {
        return new UserDataAccess($this->getDbAdapter());
    }

    private $storage;

    /**
     * @return array|object
     */
    public function getSessionStorage()
    {
        if(!$this->storage){
            $this->storage = $this->getServiceLocator()->get('Sundew\AuthStorage');
        }
        return $this->storage;
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function indexAction()
    {
        if($this->getAuthService()->hasIdentity()){
            return $this->redirect()->toRoute('home');
        }

        $helper = new AuthHelper();
        $form = $helper->getForm();
        $request = $this->getRequest();
        $message = "";

        if($request->isPost())
        {
            $form->setInputFilter($helper->getInputFilter());
            $form->setData($request->getPost());
            if($form->isValid())
            {
                $user = $request->getPost('username');
                $pass = $request->getPost('password');

                $authAdapter = $this->getAuthService()->getAdapter();
                $authAdapter->setIdentity($user)->setCredential($pass);

                $result = $authAdapter->authenticate();
                if($result->isValid()){
                    if($request->getPost('remember') == 1){
                        $this->getSessionStorage()
                            ->setRememberMe(1);
                        $this->getAuthService()->setStorage($this->getSessionStorage());
                    }
                    $data = (array)$authAdapter->getResultRowObject();
                    $user = new User();
                    $user->exchangeArray($data);
                    $user->setLastLogin(date('Y-m-d H:i:s'));
                    $this->userTable()->saveUser($user);
                    $columnsToOmit = array('password');
                    $authUser = $authAdapter->getResultRowObject(null, $columnsToOmit);
                    $userRoles = array();
                    foreach ($this->userRoleTable()->grantRoles($authUser->userId) as $userRole) {
                        array_push($userRoles, $userRole->roleId);
                    }
                    $authUser->roles = $userRoles;
                    $this->getAuthService()->getStorage()->write($authUser);
                    return $this->redirect()->toRoute('home');
                }else{
                    $message = "Invalid user/password";
                }
            }
        }

        $this->layout('layout/empty');

        return new ViewModel(array(
            'form' => $form,
            'message' => $message,
        ));
    }

    /**
     * @return \Zend\Http\Response
     */
    public function logoutAction()
    {
        $this->getSessionStorage()->forgetMe();
        $this->getAuthService()->clearIdentity();

        return $this->redirect()->toRoute('auth');
    }
}

