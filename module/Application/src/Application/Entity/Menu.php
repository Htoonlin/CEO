<?php
/**
 * Created by PhpStorm.
 * User: linn
 * Date: 3/5/2015
 * Time: 11:09 AM
 */

namespace Application\Entity;

use Application\Helper\Entity\TreeViewEntityInterface;
use Zend\Stdlib\ArraySerializableInterface;


class Menu implements TreeViewEntityInterface, ArraySerializableInterface
{

    protected  $menuId;
    public function getMenuId(){return $this->menuId;}
    public function setMenuId($value){$this->menuId =$value;}

    protected $title;
    public function getTitle(){return $this->title;}
    public function setTitle($value){return $this->title=$value;}

    protected $description;
    public  function getDescription(){return $this->description;}
    public function  setDescription($value){$this->description=$value;}

    protected $icon;
    public function getIcon() {return $this->icon;}
    public function setIcon($value){$this->icon=$value;}

    protected $url;
    public function getUrls(){return $this->url;}
    public function setUrl($value){$this->url=$value;}

    protected $url_type;
    public function getUrlType(){return $this->url_type;}
    public function setUrlType($value){$this->url_type=$value;}

    protected  $parentId;
    public function getParentId(){return $this->parentId;}
    public function setParentId($value){$this->parentId=$value;}

    protected  $priority;
    public  function  getPriority(){return $this->priority;}
    public  function  setPriority($value){$this->priority=$value;}

    protected $hasDivider;
    public function getHasDivider(){return $this->hasDivider;}
    public function setHasDivider($value){$this->hasDivider = $value;}

    public function exchangeArray(array $data)
    {
        $this->menuId = (!empty($data['menuId'])) ? $data['menuId'] : null;
        $this->title = (!empty($data['title'])) ? $data['title'] : null;
        $this->description = (!empty($data['description'])) ? $data['description'] : null;
        $this->icon = (!empty($data['icon'])) ? $data['icon'] : null;
        $this->url = (!empty($data['url'])) ? $data['url'] : null;
        $this->url_type = (!empty($data['url_type'])) ? $data['url_type'] : null;
        $this->parentId = (!empty($data['parentId'])) ? $data['parentId'] : null;
        $this->priority = (!empty($data['priority'])) ? $data['priority'] : null;
        $this->hasDivider = (!empty($data['hasDivider'])) ? $data['hasDivider'] : false;
    }

    public function getArrayCopy()
    {
        return array(
            "menuId" => $this->menuId,
            "title" => $this->title,
            "description" => $this->description,
            "icon" => $this->icon,
            "url" => $this->url,
            "url_type" => $this->url_type,
            "parentId" => $this->parentId,
            "priority" => $this->priority ? $this->priority : 0,
            "hasDivider" => $this->hasDivider,
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
        return $this->title;
    }
    public function getUrl()
    {
        return "/menu/index/" . $this->menuId;
    }

    public function getValue()
    {
        return $this->menuId;
    }

}