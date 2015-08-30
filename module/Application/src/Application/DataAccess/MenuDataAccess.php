<?php
/**
 * Created by PhpStorm.
 * User: linn
 * Date: 3/5/2015
 * Time: 12:39 PM
 */

namespace Application\DataAccess;

use Application\Entity\Menu;
use Core\SundewTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Stdlib\Hydrator\ClassMethods;

/**
 * Class MenuDataAccess
 * @package Application\DataAccess
 */
class MenuDataAccess extends SundewTableGateway
{
    /**
     * @param Adapter $dbAdapter
     */
    public function __construct(Adapter $dbAdapter)
    {
        $this->table = 'tbl_menu';
        $this->adapter = $dbAdapter;
        $this->resultSetPrototype = new HydratingResultSet(new ClassMethods(), new Menu());
        $this->initialize();
    }

    /**
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function fetchAll()
    {
        $results = $this->select();
        return $results;
    }

    /**
     * @param $id
     * @return array|\ArrayObject|null
     * @throws \Exception
     */
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

    /**
     * @param null $parentId
     * @param array $roles
     * @return array
     */
    public function getMenuList($parentId = null, array $roles)
    {
        $results = $this->select(function(Select $select) use($parentId, $roles){
            try{
                $where = new Where();
                if(empty($parentId)){
                    $where->isNull('parentId');
                }else{
                    $where->equalTo('parentId', $parentId);
                }
                if(!empty($roles)){
                    $where->in('mp.roleId', $roles);
                }else{
                    $where->expression(' 1 = ?', 0);
                }

                $select->join(array('mp' => 'tbl_menu_permission'), 'tbl_menu.menuId = mp.menuId',
                    array('menuId'), Select::JOIN_INNER)
                    ->where($where)->quantifier(Select::QUANTIFIER_DISTINCT)->order(array('priority ASC'));
            }catch (\Exception $ex){
                throw $ex;
            }
        });

        $menus = array();
        foreach($results as $menu){
            $nav = array();
            $nav['id'] = $menu->getMenuId();

            $pages = $this->getMenuList($menu->getMenuId(), $roles);
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

    /**
     * @param null $parentId
     * @param string $parentName
     * @return array
     */
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

    /**
     * @param Menu $menu
     * @return Menu
     */
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

    /**
     * @param $id
     */
    public function deleteMenu($id)
    {
        $results = $this->select(array("parentId" => $id));
        foreach($results as $menu){
            $this->deleteMenu($menu->getMenuId());
        }
        $this->delete(array('menuId' => (int)$id));
    }
}

