<?php
/**
 * Created by PhpStorm.
 * User: Lwin
 * Date: 4/28/2015
 * Time: 1:39 PM
 */

namespace CustomerRelation\DataAccess;

use CustomerRelation\Entity\Company;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Db\Sql\Where;

class CompanyDataAccess extends AbstractTableGateway
{
    public function __construct(Adapter $dbAdapter)
    {
        $this->table='tbl_cr_company';
        $this->adapter=$dbAdapter;
        $this->resultSetPrototype=new HydratingResultSet(new ClassMethods(), new Company());
        $this->initialize();
    }

    public function fetchAll($paginated=false, $filter='', $orderBy='name', $order='ASC')
    {
        if($paginated){
            $select=new Select($this->table);
            $select->order($orderBy. ' '. $order);
            $where=new Where();
            $where->literal("Concat_ws(' ',name,phone,address,website) LIKE ?", '%'.$filter.'%');
            $select->where($where);
            $select->where($where);
            $paginatorAdapter=new DbSelect($select, $this->adapter);
            $paginator=new Paginator($paginatorAdapter);
            return $paginator;
        }
        return $this->select();

    }

    public function getComboData($key, $value)
    {
        $results=$this->select();
        $selectData=array();
        foreach($results as $company){
            $data=$company->getArrayCopy();
            $selectData[$data[$key]]=$data[$value];
        }
        return $selectData;
    }

    public function getCompany($id)
    {
        $id=(int)$id;
        $rowset=$this->select(array('companyId'=>$id));
        $row=$rowset->current();

        return $row;
    }

    public function saveCompany(Company $company)
    {
        $id=$company->getCompanyId();
        $data=$company->getArrayCopy();


        if($id>0){
            $this->update($data, array('companyId'=>$id));
        }else{
            unset($data['companyId']);
            $this->insert($data);
        }
        if(!$company->getCompanyId()){
            $company->setCompanyId($this->getLastInsertValue());
        }
        return $company;
    }

    public function deleteCompany($id)
    {
        $this->delete(array('companyId'=>(int)$id));
    }
}