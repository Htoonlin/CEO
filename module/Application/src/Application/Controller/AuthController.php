<?php

namespace Application\Controller;

use Application\DataAccess\UserDataAccess;
use Application\Entity\User;
use Application\Helper\AuthHelper;
use Zend\Form\Annotation\Hydrator;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AuthController extends AbstractActionController
{
    private $authService;
    public function getAuthService()
    {
        if(!$this->authService){
            $this->authService = $this->getServiceLocator()->get('AuthService');
        }
        return $this->authService;
    }

    private function userTable()
    {
        $dbAdapter = $this->getServiceLocator()->get('Sundew\Db\Adapter');
        return new UserDataAccess($dbAdapter);
    }

    private $storage;
    public function getSessionStorage()
    {
        if(!$this->storage){
            $this->storage = $this->getServiceLocator()->get('Application\Service\SundewAuthStorage');
        }
        return $this->storage;
    }

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
                    $this->getAuthService()->getStorage()->write($authAdapter->getResultRowObject(null, $columnsToOmit));
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

    public function logoutAction()
    {
        $this->getSessionStorage()->forgetMe();
        $this->getAuthService()->clearIdentity();

        return $this->redirect()->toRoute('auth');
    }
}

