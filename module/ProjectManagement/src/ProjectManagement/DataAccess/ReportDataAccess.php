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

/**
 * Class ReportDataAccess
 * @package ProjectManagement\DataAccess
 */
class ReportDataAccess extends SundewTableGateway
{
    /**
     * @param Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->table = 'vw_pm_task';
        $this->initialize();
    }

    /**
     * @param $projectId
     * @return Where
     */
    private function getWhereByProjectId($projectId){
        $where = new Where();
        if($projectId > 0){
            $where->equalTo('projectId', $projectId);
        }else if($projectId == 0 || $projectId == null){
            $where->isNull('projectId');
        }

        return $where;
    }

    /**
     * @param int $projectId
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getTime($projectId = -1)
    {
        $results = $this->select(function(Select $select) use ($projectId){
            $where = $this->getWhereByProjectId($projectId);
            $where->and->in('status', array('A', 'P'));
            $select->where($where)->order('toTime asc');
        });

        return $results;
    }

    /**
     * @param int $projectId
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getProgress($projectId = -1)
    {
        $results = $this->select(function(Select $select) use ($projectId){
            $select->columns(array('status', 'count' => new Expression('count(taskId)')))
                ->group(array('status'))
                ->where($this->getWhereByProjectId($projectId))
                ->order(new Expression("FIELD(`status`, 'A', 'P', 'F', 'C', 'L')"));
        });

        return $results;
    }

    /**
     * @param int $projectId
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getActiveStaffs($projectId = -1){
        $results = $this->select(function(Select $select) use ($projectId){
            $select->columns(array('staffId' => new Expression('Distinct staffId'), 'staffCode', 'staffName'))
                ->where($this->getWhereByProjectId($projectId));
        });

        return $results;
    }

    /**
     * @param $staffId
     * @param int $projectId
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getWorkload($staffId, $projectId = -1)
    {
        $results = $this->select(function(Select $select) use($staffId,$projectId){
            $where = $this->getWhereByProjectId($projectId);
            $where->and->equalTo('staffId', $staffId);
            $select->columns(array('status', 'workload' => new Expression('sum(level)')))
                ->group(array('status'))
                ->where($where)
                ->order(new Expression("FIELD(`status`, 'A', 'P', 'F', 'C', 'L')"));
        });

        return $results;
    }
}