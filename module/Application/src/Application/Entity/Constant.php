<?php
namespace Application\Entity;

use Zend\Stdlib\ArraySerializableInterface;

/**
 * System Generated Code
 *
 * User : Htoonlin
 * Date : 2015-08-04 18:52:44
 *
 * @package Application\Entity
 */
class Constant implements ArraySerializableInterface
{

    protected $constantId = null;

    protected $name = null;

    protected $value = null;

    protected $groupCode = null;

    public function getConstantId()
    {
        return $this->constantId;
    }

    public function setConstantId($value)
    {
        $this->constantId = $value;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($value)
    {
        $this->name = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getGroupCode()
    {
        return $this->groupCode;
    }

    public function setGroupCode($value)
    {
        $this->groupCode = $value;
    }

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
