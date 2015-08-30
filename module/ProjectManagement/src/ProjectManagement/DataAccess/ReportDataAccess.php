<?php
/**
 * Created by PhpStorm.
 * User: Htoonlin
 * Date: 2015-08-17
 * Time: 01:53 PM
 */

namespace ProjectManagement\DataAccess;


use Core\SundewTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class ReportDataAccess extends SundewTableGateway
{
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->table = 'vw_pm_task';
        $this->initialize();
    }

    private function getWhereByProjectId($projectId){
        $where = new Where();
        if($projectId > 0){
            $where->equalTo('projectId', $projectId);
        }else if($projectId == 0 || $projectId == null){
            $where->equalTo('projectId', 0)->or->isNull();
        }

        return $where;
    }

    public function getTime($projectId = -1)
    {
        $results = $this->select(function(Select $select) use ($projectId){
            $where = $this->getWhereByProjectId($projectId);
            $where->and->in('status', array('A', 'P'));
            $select->where($where)->order('toTime asc');
        });

        return $results;
    }

    public function getProgress($projectId = -1)
    {
        $results = $this->select(function(Select $select) use ($projectId){
            $select->columns(array('status', 'count' => new Expression('count(taskId)')))
                ->group(array('status'))
                ->where($this->getWhereByProjectId($projectId))
                ->order(new Expression("FIELD(`status`, 'A', 'P', 'F', 'C')"));
        });

        return $results;
    }

    public function getActiveStaffs($projectId = -1){
        $results = $this->select(function(Select $select) use ($projectId){
            $select->columns(array('staffId' => new Expression('Distinct staffId'), 'staffCode', 'staffName'))
                ->where($this->getWhereByProjectId($projectId));
        });

        return $results;
    }

    public function getWorkload($staffId, $projectId = -1)
    {
        $results = $this->select(function(Select $select) use($staffId,$projectId){
            $where = $this->getWhereByProjectId($projectId);
            $where->and->equalTo('staffId', $staffId);
            $select->columns(array('status', 'workload' => new Expression('sum(level)')))
                ->group(array('status'))
                ->where($where)
                ->order(new Expression("FIELD(`status`, 'A', 'P', 'F', 'C')"));
        });

        return $results;
    }
}