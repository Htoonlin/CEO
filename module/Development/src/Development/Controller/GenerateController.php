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
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\PropertyGenerator;
use Zend\Filter\Word\CamelCaseToSeparator;
use Zend\Filter\Word\UnderscoreToCamelCase;
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
     * @param $className
     * @param $nameSpace
     * @return ClassGenerator
     */
    private function initClass($className, $nameSpace)
    {
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
        $class->setNamespaceName($nameSpace);

        return $class;
    }

    protected $typeNum = array('int', 'tinyint', 'smallint', 'mediumint', 'bigint');
    protected $typeFloat = array('decimal', 'float', 'double', 'real');
    protected $typeDate = array('date', 'datetime', 'timestamp');
    protected $typeString = array('char', 'varchar', 'tinytext', 'text', 'mediumtext', 'longtext');

    /**
     * @param $name
     * @param $type
     * @return string
     */
    private function createControl($name, $type, $length = 0, $isPrimary = false)
    {
        $isForeign = (!$isPrimary && strpos($name, 'Id') && $type == 'int');

        $toCamelCase = new UnderscoreToCamelCase();
        $var = lcfirst($toCamelCase->filter($name));
        $toSeperator = new CamelCaseToSeparator(array(" "));
        $label = $toSeperator->filter($toCamelCase->filter($name));

        $code = '';
        if($isPrimary){
            $code .= "\t\${$var} = new Element\\Hidden('{$name}');\n";
        }else if($isForeign){
            $code .= "\t\${$var} = new Element\\Select('{$name}');\n";
            $code .= "\t\${$var}->setAttribute('class', 'form-control');\n";
        }else if(in_array($type, $this->typeNum) || in_array($type, $this->typeFloat)){
            $code .= "\t\${$var} = new Element\\Number('{$name}');\n";
            if(in_array($type, $this->typeNum)){
                $code .= "\t\${$var}->setAttributes(array(\n";
                $code .= "\t\t'min' => '0',\n";
                $code .= "\t\t'max' => '99999999999',\n";
                $code .= "\t\t'step' => '1',\n";
                $code .= "\t));\n";
            }elseif(in_array($type, $this->typeNum)){
                $code .= "\t\${$var}->setAttributes(array(\n";
                $code .= "\t\t'min' => '0',\n";
                $code .= "\t\t'max' => '99999999999',\n";
                $code .= "\t\t'step' => '0.5',\n";
                $code .= "\t));\n";
            }
        }else if(in_array($type, $this->typeDate)){
            $code .= "\t\${$var} = new Element\\Date('{$name}');\n";
            $code .= "\t\${$var}->setAttributes(array(\n";
            $code .= "\t\t'allowPastDates' => true,\n";
            $code .= "\t\t'momentConfig' => array('format' => 'YYYY-MM-DD'),\n";
            $code .= "\t));\n";
        }else{
            if($length >= 500){
                $code .= "\t" . '$' . $var . " = new Element\\Textarea('{$name}');\n";
            }else{
                $code .= "\t" . '$' . $var . " = new Element\\Text('{$name}');\n";
            }
            $code .= "\t\${$var}->setAttribute('class', 'form-control');\n";
        }

        if(!$isPrimary){
            $code .= "\t\${$var}->setLabel('{$label}');\n";
        }
        $code .= "\t\$form->add(\${$var});\n";

        return $code;
    }

    /**
     *
     * @param unknown $name
     * @param unknown $type
     * @param unknown $isNull
     * @param number $length
     */
    private function createFilter($name, $type, $isNull, $length = 0)
    {
        $null = $isNull ? 'false' : 'true';

        $code = "\t\$filter->add(array(\n";
        $code .= "\t\t'name' => '{$name}',\n";
        $code .= "\t\t'required' => {$null},\n";

        if(in_array($type, $this->typeNum)){
            $code .= "\t\t'filters' => array(array('name' => 'Int')),\n";
        }else if(in_array($type, $this->typeFloat)){
            $code .= "\t\t'filters' => array(array('name' => 'Float')),\n";
        }else if(in_array($type, $this->typeString)){
            $code .= "\t\t'filters' => array(\n";
            $code .= "\t\t\tarray('name' => 'StripTags'),\n";
            $code .= "\t\t\tarray('name' => 'StringTirm'),\n";
            $code .= "\t\t),\n";
            $code .= "\t\t'validators' => array(\n";
            $code .= "\t\t\tarray(\n";
            $code .= "\t\t\t\t'name' => 'StringLength',\n";
            $code .= "\t\t\t\t'max' => {$length},\n";
            $code .= "\t\t\t\t'min' => 1,\n";
            $code .= "\t\t\t\t'encoding' => 'UTF-8',\n";
            $code .= "\t\t\t),\n";
            $code .= "\t\t),\n";
        }

        $code .= "\t));";
        return $code;
    }
    /**
     *
     * @param unknown $module
     * @param unknown $tblName
     * @param string $suffix
     * @return string
     */
    private function getClassName($module, $tblName, $suffix = ''){

        $toCamelCase = new UnderscoreToCamelCase();
        if($module == 'Application'){
            $name = $toCamelCase->filter(substr($tblName, 4));
        }else{
            $name = $toCamelCase->filter(explode('_', $tblName, 3)[2]);
        }

        return $name . $suffix;
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
            $type = $this->params()->fromPost('type', '');
            $tblName = $this->params()->fromPost('tbl_name', '');
            $module = $this->params()->fromPost('module', '');
            $code = $this->params()->fromPost('txtGenerate', '');
            $filename = '';
            $name = $this->getClassName($module, $tblName);
            if($type === 'E'){
                $filename = 'attachment; filename="' . $name . '.php"';
            }else if($type === 'H'){
                $filename = 'attachment; filename="' . $name . 'Helper.php"';
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

    public function gatewayAction()
    {
        $isOK = $this->checkValidate();
        if($isOK != true){
            return $isOK;
        }

        $tblName = $this->params()->fromPost('tbl_name', '');
        $module = $this->params()->fromPost('module', '');
        $module = empty($module) ? 'Application' : $module;

        $entity = $this->getClassName($module, $tblName);
        $className = $this->getClassName($module, $tblName, 'DataAccess');
        $nameSpace = $module . '\\DataAccess';
        $class = $this->initClass($className, $nameSpace);
        $class->addUse('Application\Service\SundewTableGateway');
        $class->addUse($module . '\\Entity\\' . $entity);
        $class->addUse('Zend\Db\Adapter\Adapter');
        $class->addUse('Zend\Db\ResultSet\HydratingResultSet');
        $class->addUse('Zend\Stdlib\Hydrator\ClassMethods');
        $class->setExtendedClass('SundewTableGateway');

        $constructorBody = '$this->table = "' . $tblName . '";' . PHP_EOL;
        $constructorBody .= '$this->adapter = $dbAdapter;' . PHP_EOL;
        $constructorBody .= '$this->resultSetPrototype = new HydratingResultSet(new ClassMethods(), new ' . $entity . '());' . PHP_EOL;
        $constructorBody .= '$this->initialize();';
        $constructor = MethodGenerator::fromArray(array(
            'name' => '__construct',
            'parameters' => array(
                array('type' => 'Adapter', 'name' => 'dbAdapter')
            ),
            'body' => $constructorBody,
        ));

        $fetchAllBody = 'if($paginated){' . PHP_EOL;
        $fetchAllBody .= "\t" . 'return $this->paginate($filter, $orderBy, $order);' . PHP_EOL;
        $fetchAllBody .= '}' . PHP_EOL;
        $fetchAllBody .= 'return $this->select();';
        $fetchAll = MethodGenerator::fromArray(array(
            'name' => 'fetchAll',
            'parameters' => array(
                array('name' => 'paginated', 'defaultvalue' => false),
                array('name' => 'filter', 'defaultvalue' => ''),
                array('name' => 'orderBy', 'defaultvalue' => ''),
                array('name' => 'order', 'defaultvalue' => ''),
            ),
            'body' => $fetchAllBody,
        ));

        $primaryKey = $this->getDbMeta()->getColumnNames($tblName)[0];
        $getRecordBody = '$id = (int)$id;' . PHP_EOL;
        $getRecordBody .= '$rowSet = $this->select(array("' . $primaryKey . '" => $id));' . PHP_EOL;
        $getRecordBody .= 'if($rowSet == null){' . PHP_EOL;
        $getRecordBody .= "\t throw new \\Exception('Invalid data');\n}\n";
        $getRecordBody .= 'return $rowSet->current();';
        $getRecord = MethodGenerator::fromArray(array(
            'name' => 'get' . $entity,
            'parameters' => array('id'),
            'body' => $getRecordBody,
        ));

        $saveRecordBody = '$id = $' . lcfirst($entity). '->get' . ucfirst($primaryKey) . '();' . PHP_EOL;
        $saveRecordBody .= '$data = $' . lcfirst($entity) . '->getArrayCopy();' . PHP_EOL;
        $saveRecordBody .= 'if($id > 0){' . PHP_EOL;
        $saveRecordBody .= "\t" . '$this->update($data, array("' . $primaryKey . '" => $id));' . PHP_EOL;
        $saveRecordBody .= "} else {\n";
        $saveRecordBody .= "\t" . 'unset($data["' . $primaryKey . '"]);' . PHP_EOL;
        $saveRecordBody .= "\t" . '$this->insert($data);' . PHP_EOL;
        $saveRecordBody .= "\t" . '$' . lcfirst($entity) . '->set' . ucfirst($primaryKey) . '($this->getLastInsertValue());' . PHP_EOL;
        $saveRecordBody .= "}" . PHP_EOL;
        $saveRecordBody .= 'return $' . lcfirst($entity) . ';';
        $saveRecord = MethodGenerator::fromArray(array(
            'name' => 'save' . $entity,
            'parameters' => array(
                array('type' => $entity, 'name' => lcfirst($entity)),
            ),
            'body' => $saveRecordBody,
        ));

        $deleteRecordBody = '$this->delete(array("' . $primaryKey . '" => (int)$id));';
        $deleteRecord = MethodGenerator::fromArray(array(
            'name' => 'delete' . $entity,
            'parameters' => array('id'),
            'body' => $deleteRecordBody
        ));

        $class->addMethods(array($constructor, $fetchAll, $getRecord, $saveRecord, $deleteRecord));
        $code = '<?php' . PHP_EOL . $class->generate();
        return new JsonModel(array(
            'status' => true,
            'code' => $code,
        ));
    }

    /**
     *
     * @return Ambigous <boolean, \Zend\View\Model\JsonModel>|\Zend\View\Model\JsonModel
     */
    public function helperAction()
    {
        $isOK = $this->checkValidate();
        if($isOK !== true){
            return $isOK;
        }

        $tblName = $this->params()->fromPost('tbl_name', '');
        $module = $this->params()->fromPost('module', '');
        $module = empty($module) ? 'Application' : $module;

        $className = $this->getClassName($module, $tblName, 'Helper');
        $nameSpace = $module . '\\Helper';

        $class = $this->initClass($className, $nameSpace);
        $class->addUse('Zend\Form\Element');
        $class->addUse('Zend\Form\Form');
        $class->addUse('Zend\InputFilter\InputFilter');

        $class->addProperties(array(
            array('dbAdapter', null, PropertyGenerator::FLAG_PROTECTED),
            array('form', null, PropertyGenerator::FLAG_PROTECTED),
            array('inputFilter', null, PropertyGenerator::FLAG_PROTECTED)
        ));

        $getForm = new MethodGenerator('getForm');
        $getFormCode = 'if(!$this->form){' . "\n\t" . '$form = new Form();' . PHP_EOL;

        $getFilter = new MethodGenerator('getInputFilter');
        $getFilterCode = 'if(!$this->inputFilter){' . "\n\t" . '$filter = new InputFilter();' . PHP_EOL;

        $columns = $this->getDbMeta()->getColumns($tblName);

        foreach($columns as $col)
        {
            $name = $col->getName();
            $type = $col->getDataType();
            $maxLength = $col->getCharacterMaximumLength();
            $primary = $this->getDbMeta()->isPrimary($tblName, $name);
            $isNull = $col->getIsNullable();
            $getFormCode .= $this->createControl($name, $type, $maxLength, $primary) . PHP_EOL;
            $getFilterCode .= $this->createFilter($name, $type, $isNull, $maxLength) . PHP_EOL;
        }

        $getFormCode .= "\t" . '$this->form = $form;';
        $getFormCode .= "\n}\n" . 'return $this->form;';
        $getForm->setBody($getFormCode);

        $getFilterCode .= "\t" . '$this->inputFilter = $filter;';
        $getFilterCode .= "\n}\n" . 'return $this->inputFilter;';
        $getFilter->setBody($getFilterCode);

        $setForm = new MethodGenerator('setForm');
        $setForm->setParameter(array('type' => 'Form', 'name' => 'form'));
        $setForm->setBody('$this->form = $form;');

        $setFilter = new MethodGenerator('setInputFilter');
        $setFilter->setParameter(array('type' => 'InputFilter', 'name' => 'filter'));
        $setFilter->setBody('$this->inputFilter = $filter;');


        $class->addMethods(array($getForm, $setForm, $getFilter, $setFilter));
        $code = '<?php' . PHP_EOL . $class->generate();
        return new JsonModel(array(
            'status' => true,
            'code' => $code,
        ));
    }


    /**
     * Entity Generator
     * @return JsonModel
     */
    public function entityAction()
    {
        $isOK = $this->checkValidate();
        if($isOK !== true){
            return $isOK;
        }

        $tblName = $this->params()->fromPost('tbl_name', '');
        $module = $this->params()->fromPost('module', '');
        $module = empty($module) ? 'Application' : $module;

        $className = $this->getClassName($module, $tblName);
        $nameSpace = $module . '\\Entity';

        $class = $this->initClass($className, $nameSpace);

        $class->addUse('Zend\Stdlib\ArraySerializableInterface');
        $class->setImplementedInterfaces(array('ArraySerializableInterface'));

        $columns = $this->getDbMeta()->getColumnNames($tblName);
        $exchangeBody = '';
        $getArrayBody = 'return array(' . PHP_EOL;
        $toCamelCase = new UnderscoreToCamelCase();
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