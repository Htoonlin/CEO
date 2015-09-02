<?php
namespace Core\Generator;

use Zend\Db\Metadata\Metadata;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\DocBlockGenerator;
use HumanResource\Entity\Staff;
use Zend\Code\Generator\GeneratorInterface;
use Zend\Filter\Word\UnderscoreToCamelCase;
/**
 *
 * @author htoonlin
 *
 */
abstract class SundewGenerator implements GeneratorInterface
{
    public abstract function generate();
    public abstract function getClassName();

    protected $typeNum = array('int', 'tinyint', 'smallint', 'mediumint', 'bigint');
    protected $typeFloat = array('decimal', 'float', 'double', 'real');
    protected $typeDate = array('date', 'datetime', 'timestamp');
    protected $typeString = array('char', 'varchar', 'tinytext', 'text', 'mediumtext', 'longtext');

    protected $dbMeta;
    protected $tbl_name;
    protected $module;
    protected $staff;

    /**
     *
     * @param Metadata $dbMeta
     * @param unknown $tbl_name
     * @param unknown $module
     * @param Staff $staff
     */
    function __construct(Metadata $dbMeta, $tbl_name, $module, Staff $staff)
    {
        $this->dbMeta = $dbMeta;
        $this->tbl_name = $tbl_name;
        $this->module = $module;
        $this->staff = $staff;
    }

    /**
     *
     * @param unknown $module
     * @param unknown $tblName
     * @param string $suffix
     * @return string
     */
    protected function generateClass($module, $tblName, $suffix = ''){

        $toCamelCase = new UnderscoreToCamelCase();
        if($module == 'Application'){
            $name = $toCamelCase->filter(substr($tblName, 4));
        }else{
            $name = $toCamelCase->filter(explode('_', $tblName, 3)[2]);
        }
        return $name . $suffix;
    }

    /**
     * @param $className
     * @param $nameSpace
     * @return ClassGenerator
     */
    protected function initClass($className, $nameSpace, array $addUses = array())
    {
        $user = $this->staff->getStaffName();
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

        foreach($addUses as $use){
            $class->addUse($use);
        }

        return $class;
    }
}

?>