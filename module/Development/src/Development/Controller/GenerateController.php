<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 8/4/2015
 * Time: 2:06 PM
 */

namespace Development\Controller;


use Application\DataAccess\ConstantDataAccess;
use Application\Service\SundewController;
use Development\DataAccess\GenerateDataAccess;
use Development\Helper\GenerateHelper;
use phpDocumentor\Reflection\DocBlock;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\PropertyGenerator;
use Zend\Filter\Word\UnderscoreToCamelCase;
use Zend\Form\Element\Select;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class GenerateController extends SundewController
{
    /**
     * @return GenerateDataAccess
     */
    private function getDbMeta()
    {
        return new GenerateDataAccess($this->getDbAdapter());
    }

    private function getTableList()
    {
        $dataAccess = $this->getDbMeta();
        $tables = array();
        foreach($dataAccess->getTableNames() as $tbl){
            $tables[$tbl] = $tbl;
        }
        return $tables;
    }

    private function getTypeList()
    {
        $dataAccess = new ConstantDataAccess($this->getDbAdapter());
        return $dataAccess->getComboByName('generate_types');
    }

    private function getModuleList()
    {
        $manager = $this->getServiceLocator()->get('ModuleManager');
        $result = array();
        foreach($manager->getLoadedModules() as $key=>$value){
            $result[$key] = $key;
        }
        return $result;
    }

    public function indexAction()
    {
        $helper = new GenerateHelper();
        $form = $helper->getForm($this->getTableList(), $this->getTypeList(), $this->getModuleList());
        $request = $this->getRequest();

        if($request->isPost()){
            $form->setData($request->getPost());
            $toCamelCase = new UnderscoreToCamelCase();
            $type = $this->params()->fromPost('type', '');
            $tblName = $this->params()->fromPost('tbl_name', '');
            $code = $this->params()->fromPost('txtGenerate', '');
            $filename = '';
            if($type === 'E'){
                $filename = 'attachment; filename="' . $toCamelCase->filter(substr($tblName, 4)) . '.php"';
            }

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

    public function entityAction()
    {
        $tblName = $this->params()->fromPost('tbl_name', '');
        $module = $this->params()->fromPost('module', '');
        $module = empty($module) ? 'Application' : $module;

        if(empty($tblName)){
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

        $toCamelCase = new UnderscoreToCamelCase();
        $className = $toCamelCase->filter(substr($tblName, 4));
        $nameSpace = $module . '\\Entity';

        $user = $this->getCurrentStaff()->getStaffName();
        $date = date('Y-m-d H:i:s', time());
        $class = new ClassGenerator($className);
        $class->setDocBlock(DocBlockGenerator::fromArray(array(
            'shortDescription' => 'System Generated Code',
            'longDescription' => "User : {$user}\nDate : {$date}",
            'tags' => array(
                array('name' => 'package', 'description' => $nameSpace)
            ),
        )));
        $class->addUse('Zend\Stdlib\ArraySerializableInterface');
        $class->setImplementedInterfaces(array('ArraySerializableInterface'));
        $class->setNamespaceName($nameSpace);

        $columns = $this->getDbMeta()->getColumnNames($tblName);
        $exchangeBody = '';
        $getArrayBody = 'return array(' . PHP_EOL;

        foreach($columns as $col)
        {
            $name = $toCamelCase->filter($col);
            $property = lcfirst($name);
            $class->addProperty($property, null, PropertyGenerator::FLAG_PROTECTED);
            $get = new MethodGenerator('get' . $name);
            $get->setBody('return $this->' .  $property . ';');
            $set = new MethodGenerator('set' . $name);
            $set->setParameter('value')
                ->setBody('$this->' . $property . ' = $value;');
            $class->addMethods(array($get, $set));
            $exchangeBody .= '$this->' . $property .
                ' = (!empty($data[\'' . $col . '\'])) ? $data[\'' .
                $col . '\'] : null;' . PHP_EOL;
            $getArrayBody .= "\t'" . $col . '\' => $this->' . $property . ',' . PHP_EOL;
        }
        $exchange = new MethodGenerator('exchangeArray');
        $exchange->setParameter(array(
            'type' => 'array',
            'name' => 'data'
        ));
        $exchange->setBody($exchangeBody);

        $getArray = new MethodGenerator('getArrayCopy');
        $getArray->setBody($getArrayBody . ');');

        $class->addMethods(array($exchange, $getArray));

        $code = '<?php' . PHP_EOL . $class->generate();
        return new JsonModel(array(
            'status' => true,
            'code' => $code,
        ));
    }
}