<?php
/**
 * Created by PhpStorm.
 * User: linn
 * Date: 3/5/2015
 * Time: 12:27 PM
 */

namespace Application\Entity;


use Zend\Stdlib\ArraySerializableInterface;
use Zend\Form\Annotation;

/**
 * Class Constant
 * @package Application\Entity
 * @Annotation\Name("constant")
 */

class Constant implements ArraySerializableInterface{

    protected $constantId;
    public function getConstantId(){return $this->constantId;}
    public function setConstantId($value){$this->constantId=$value;}


    protected $name;
    public function getName(){return $this->name;}
    public function setName($value){$this->name=$value;}


    protected $value;
    public function getValue(){return $this->value;}
    public function setValue($value){$this->value=$value;}

    protected $groupCode;
    public function getGroupCode(){return $this->groupCode;}
    public function setGroupCode($value){$this->groupCode=$value;}

    public function exchangeArray(array $data)
    {
        $this->constantId = (!empty($data['constantId'])) ? $data['constantId'] : null;
        $this->name = (!empty($data['name'])) ? $data['name'] : null;
        $this->value = (!empty($data['value'])) ? $data['value'] : null;
        $this->groupCode = (!empty($data['group_code'])) ? $data['group_code'] : null;
    }

    public function getArrayCopy()
    {
        return array(
            'constantId' => $this->constantId,
            'name' => $this->name,
            'value' => $this->value,
            'group_code' => $this->groupCode,
        );
    }
}