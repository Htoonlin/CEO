<?php
namespace Core\Generator;

use Zend\Db\Metadata\Metadata;
use HumanResource\Entity\Staff;
use Zend\Filter\Word\UnderscoreToCamelCase;
use Zend\Code\Generator\PropertyGenerator;
use Zend\Code\Generator\MethodGenerator;
/**
 *
 * @author htoonlin
 *
 */
class EntityGenerator extends SundewGenerator
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
     * @see \Core\Generator\SundewGenerator::getClassName()
     */
    public function getClassName(){
        return parent::generateClass($this->module, $this->tbl_name);
    }

    /**
     * (non-PHPdoc)
     * @see \Core\Generator\SundewGenerator::generate()
     */
    public function generate()
    {
        $className = $this->generateClass($this->module, $this->tbl_name);
        $nameSpace = $this->module . '\\Entity';

        $class = $this->initClass($className, $nameSpace);

        $class->addUse('Zend\Stdlib\ArraySerializableInterface');
        $class->setImplementedInterfaces(array('ArraySerializableInterface'));

        $columns = $this->dbMeta->getColumnNames($this->tbl_name);
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

        return '<?php' . PHP_EOL . $class->generate();
    }
}

?>