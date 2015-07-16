<?php
/**
 * Created by PhpStorm.
 * User: Sundew
 * Date: 5/25/2015
 * Time: 1:22 PM
 */

namespace ProjectManagement\DataAccess;

use Application\Service\SundewTableGateway;
use ProjectManagement\Entity\Project;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Hydrator\ClassMethods;

class ProjectDataAccess extends SundewTableGateway{
    public function __construct(Adapter $dbAdapter){
        $this->table="tbl_pm_project";
        $this->adapter=$dbAdapter;
        $this->resultSetPrototype=new HydratingResultSet(new ClassMethods(),new Project());
        $this->initialize();
    }

    public function fetchAll($paginated=false, $filter='',$orderBy='code',$order='ASC'){
        $view='vw_pm_project';

        if($paginated){
           return $this->paginate($filter, $orderBy, $order, $view);
        }

        return $this->select();
    }

    public function getComboData($key, $value){
        $results=$this->select();
        $selectData=array();
        foreach($results as $project){
            $data=$project->getArrayCopy();
            $selectData[$data[$key]]=$data[$value];
        }
        return $selectData;
    }

    public function getProject($id){
        $id=(int)$id;
        $rowSet=$this->select(array('projectId'=>$id));
        return $rowSet->current();
    }

    public function saveProject(Project $project){
        $id=$project->getProjectId();
        $data=$project->getArrayCopy();

        if($id>0){
            $this->update($data,array('projectId'=>$id));
        }
        else{
            unset($data['projectId']);
            $this->insert($data);
        }

        if(!$project->getProjectId()){
            $project->setProjectId($this->getLastInsertValue());
        }

        return $project;
    }

    public function deleteProject($id){
        $this->delete(array('projectId'=>(int)$id));
    }
}