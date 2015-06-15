<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 1/19/2015
 * Time: 3:53 PM
 */

namespace Application\Entity;

use Application\Helper\Entity\TreeViewEntityInterface;
use Zend\Stdlib\ArraySerializableInterface;

class Role implements TreeViewEntityInterface, ArraySerializableInterface
{
    protected $id;
    public function getId(){return $this->id;}
    public function setId($id){$this->id = $id;}

    protected $name;
    public function getName(){return $this->name;}
    public function setName($name){$this->name = $name;}

    protected $description;
    public function getDescription(){return $this->description;}
    public function setDescription($description){$this->description = $description;}

    protected $parentId;
    public function getParentId(){return $this->parentId;}
    public function setParentId($value){$this->parentId = $value;}

    protected $status;
    public function getStatus(){return $this->status;}
    public function setStatus($value){$this->status = $value;}

    protected $icon;
    public function getIcon(){return $this->icon;}
    public function setIcon($value){$this->icon = $value;}

    protected $priority;
    public function getPriority(){return $this->priority;}
    public function setPriority($value){$this->priority = $value;}

    public function exchangeArray(array $data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->name = (!empty($data['name'])) ? $data['name'] : null;
        $this->description = (!empty($data['description'])) ? $data['description'] : null;
        $this->parentId = (!empty($data['parentId'])) ? $data['parentId'] : null;
        $this->status = (!empty($data['status'])) ? $data['status'] : null;
        $this->icon = (!empty($data['icon'])) ? $data['icon'] : null;
        $this->priority = (!empty($data['priority'])) ? $data['priority'] : null;
    }

    public function getArrayCopy()
    {
        return array(
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "parentId" => $this->parentId,
            "status" => ($this->status) ? $this->status : "A",
            "icon" => $this->icon,
            "priority" => $this->priority ? $this->priority : 0,
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
        return $this->name;
    }

    public function getUrl()
    {
        return "/role/index/" . $this->id;
    }

    public function getValue()
    {
        return $this->id;
    }
}