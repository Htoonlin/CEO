<?php
/**
 * Created by PhpStorm.
 * User: linn
 * Date: 3/6/2015
 * Time: 8:55 AM
 */

namespace HumanResource\Entity;

use Application\Helper\Entity\TreeViewEntityInterface;
use Zend\Stdlib\ArraySerializableInterface;
class Department implements TreeViewEntityInterface, ArraySerializableInterface
{

    protected $departmentId;
    public function getDepartmentId(){return $this->departmentId;}
    public function setDepartmentId($value){$this->departmentId=$value;}

    protected $name;
    public function getName(){return $this->name;}
    public function setName($value){$this->name=$value;}

    protected $description;
    public function getDescription(){return $this->description;}
    public function setDescription($value){$this->description = $value;}

    protected $group_code;
    public function getTeamCode(){return $this->group_code;}
    public function setTeamCode($value){$this->group_code=$value;}

    protected $parentId;
    public function getParentId(){return $this->parentId;}
    public function setParentId($value){$this->parentId=$value;}

    protected $priority;
    public function getPriority(){return $this->priority;}
    public function setPriority($value){$this->priority=$value;}

    protected $status;
    public function getStatus(){return $this->status;}
    public function setStatus($value){$this->status=$value;}

    public function exchangeArray(array $data)
    {
        $this->departmentId=(!empty($data['departmentId']))?$data['departmentId']:null;
        $this->name=(!empty($data['name']))?$data['name']:null;
        $this->description=(!empty($data['description']))?$data['description']:null;
        $this->group_code=(!empty($data['group_code']))?$data['group_code']:null;
        $this->parentId=(!empty($data['parentId']))?$data['parentId']:null;
        $this->priority=(!empty($data['priority']))?$data['priority']:null;
        $this->status=(!empty($data['status']))?$data['status']:null;
    }
    public function getArrayCopy()
    {
        return array(
            "departmentId"=>$this->departmentId,
            "name"=>$this->name,
            "description"=>$this->description,
            "group_code"=>$this->group_code,
            "parentId"=>$this->parentId,
            "priority"=>$this->priority ? $this->priority:0,
            "status"=>($this->status)? $this->status:"A",
        );
    }
    private $children;
    public function getChildren()
    {
        return $this->children;
    }
    public function setChildren($children)
    {
        $this->children=$children;
    }
    public function hasChildren()
    {
        return count($this->children)>0;
    }
    public function getIconClass()
    {
        return $this->hasChildren() ?"glyphicon glyphicon-folder-open" : "glyphicon glyphicon-file";
    }
    public function getLabel()
    {
        return $this->name;
    }
    public function getUrl()
    {
        return "/hr/department/index/".$this->departmentId;
    }
    public function getValue()
    {
        return $this->departmentId;
    }
}