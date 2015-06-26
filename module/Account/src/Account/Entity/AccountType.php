<?php
/**
 * Created by PhpStorm.
 * User: july
 * Date: 3/5/2015
 * Time: 11:41 AM
 */

namespace Account\Entity;

use Application\Helper\Entity\TreeViewEntityInterface;
use Zend\Stdlib\ArraySerializableInterface;

class AccountType implements TreeViewEntityInterface, ArraySerializableInterface
{

    protected $accountTypeId;
    public function getAccountTypeId(){return $this->accountTypeId;}
    public function setAccountTypeId($value){$this->accountTypeId=$value;}

    protected $code;
    public function getCode(){return $this->code;}
    public function setCode($value){$this->code=$value;}

    protected $name;
    public function getName(){return $this->name;}
    public function setName($value){$this->name=$value;}

    protected $parentTypeId;
    public function getParentTypeId(){return $this->parentTypeId;}
    public function setParentTypeId($value){$this->parentTypeId=$value;}

    protected $baseType;
    public function getBaseType(){return $this->baseType;}
    public function setBaseType($value){$this->baseType=$value;}

    protected $status;
    public function getStatus(){return $this->status;}
    public function setStatus($value){ $this->status=$value;}

    protected $icon;
    public function getIcon(){return $this->icon;}
    public function setIcon($value){$this->icon=$value;}

    public function exchangeArray(array $data)
    {
        $this->accountTypeId = (!empty($data['accountTypeId'])) ? $data['accountTypeId'] : null;
        $this->code = (!empty($data['code'])) ? $data['code'] : null;
        $this->name = (!empty($data['name'])) ? $data['name'] : null;
        $this->parentTypeId = (!empty($data['parentTypeId'])) ? $data['parentTypeId'] : null;
        $this->baseType = (!empty($data['baseType'])) ? $data['baseType'] : null;
        $this->status = (!empty($data['status'])) ? $data['status'] : null;

    }

    public function getArrayCopy()
    {
        return array(
            "accountTypeId" => $this->accountTypeId,
            "code" => $this->code,
            "name" => $this->name,
            "parentTypeId" => $this->parentTypeId,
            "baseType" => $this->baseType,
            "status" => $this->status,

        );
    }

    private $children;
    public function getChildren()
    {
        return $this->children;
    }
    public function setChildren($children)
    {
        $this->children = $children;
    }
    public function hasChildren()
    {
        return count($this->children) > 0;
    }

    public function getIconClass()
    {
        $icon = ($this->icon) ? $this->icon : 'glyphicon glyphicon-file';
        return ($this->hasChildren() ? 'glyphicon glyphicon-folder-open' : $icon);
    }

    public function getLabel()
    {
        return $this->name . '(' . $this->baseType . ')';
    }

    public function getUrl()
    {
        return "/account/type/index/" . $this->accountTypeId;
    }

    public function getValue()
    {
        return $this->accountTypeId;
    }
}