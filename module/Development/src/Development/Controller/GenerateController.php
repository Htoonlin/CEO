<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 8/4/2015
 * Time: 2:06 PM
 */

namespace Development\Controller;


use Application\DataAccess\ConstantDataAccess;
use Core\SundewController;
use Development\DataAccess\GenerateDataAccess;
use Development\Helper\GenerateHelper;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Development\Helper\Generator\EntityGenerator;
use Development\Helper\Generator\HelperGenerator;
use Development\Helper\Generator\GatewayGenerator;
use Development\Helper\Generator\ControllerGenerator;
use Development\Helper\Generator\SundewGenerator;

class GenerateController extends SundewController
{
    /**
     * @return GenerateDataAccess
     */
    private function getDbMeta()
    {
        return new GenerateDataAccess($this->getDbAdapter());
    }

    /**
     * @return array
     */
    private function getTableList()
    {
        $dataAccess = $this->getDbMeta();
        $tables = array();
        foreach($dataAccess->getTableNames() as $tbl){
            $tables[$tbl] = $tbl;
        }
        return $tables;
    }

    /**
     * @return array
     */
    private function getTypeList()
    {
        $dataAccess = new ConstantDataAccess($this->getDbAdapter());
        return $dataAccess->getComboByName('generate_types');
    }

    /**
     * @return array
     */
    private function getModuleList()
    {
        $manager = $this->getServiceLocator()->get('ModuleManager');
        $result = array();
        foreach($manager->getLoadedModules() as $key=>$value){
            $result[$key] = $key;
        }
        return $result;
    }

   /**
    * @return SundewGenerator
    */
    private function getGenerator()
    {
        $type = $this->params()->fromPost('type', '');
        $tblName = $this->params()->fromPost('tbl_name', '');
        $module = $this->params()->fromPost('module', '');
        $module = empty($module) ? 'Application' : $module;

        $generator = null;
        switch($type){
            case "E":
                $generator = new EntityGenerator($this->getDbMeta(), $tblName,
                $module, $this->getCurrentStaff());
                break;
            case "H":
                $generator = new HelperGenerator($this->getDbMeta(), $tblName,
                $module, $this->getCurrentStaff());
                break;
            case "D":
                $generator = new GatewayGenerator($this->getDbMeta(), $tblName,
                $module, $this->getCurrentStaff());
                break;
            case "C":
                $generator = new ControllerGenerator($this->getDbMeta(), $tblName,
                $module, $this->getCurrentStaff());
                break;
        }
        return $generator;
    }

    /**
     * @return bool|JsonModel
     */
    private function checkValidate()
    {
        $tblName = $this->params()->fromPost('tbl_name', '');
        $columns = $this->getDbMeta()->getColumnNames($tblName);
        if(empty($tblName) || empty($columns)){
            return new JsonModel(array(
                'status' => false,
                'request' => $this->params()->fromPost(),
                'message' => 'Invalid Table',
            ));
        }

        if(!$this->getRequest()->isPost()){
            return new JsonModel(array(
                'status' => false,
                'request' => $this->params()->fromPost(),
                'message' => 'Invalid Request',
            ));
        }

        return true;
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface|ViewModel
     */
    public function indexAction()
    {
        $helper = new GenerateHelper();
        $form = $helper->getForm($this->getTableList(), $this->getTypeList(), $this->getModuleList());
        $request = $this->getRequest();

        if($request->isPost()){
            $form->setData($request->getPost());
            $code = $this->params()->fromPost('txtGenerate', '');
            $generator = $this->getGenerator();
            if(!$generator){
                throw new \Exception("Invalid generator.");
            }
            $name = $generator->getClassName();
            $filename = 'attachement; filename="' . $name . '.php"';
            $response = $this->getResponse();
            $headers = $response->getHeaders();
            $headers->addHeaderLine('Content-Type', 'application/x-httpd-php; charset=UTF-8');
            $headers->addHeaderLine('Content-Disposition', $filename);
            $response->setContent($code);

            return $response;
        }

        return new ViewModel(array(
            'form' => $form
        ));
    }

    public function generateAction()
    {
        $isOK = $this->checkValidate();
        if($isOK != true){
            return $isOK;
        }

        $generator = $this->getGenerator();

        if($generator == null){
            return new JsonModel(array(
                'status' => false,
                'request' => $this->params()->fromPost(),
                'message' => 'Invalid Request',
            ));
        }

        return new JsonModel(array(
            'status' => true,
            'code' => $generator->generate(),
        ));
    }
}