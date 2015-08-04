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
}