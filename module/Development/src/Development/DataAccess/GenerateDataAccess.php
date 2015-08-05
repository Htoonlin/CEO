<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 8/4/2015
 * Time: 3:16 PM
 */

namespace Development\DataAccess;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Metadata\Metadata;

class GenerateDataAccess extends Metadata
{
    /**
     * @param Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter);
    }

    public function isPrimary($tableName, $columnName)
    {
        $constraints = $this->getConstraints($tableName);
        $result = false;
        foreach($constraints as $key){
            if($key->isPrimaryKey()){
                $result = in_array($columnName, $key->getColumns());
            }
            if($result) break;
        }
        return $result;
    }
}