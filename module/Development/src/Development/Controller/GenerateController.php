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
    /**
     * @return Select
     */
    private function getTableList()
    {
        $dataAccess = $this->getDbMeta();

        $tables = array();
        foreach($dataAccess->getTableNames() as $tbl){
            $tables[$tbl] = $tbl;
        }

        $cboTable = new Select('tbl_name');
        $cboTable->setAttributes(array('class' => 'form-control'))
            ->setValueOptions($tables)
            ->setEmptyOption("-- Choose Table --");

        return $cboTable;
    }

    /**
     * @return Select
     */
    private function getGenerateType()
    {
        $dataAccess = new ConstantDataAccess($this->getDbAdapter());
        $cboGenerate = new Select('type');
        $cboGenerate->setAttribute('class', 'form-control')
            ->setValueOptions($dataAccess->getComboByName('generate_types '))
            ->setEmptyOption('-- Choose Type --');
        return $cboGenerate;
    }


    public function indexAction()
    {
        $request = $this->getRequest();

        return new ViewModel(array(
            'cboTable' => $this->getTableList(),
            'cboGenerate' => $this->getGenerateType(),
        ));
    }

    public function entityAction()
    {
        $tblName = $this->params()->fromPost('tbl_name', '');
        $module = $this->params()->fromPost('txtModule', '');
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