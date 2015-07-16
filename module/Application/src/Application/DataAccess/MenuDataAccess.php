<?php
/**
 * Created by PhpStorm.
 * User: linn
 * Date: 3/5/2015
 * Time: 12:39 PM
 */

namespace Application\DataAccess;

use Application\Entity\Menu;
use Application\Service\SundewTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\View\Helper\EscapeHtml;

class MenuDataAccess extends SundewTableGateway
{
    public function __construct(Adapter $dbAdapter)
    {
        $this->table = 'tbl_menu';
        $this->adapter = $dbAdapter;
        $this->resultSetPrototype = new HydratingResultSet(new ClassMethods(), new Menu());
        $this->initialize();
    }


    public function fetchAll()
    {
        $results = $this->select();
        return $results;
    }

    public function getComboData($key,$value)
    {
        $results=$this->select();
        $selectData=array();
        foreach($results as $controller){
            $data=$controller>getArrayCopy();
            $selectData[$data[$key]]=$data[$value];
        }
        return $selectData;
    }

    public function getMenu($id)
    {
        $id=(int)$id;
        $rowset=$this->select(array('menuId'=>$id));
        $row=$rowset->current();
        if(!$row){
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getMenuList($parentId = null, $roleId = 0)
    {
        $results = $this->select(function(Select $select) use($parentId, $roleId){
            $select->join(array('mp' => 'tbl_menu_permission'), 'tbl_menu.menuId = mp.menuId')
                ->where(array('parentId' => $parentId, 'mp.roleId' => $roleId))->order(array('priority ASC'));
        });
        $menus = array();
        foreach($results as $menu){
            $nav = array();
            $nav['id'] = $menu->getMenuId();

            $pages = $this->getMenuList($menu->getMenuId(), $roleId);
            $caret = '';
            if(!empty($pages)){
                $nav['pages'] = $pages;
            }

            $icon = '';
            if($menu->getIcon()){
                $icon = '<span class="' . $menu->getIcon() . '"></span>&nbsp;';
            }

            $nav['title'] = $menu->getDescription();
            $nav['label'] = $icon . htmlspecialchars($menu->getTitle()) . $caret;
            $nav['order'] = $menu->getPriority();
            $nav['rel'] = array('divider' => $menu->getHasDivider());

            if($menu->getUrlType() == 'R'){
                $nav['route'] = $menu->getUrls();
            }else{
                $nav['uri'] = $menu->getUrls();
            }

            $menus[] = $nav;
        }
        return $menus;
    }

    public function getChildren($parentId = null, $parentName = "")
    {
        $results = $this->select(function (Select $select) use ($parentId){
            $select->where(array('parentId' => $parentId))->order(array('priority ASC'));
        });

        $resultList = array();
        foreach($results as $menu)
        {
            $children = $this->getChildren($menu->getMenuId(), $parentName);
            if(!empty($children)){
            $menu->setChildren($children);
            }
            $resultList[] = $menu;
        }
        return $resultList;
    }

    public function saveMenu(Menu $menu)
    {

        $id=$menu->getMenuId();
        $data=$menu->getArrayCopy();

        if($id>0){
            $this->update($data,array('menuId'=>$id));
        }else{
            unset($data['menuId']);
            $this->insert($data);
        }
        if(!$menu->getMenuId()){
            $menu->setMenuId($this->getLastInsertValue());
        }
        return $menu;
    }

    public function deleteMenu($id)
    {
        $results = $this->select(array("parentId" => $id));
        foreach($results as $menu){
            $this->deleteMenu($menu->getMenuId());
        }
        $this->delete(array('menuId' => (int)$id));
    }
}

