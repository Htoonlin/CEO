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
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class RouteController extends AbstractActionController
{
    private function routeTable()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        return new RouteDataAccess($adapter);
    }

    private function routePermissionTable()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        return new RoutePermissionDataAccess($adapter);
    }

    private function roleTreeData()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $dataAccess = new RoleDataAccess($adapter);
        return $dataAccess->getChildren();
    }

    public function jsonAllAction()
    {
        $data = $this->routeTable()->fetchAll();
        return new JsonModel($data);
    }
    public function indexAction()
    {
        $page=(int)$this->params()->fromQuery('page',1);
        $sort=$this->params()->fromQuery('sort','name');
        $sortBy=$this->params()->fromQuery('by','asc');
        $filter = $this->params()->fromQuery('filter', '');

        $paginator=$this->routeTable()->fetchAll(true,$filter,$sort,$sortBy);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(10);
        return new ViewModel(array(
            'paginator'=>$paginator,
            'page'=>$page,
            'sort'=>$sort,
            'sortBy'=>$sortBy,
            'filter' => $filter,
        ));
    }
    public function detailAction()
    {
        $id=(int)$this->params()->fromRoute('id',0);
        $helper = new RouteHelper($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        $form = $helper->getForm();
        $route = $this->routeTable()->getRoute($id);
        $isEdit = true;
        if(!$route){
            $isEdit = false;
            $route = new Route();
        }
        $permissions = $this->routePermissionTable()->grantRoles($id);

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
                }catch(\Exception $ex){
                    $conn->rollback();
                    $this->flashMessenger()->addErrorMessage($ex->getMessage());
                }

                $this->flashMessenger()->addSuccessMessage('Save successful');
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
    public function exportAction()
    {
        $response = $this->getResponse();

        $excelObj = new \PHPExcel();
        $excelObj->setActiveSheetIndex(0);

        $sheet = $excelObj->getActiveSheet();

        $routes = $this->routeTable()->fetchAll(false);
        $columns = array();

        $excelColumn = "A";
        $start = 2;
        foreach($routes as $row)
        {
            $data = $row->getArrayCopy();
            if(count($columns) == 0){
                $columns = array_keys($data);
            }
            foreach($columns as $col){
                $cellId = $excelColumn . $start;
                $sheet->setCellValue($cellId, $data[$col]);
                $excelColumn++;
            }
            $start++;
            $excelColumn = "A";
        }
        foreach($columns as $col)
        {
            $cellId = $excelColumn . '1';
            $sheet->setCellValue($cellId, $col);
            $excelColumn++;
        }

        $excelWriter = \PHPExcel_IOFactory::createWriter($excelObj, 'Excel2007');
        ob_start();
        $excelWriter->save('php://output');
        $excelOutput = ob_get_clean();

        $filename = 'attachment; filename="Route-' . date('Ymdhms') . '.xlsx"';

        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/ms-excel; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', $filename);
        $response->setContent($excelOutput);

        return $response;
    }
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