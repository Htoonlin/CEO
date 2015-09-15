<?php

namespace Application\Controller;

use Application\DataAccess\UserDataAccess;
use Application\DataAccess\UserRoleDataAccess;
use Application\Entity\User;
use Application\Helper\AuthHelper;
use Core\SundewController;
use Zend\Session\Container;
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
    private function userTable($userId)
    {
        return new UserDataAccess($this->getDbAdapter(), $userId);
    }

    const SESSION_NS = 'ceo_auth_session';

    /**
     * @param int $increment
     * @return mixed
     */
    private function sessionCount($increment = 0)
    {
        $auth = new Container(self::SESSION_NS);
        $auth->failed = $auth->failed + $increment;
        return $auth->failed;
    }

    private $storage;

    /**
     * @return array|object
     */
    private function getSessionStorage()
    {
        if(!$this->storage){
            $this->storage = $this->getServiceLocator()->get('SundewAuthStorage');
        }
        return $this->storage;
    }

    const CAPTCHA_DIR = './data/captcha/';

    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function captchaAction()
    {
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'image/png');

        $captcha = $this->params()->fromRoute('id', false);
        if($captcha){
            $image = self::CAPTCHA_DIR . $captcha;
            if(file_exists($image) !== false){
                $imageContent = @file_get_contents($image);
                $response->setStatusCode(200);
                $response->setContent($imageContent);
                if(file_exists($image) == true){
                    unlink($image);
                }
            }
        }
        return $response;
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
        if($this->sessionCount() == 0){
            $this->sessionCount(1);
        }
        $hasCaptcha = ($this->sessionCount() >= 3);

        if($hasCaptcha){
            $plugin = $this->plugin('url');
            $url = $plugin->fromRoute('auth', array('action' => 'captcha'));
            $form = $helper->getForm($hasCaptcha, $url, self::CAPTCHA_DIR);
        }else{
            $form = $helper->getForm($hasCaptcha);
        }

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
                    $this->userTable($data['userId'])->saveUser($user);
                    $columnsToOmit = array('password');
                    $authUser = $authAdapter->getResultRowObject(null, $columnsToOmit);
                    $userRoles = array();
                    foreach ($this->userRoleTable()->grantRoles($authUser->userId) as $userRole) {
                        array_push($userRoles, $userRole->roleId);
                    }
                    $authUser->roles = $userRoles;
                    $this->getAuthService()->getStorage()->write($authUser);
                    $this->sessionCount(-$this->sessionCount());
                    return $this->redirect()->toRoute('home');
                }
                $message = "Invalid user/password";
                $this->sessionCount(1);
            }
        }

        $this->layout('layout/empty');

        return new ViewModel(array(
            'form' => $form,
            'message' => $message,
            'hasCaptcha' => $hasCaptcha,
        ));
    }

    /**
     * @return \Zend\Http\Response
     */
    public function logoutAction()
    {
        $this->getSessionStorage()->cacheClear();
        $this->getSessionStorage()->forgetMe();
        $this->getAuthService()->clearIdentity();
        return $this->redirect()->toRoute('auth');
    }
}

