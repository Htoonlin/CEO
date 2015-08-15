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
use Zend\Paginator\Paginator;
use Zend\Stdlib\Hydrator\ClassMethods;

/**
 * Class ProjectDataAccess
 * @package ProjectManagement\DataAccess
 */
class ProjectDataAccess extends SundewTableGateway{

    /**
     * @param Adapter $dbAdapter
     */
    public function __construct(Adapter $dbAdapter){
        $this->table="tbl_pm_project";
        $this->adapter=$dbAdapter;
        $this->resultSetPrototype=new HydratingResultSet(new ClassMethods(),new Project());
        $this->initialize();
    }

    /**
     * @param bool $paginated
     * @param string $filter
     * @param string $orderBy
     * @param string $order
     * @return \Zend\Db\ResultSet\ResultSet|Paginator
     * @throws \Exception
     */
    public function fetchAll($paginated=false, $filter='',$orderBy='code',$order='ASC'){
        $view='vw_pm_project';

        if($paginated){
           return $this->paginate($filter, $orderBy, $order, $view);
        }

        return $this->select();
    }

    /**
     * @param $id
     * @return array|\ArrayObject|null
     * @throws \Exception
     */
    public function getProject($id){
        $id=(int)$id;
        $rowSet=$this->select(array('projectId'=>$id));
        if($rowSet == null){
            throw new \Exception('Invalid data.');
        }
        return $rowSet->current();
    }

    /**
     * @param Project $project
     * @return Project
     */
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

    /**
     * @param $id
     */
    public function deleteProject($id){
        $this->delete(array('projectId'=>(int)$id));
    }
}