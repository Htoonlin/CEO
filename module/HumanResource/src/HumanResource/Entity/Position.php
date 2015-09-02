<?php
/**
 * Created by PhpStorm.
 * User: linn
 * Date: 3/5/2015
 * Time: 3:36 PM
 */

namespace HumanResource\Entity;


use Zend\Stdlib\ArraySerializableInterface;

class Position implements ArraySerializableInterface{

    protected $positionId;
    public function getPositionId(){return $this->positionId;}
    public function setPositionId($value){$this->positionId=$value;}

    protected $name;
    public function getName(){return $this->name;}
    public function setName($value){$this->name=$value;}

    protected $currencyId;
    public function getCurrencyId(){return $this->currencyId;}
    public function setCurrencyId($value){$this->currencyId = $value;}

    protected $minSalary;
    public function getMinSalary(){return $this->minSalary;}
    public function setMinSalary($value){$this->minSalary=$value;}

    protected $maxSalary;
    public function getMaxSalary(){return $this->maxSalary;}
    public function setMaxSalary($value){$this->maxSalary=$value;}

    protected $status;
    public function getStatus(){return $this->status;}
    public function setStatus($value){$this->status=$value;}

    public function exchangeArray(array $data)
    {
        $this->positionId = (!empty($data['positionId'])) ? $data['positionId'] : null;
        $this->name = (!empty($data['name'])) ? $data['name'] : null;
        $this->currencyId = (!empty($data['currencyId'])) ? $data['currencyId'] : null;
        $this->minSalary = (!empty($data['min_Salary'])) ? $data['min_Salary'] : null;
        $this->maxSalary = (!empty($data['max_Salary'])) ? $data['max_Salary'] : null;
        $this->status = (!empty($data['status'])) ? $data['status'] : false;
    }

    public function getArrayCopy()
    {
        return array(
            'positionId' => $this->positionId,
            'name' => $this->name,
            'currencyId' => $this->currencyId,
            'min_Salary' => $this->minSalary,
            'max_Salary' => $this->maxSalary,
            'status' => $this->status,
        );
    }
}