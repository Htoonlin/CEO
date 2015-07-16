<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 7/16/2015
 * Time: 3:39 PM
 */

namespace Application\Service;

use Zend\Db\Metadata\Metadata;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Update;
use Zend\Db\Sql\Delete;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class SundewTableGateway extends AbstractTableGateway
{
    protected $executeUser;

    /**
     * @param string $filter
     * @param string $orderBy
     * @param string $order
     * @param string $source
     * @return Paginator
     * @throws \Exception
     */
    public function paginate($filter, $orderBy, $order, $source = '')
    {
        try{
            $metadata = new Metadata($this->adapter);

            if(empty($source)){
                $source = $this->table;
            }
            $columns = $metadata->getColumnNames($source);
            $select = new Select($source);
            $select->order($orderBy . ' ' . $order);

            if(!empty($filter)){
                $query = "CONCAT_WS(' '," . implode(',', $columns) . ') LIKE ?';
                $where = new Where();
                $where->literal($query, '%' . $filter . '%');
                $select->where($where);
            }

            $paginatorAdapter = new DbSelect($select, $this->adapter);
            return new Paginator($paginatorAdapter);

        }catch(\Exception $ex){
            throw $ex;
        }
    }

    /**
     * @param Select $select
     * @return Paginator
     */
    public function paginateWith(Select $select){
        $paginatorAdapter = new DbSelect($select, $this->adapter);
        return new Paginator($paginatorAdapter);
    }

    /**
     * Select
     *
     * @param Where|\Closure|string|array $where
     * @return ResultSet
     */
    public function select($where = null)
    {
        if (!$this->isInitialized) {
            $this->initialize();
        }

        $select = $this->sql->select();

        if ($where instanceof \Closure) {
            $where($select);
        } elseif ($where !== null) {
            $select->where($where);
        }

        return $this->selectWith($select);
    }

    /**
     * @param Select $select
     * @return null|ResultSetInterface
     * @throws \RuntimeException
     */
    public function selectWith(Select $select)
    {
        if (!$this->isInitialized) {
            $this->initialize();
        }
        return $this->executeSelect($select);
    }

    /**
     * Insert
     *
     * @param  array $set
     * @return int
     */
    public function insert($set)
    {
        if (!$this->isInitialized) {
            $this->initialize();
        }
        $insert = $this->sql->insert();
        $insert->values($set);
        return $this->executeInsert($insert);
    }

    /**
     * @param Insert $insert
     * @return mixed
     */
    public function insertWith(Insert $insert)
    {
        if (!$this->isInitialized) {
            $this->initialize();
        }
        return $this->executeInsert($insert);
    }

    /**
     * Update
     *
     * @param  array $set
     * @param  string|array|\Closure $where
     * @return int
     */
    public function update($set, $where = null)
    {
        if (!$this->isInitialized) {
            $this->initialize();
        }
        $sql = $this->sql;
        $update = $sql->update();
        $update->set($set);
        if ($where !== null) {
            $update->where($where);
        }
        return $this->executeUpdate($update);
    }


    /**
     * @param Update $update
     * @return mixed
     */
    public function updateWith(Update $update)
    {
        if (!$this->isInitialized) {
            $this->initialize();
        }
        return $this->executeUpdate($update);
    }

    /**
     * Delete
     *
     * @param  Where|\Closure|string|array $where
     * @return int
     */
    public function delete($where)
    {
        if (!$this->isInitialized) {
            $this->initialize();
        }
        $delete = $this->sql->delete();
        if ($where instanceof \Closure) {
            $where($delete);
        } else {
            $delete->where($where);
        }
        return $this->executeDelete($delete);
    }

    /**
     * @param Delete $delete
     * @return mixed
     */
    public function deleteWith(Delete $delete)
    {
        $this->initialize();
        return $this->executeDelete($delete);
    }
}