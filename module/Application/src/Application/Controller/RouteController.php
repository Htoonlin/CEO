<?php
/**
 * Created by PhpStorm.
 * User: linn
 * Date: 3/19/2015
 * Time: 3:20 PM
 */

namespace Application\Controller;

use Application\DataAccess\ControllerDataAccess;
use Application\DataAccess\RoleDataAccess;
use Application\DataAccess\RouteDataAccess;
use Application\DataAccess\RoutePermissionDataAccess;
use Application\Entity\Route;
use Application\Helper\RouteHelper;
use Application\Service\SundewController;
use Application\Service\SundewExporting;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class RouteController extends SundewController
{
    /**
     * @return RouteDataAccess
     */
    private function routeTable()
    {
        return new RouteDataAccess($this->getDbAdapter());
    }

    private function getControllerList()
    {
        $configManager = $this->getServiceLocator()->get('ConfigManager');
        return $configManager->get('controllers')['invokables'];
    }

    /**
     * @return RoutePermissionDataAccess
     */
    private function routePermissionTable()
    {
        return new RoutePermissionDataAccess($this->getDbAdapter());
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
     * @return JsonModel
     */
    public function jsonAllAction()
    {
        $data = $this->routeTable()->fetchAll();
        return new JsonModel($data);
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $page = (int)$this->params()->fromQuery('page',1);
        $sort = $this->params()->fromQuery('sort','name');
        $sortBy = $this->params()->fromQuery('by','asc');
        $filter = $this->params()->fromQuery('filter', '');
        $pageSize = (int)$this->params()->fromQuery('size', 10);

        $paginator=$this->routeTable()->fetchAll(true,$filter,$sort,$sortBy);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($pageSize);

        return new ViewModel(array(
            'paginator'=>$paginator,
            'sort'=>$sort,
            'sortBy'=>$sortBy,
            'filter' => $filter,
        ));
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function detailAction()
    {
        $id=(int)$this->params()->fromRoute('id',0);
        $action = $this->params()->fromQuery('action', '');

        $helper = new RouteHelper($this->getDbAdapter());
        $form = $helper->getForm($this->getControllerList());
        $route = $this->routeTable()->getRoute($id);
        $isEdit = true;
        if(!$route){
            $isEdit = false;
            $route = new Route();
        }
        $permissions = $this->routePermissionTable()->grantRoles($id);

        if($action == 'clone'){
            $isEdit = false;
            $id = 0;
            $route->setRouteId(0);
        }

        $form->bind($route);
        $request=$this->getRequest();
        if($request->isPost()){
            $post_data = $request->getPost()->toArray();
            $form->setData($post_data);
            $form->setInputFilter($helper->getInputFilter($id));
            if($form->isValid()){
                $db = $this->routeTable()->getAdapter();
                $conn = $db->getDriver()->getConnection();
                try{
                    $conn->beginTransaction();
                    $routeId = $this->routeTable()->saveRoute($route)->getRouteId();
                    $grant_roles = isset($post_data['grant_roles']) ? $post_data['grant_roles'] : array();
                    $this->routePermissionTable()->saveRoutePermission($routeId, $grant_roles);
                    $conn->commit();
                    $this->flashMessenger()->addSuccessMessage('Save successful');
                }catch(\Exception $ex){
                    $conn->rollback();
                    $this->flashMessenger()->addErrorMessage($ex->getMessage());
                }
                return $this->redirect()->toRoute('route');
            }
        }
        return new ViewModel(array(
            'form'=>$form,
            'id'=>$id,
            'isEdit'=>$isEdit,
            'roles' => $this->roleTreeData(),
            'permissions' => $permissions,
        ));
    }

    /**
     * @return \Zend\Http\Response
     */
    public function deleteAction()
    {
        $id=(int)$this->params()->fromRoute('id',0);
        $route=$this->routeTable()->getRoute($id);
        if($route){
            $db = $this->routeTable()->getAdapter();
            $conn = $db->getDriver()->getConnection();
            try{
                $conn->beginTransaction();
                $this->routeTable()->deleteRoute($id);
                $this->routePermissionTable()->deleteRoles($id);
                $conn->commit();
                $this->flashMessenger()->addInfoMessage('Delete successful');
            }catch (\Exception $ex){
                $conn->rollback();
                $this->flashMessenger()->addErrorMessage($ex->getMessage());
            }
        }
        return $this->redirect()->toRoute("route");
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function exportAction()
    {
        $export = new SundewExporting($this->routeTable()->fetchAll(false));
        $response = $this->getResponse();
        $filename = 'attachment; filename="Route-' . date('YmdHis') . '.xlsx"';

        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/ms-excel; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', $filename);
        $response->setContent($export->getExcel());

        return $response;
    }

    /**
     * @return JsonModel
     */
    public function jsonDeleteAction()
    {
        $data = $this->params()->fromPost('chkId',array());
        $message = "success";
        $db = $this->routeTable()->getAdapter();
        $conn = $db->getDriver()->getConnection();
        try{
            $conn->beginTransaction();
            foreach($data as $id){
                $this->routeTable()->deleteRoute($id);
                $this->routePermissionTable()->deleteRoles($id);
            }
            $conn->commit();
            $this->flashMessenger()->addInfoMessage("Delete successful");
        }catch (\Exception $ex){
            $conn->rollback();
            $message = $ex->getMessage();
        }

        return new JsonModel(array("message"=>$message));
    }

}