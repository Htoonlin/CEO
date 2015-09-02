<?php
/**
 * Created by PhpStorm.
 * User: july
 * Date: 3/9/2015
 * Time: 3:43 PM
 */

namespace Application\Controller;

use Application\DataAccess\ConstantDataAccess;
use Application\DataAccess\MenuPermissionDataAccess;
use Application\DataAccess\RoleDataAccess;
use Application\Helper\MenuHelper;
use Application\Entity\Menu;
use Application\DataAccess\MenuDataAccess;
use Core\Model\ApiModel;
use Core\SundewController;
use Zend\View\Model\ViewModel;

class MenuController extends SundewController
{
    /**
     * @return MenuDataAccess
     */
    private function menuTable()
    {
        return new MenuDataAccess($this->getDbAdapter());
    }

    /**
     * @return array
     */
    private function urlTypeCombo()
    {
        $dataAccess = new ConstantDataAccess($this->getDbAdapter());
        return $dataAccess->getComboByName('routing_url_type');
    }

    /**
     * @return array
     */
    private function roleTreeData()
    {
        $dataAccess = new RoleDataAccess($this->getDbAdapter());
        return $dataAccess->getChildren();
    }

    /**
     * @return MenuPermissionDataAccess
     */
    private function menuPermissionTable()
    {
        return new MenuPermissionDataAccess($this->getDbAdapter());
    }

    /**
     * @return ApiModel
     */
    public function apiAllAction()
    {
        $menus = $this->menuTable()->fetchAll();
        $data = array();
        foreach($menus as $menu){
            $data[] = array('menuId' => $menu->getMenuId(), 'name' => ($menu->getTitle() .' (' . $menu->getDescription() . ')'));
        }
        return new ApiModel($data);
    }

    private function deleteMenu($id)
    {
        $db = $this->menuTable()->getAdapter();
        $conn = $db->getDriver()->getConnection();

        try{
            $conn->beginTransaction();
            $this->menuTable()->deleteMenu($id);
            $this->menuPermissionTable()->deleteRoles($id);
            $conn->commit();
        }catch(\Exception $ex){
            $conn->rollback();
            $this->flashMessenger()->addErrorMessage($ex->getMessage());
        }

        $this->flashMessenger()->addInfoMessage('Delete successful!');
        return $this->redirect()->toRoute("menu");
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     * @throws \Exception
     */
    public function indexAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $action = $this->params()->fromQuery('action', '');

        if($action == 'delete' && $id > 0){
            return $this->deleteMenu($id);
        }

        $parentMenu = new Menu();
        $edit = false;
        if($id > 0){
            $menu = $this->menuTable()->getMenu($id);

            if($menu->getParentId()){
                $parentMenu = $this->menuTable()->getMenu($menu->getParentId());
            }
            $edit = true;
        }else{
            $menu = new Menu();
        }

        $permissions = $this->menuPermissionTable()->grantRoles($id);

        $menus = $this->menuTable()->getChildren();
        $helper = new MenuHelper();
        $form = $helper->getForm($this->urlTypeCombo());

        if($action == 'clone'){
            $edit = false;
            $id = 0;
            $menu->setMenuId(0);
        }

        $form->bind($menu);
        $request = $this->getRequest();
        if($request->isPost())
        {
            $post_data = $request->getPost();
            $form->setData($post_data);
            if($form->isValid()){
                $db = $this->menuTable()->getAdapter();
                $conn = $db->getDriver()->getConnection();
                try{
                    $conn->beginTransaction();
                    $menuId = $this->menuTable()->saveMenu($menu)->getMenuId();
                    $grant_roles = isset($post_data['grant_roles']) ? $post_data['grant_roles'] : array();
                    $this->menuPermissionTable()->saveMenuPermission($menuId, $grant_roles);
                    $conn->commit();
                    $this->flashMessenger()->addSuccessMessage('Save successful!');
                }catch(\Exception $ex){
                    $conn->rollback();
                    $this->flashMessenger()->addErrorMessage($ex->getMessage());
                }
                return $this->redirect()->toRoute("menu");
            }
        }

        return new ViewModel(array(
            'id' => $id,
            'menu'=>$menu,
            'menus' => $menus,
            'form' => $form,
            'isEdit' => $edit,
            'parent' => $parentMenu,
            'roles' => $this->roleTreeData(),
            'permissions' => $permissions,
        ));
    }
}