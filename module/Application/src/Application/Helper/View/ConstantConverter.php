<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 7/24/2015
 * Time: 4:44 PM
 */

namespace Application\Helper\View;


use Application\DataAccess\ConstantDataAccess;
use Zend\View\Helper\AbstractHelper;

class ConstantConverter extends AbstractHelper{
    protected $dataAccess;

    /**
     * @param ConstantDataAccess $constantDataAccess
     */
    public function __construct(ConstantDataAccess $constantDataAccess){
        $this->dataAccess = $constantDataAccess;
    }

    /**
     * @param $key
     * @param $constant
     * @return mixed
     */
    public function __invoke($key, $constant){
        $data = $this->dataAccess->getConstantByName($constant);

        if(!$data){
            return $key;
        }

        $constantValue = json_decode($data->getValue());

        return $constantValue->{$key};
    }
}