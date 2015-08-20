<?php
/**
 * Created by PhpStorm.
 * User: Htoonlin
 * Date: 2015-08-20
 * Time: 11:38 AM
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class PreferencesController extends AbstractActionController
{
    private function getGeneralInfo()
    {
        $moduleManger = $this->getServiceLocator()->get('ModuleManager');
        $modules = array_keys($moduleManger->getLoadedModules());

        $configManager = $this->getServiceLocator()->get('ConfigManager');
        return  array(
            'id' => 'general-info',
            'icon' => 'fa fa-cogs',
            'title' => 'General Info',
            'isOpen' => 'in',
            'data' => array(
                'Version' => $configManager->getVersion(),
                'License' => 'GNU GENERAL PUBLIC LICENSE Version 2, June 1991',
                'Modules' => implode('<br />', $modules),
            )
        );
    }

    private function getControllers()
    {
        $configManager = $this->getServiceLocator()->get('ConfigManager');
        return  array(
            'id' => 'controllers',
            'icon' => 'fa fa-cubes',
            'title' => 'Controller List',
            'data' => $configManager->get('controllers')['invokables'],
        );
    }

    private function getViewManager()
    {
        $configManager = $this->getServiceLocator()->get('ConfigManager');
        $viewManager = $configManager->get('view_manager');
        $data = array();
        foreach($viewManager as $key => $value){
            if(is_array($value)) continue;
            $data[$key] = $value;
        }

        foreach($viewManager['template_map'] as $key => $value){
            $data[$key] = str_replace(APP_PATH, '', $value) . '<br />';
        }

        $data['View Paths'] = '';
        foreach($viewManager['template_path_stack'] as $value){
            $data['View Paths'] .= str_replace(APP_PATH, '', $value) . '<br />';
        }

        return  array(
            'id' => 'view-manager',
            'icon' => 'fa fa-clone',
            'title' => 'View Manager',
            'data' => $data,
        );
    }

    private function getDbManager()
    {
        $configManager = $this->getServiceLocator()->get('ConfigManager');
        $dbConfig = $configManager->get('db');
        $dbConfig['driver_options'] = implode('<br />', array_values($dbConfig['driver_options']));
        $dbConfig['password'] = '*****';

        return  array(
            'id' => 'db-manager',
            'icon' => 'fa fa-database',
            'title' => 'DB Manager',
            'data' => $dbConfig,
        );
    }

    public function indexAction()
    {
        return new ViewModel(array(
            'settings' => array(
                $this->getGeneralInfo(),
                $this->getControllers(),
                $this->getViewManager(),
                $this->getDbManager(),
            ),
        ));
    }

    public function saveDbAction()
    {
        $configManager = $this->getServiceLocator()->get('ConfigManager');
        $dbConfig = $configManager->get('db');
        $dsn = $this->params()->fromPost('dbDSN', $dbConfig['dsn']);
        $user = $this->params()->fromPost('dbUser', $dbConfig['username']);
        $password = $this->params()->fromPost('dbPassword', $dbConfig['password']);
        $configManager->setDbConfig($dsn, $user, $password);
        $this->flashMessenger()->addWarningMessage('Database configuration has been changed.');

        return $this->redirect()->toRoute('auth', array('action' => 'logout'));
    }

    public function jsonAction()
    {
        return new JsonModel($this->getServiceLocator()->get('Config'));
    }

    public function phpAction()
    {
        $this->layout('layout/null');
        return phpinfo();
    }
}