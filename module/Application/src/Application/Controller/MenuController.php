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
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class MenuController extends AbstractActionController{
    private function menuTable()
    {
        $sm = $this->getServiceLocator();
        $adapter = $sm->get('Sundew\Db\Adapter');
        $dataAccess = new MenuDataAccess($adapter);
        return $dataAccess;
    }

    private function urlTypeCombo()
    {
        $adapter = $this->getServiceLocator()->get('Sundew\Db\Adapter');
        $dataAccess = new ConstantDataAccess($adapter);
        return $dataAccess->getComboByName('routing_url_type');
    }
    private function roleTreeData()
    {
        $adapter = $this->getServiceLocator()->get('Sundew\Db\Adapter');
        $dataAccess = new RoleDataAccess($adapter);
        return $dataAccess->getChildren();
    }

    private function menuPermissionTable()
    {
        $adapter = $this->getServiceLocator()->get('Sundew\Db\Adapter');
        return new MenuPermissionDataAccess($adapter);
    }

    public function jsonAllAction()
    {
        $menus = $this->menuTable()->fetchAll();
        $data = array();
        foreach($menus as $menu){
            $data[] = array('menuId' => $menu->getMenuId(), 'name' => ($menu->getTitle() .' (' . $menu->getDescription() . ')'));
        }
        return new JsonModel($data);
    }

    public function indexAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
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
        $form->bind($menu);
        $request = $this->getRequest();
        if($request->isPost())
        {
            $isDelete = $request->getPost('is_delete', 'no');
            $db = $this->menuTable()->getAdapter();
            $conn = $db->getDriver()->getConnection();

            if($isDelete == 'yes' && $id > 0){
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
            }else{
                $post_data = $request->getPost();
                $form->setData($post_data);
                if($form->isValid()){
                    try{
                        $conn->beginTransaction();
                        $menuId = $this->menuTable()->saveMenu($menu)->getMenuId();
                        $grant_roles = isset($post_data['grant_roles']) ? $post_data['grant_roles'] : array();
                        $this->menuPermissionTable()->saveMenuPermission($menuId, $grant_roles);
                        $conn->commit();
                    }catch(\Exception $ex){
                        $conn->rollback();
                        $this->flashMessenger()->addErrorMessage($ex->getMessage());
                    }

                    $this->flashMessenger()->addSuccessMessage('Save successful!');
                    return $this->redirect()->toRoute("menu");
                }
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