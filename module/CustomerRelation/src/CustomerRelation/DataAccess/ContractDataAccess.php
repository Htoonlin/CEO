<?php
/**
 * Created by PhpStorm.
 * User: july
 * Date: 5/4/2015
 * Time: 2:36 PM
 */

namespace CustomerRelation\DataAccess;

use Application\Service\SundewTableGateway;
use CustomerRelation\Entity\Contract;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Stdlib\Hydrator\ClassMethods;

class ContractDataAccess extends SundewTableGateway
{
    protected $staffId;
    public function __construct(Adapter $dbAdapter,$staffId)
    {
        $this->staffId=$staffId;
        $this->table="tbl_cr_contract";
        $this->adapter=$dbAdapter;
        $this->resultSetPrototype=new HydratingResultSet(new ClassMethods(),new Contract());
        $this->initialize();
    }
    public function getContractView($id)
    {
        $select=new Select('vw_cr_contract');
        $select->where(array('contractId'=>$id,'contractBy'=>$this->staffId));
        $statement=$this->sql->prepareStatementForSqlObject($select);
        $result=$statement->execute();
        return $result->current();
    }
    public function fetchAll($paginated = false, $filter ='', $orderBy= 'contractBy', $order='ASC')
    {
        if($paginated){
            $this->paginate($filter, $orderBy, $order);
        }
        $contractView=new TableGateway($this->table, $this->adapter);
        return $contractView->select(array('contract'=>$this->staffId));
    }
    public function getComboData($key, $value)
    {
        $results=$this->select();
        $selectData=array();
        foreach($results as $proposal){
            $data=$proposal->getArrayCopy();
            $selectData[$data[$key]]=$data[$value];
        }
        return $selectData;
    }

    public function getContract($id)
    {
        $id=(int)$id;
        $rowset=$this->select(array('contractId'=>$id,'contractBy'=>$this->staffId));
        return $rowset->current();
    }
    public function saveContract(Contract $contract)
    {
        $id = $contract->getContractId();
        $data = $contract->getArrayCopy();
        $data['contractBy']=$this->staffId;
        if(is_array($contract->getContractFile())){
            $data['contractFile'] = $contract->getContractFile()['tmp_name'];
        }

        if($id > 0){
            $this->update($data, array('contractId' => $id));
        }else{
            unset($data['contractId']);
            $this->insert($data);
        }

        if(!$contract->getContractId())
        {
            $contract->setContractId($this->getLastInsertValue());
        }

        return $contract;
    }
    public function deleteContract($id)
    {
        $this->delete(array('contractId'=>(int)$id));
    }
}