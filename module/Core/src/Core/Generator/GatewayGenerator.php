<?php
namespace Core\Helper\Generator;

use Zend\Db\Metadata\Metadata;
use HumanResource\Entity\Staff;
use Zend\Code\Generator\MethodGenerator;
/**
 *
 * @author htoonlin
 *
 */
class GatewayGenerator extends SundewGenerator
{

    /**
     *
     * @param Metadata $dbMeta
     *
     * @param string $tbl_name
     *
     * @param string $module
     *
     * @param Staff $staff
     *
     */
    public function __construct(Metadata $dbMeta, $tbl_name,
        $module, Staff $staff)
    {
        parent::__construct($dbMeta, $tbl_name, $module, $staff);
    }

    /**
     * (non-PHPdoc)
     * @see \Core\Helper\Generator\SundewGenerator::getClassName()
     */
    public function getClassName(){
        return parent::generateClass($this->module, $this->tbl_name, 'DataAccess');
    }

    public function generate()
    {
        $entity = $this->generateClass($this->module, $this->tbl_name);
        $className = $this->generateClass($this->module, $this->tbl_name, 'DataAccess');
        $nameSpace = $this->module . '\\DataAccess';
        $class = $this->initClass($className, $nameSpace);
        $class->addUse('Core\SundewTableGateway');
        $class->addUse($this->module . '\\Entity\\' . $entity);
        $class->addUse('Zend\Db\Adapter\Adapter');
        $class->addUse('Zend\Db\ResultSet\HydratingResultSet');
        $class->addUse('Zend\Stdlib\Hydrator\ClassMethods');
        $class->setExtendedClass('SundewTableGateway');

        $constructorBody = '$this->table = "' . $this->tbl_name . '";' . PHP_EOL;
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

        $primaryKey = $this->dbMeta->getColumnNames($this->tbl_name)[0];
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
        return '<?php' . PHP_EOL . $class->generate();
    }
}

?>