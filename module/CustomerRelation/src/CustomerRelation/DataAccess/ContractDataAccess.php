<?php
/**
 * Created by PhpStorm.
 * User: july
 * Date: 5/4/2015
 * Time: 2:36 PM
 */

namespace CustomerRelation\DataAccess;

use Core\SundewTableGateway;
use CustomerRelation\Entity\Contract;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Stdlib\Hydrator\ClassMethods;

/**
 * Class ContractDataAccess
 * @package CustomerRelation\DataAccess
 */
class ContractDataAccess extends SundewTableGateway
{
    /**
     * @param Adapter $dbAdapter
     * @param $userId
     */
    public function __construct(Adapter $dbAdapter,$userId)
    {
        $this->table="tbl_cr_contract";
        $this->adapter=$dbAdapter;
        $this->resultSetPrototype=new HydratingResultSet(new ClassMethods(),new Contract());
        $this->initialize();

        $this->useSoftDelete = true;
        parent::__construct($userId);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getContractView($id)
    {
        $select=new Select('vw_cr_contract');
        $select->where(array('contractId'=>$id));
        $statement=$this->sql->prepareStatementForSqlObject($select);
        $result=$statement->execute();
        return $result->current();
    }

    /**
     * @param bool $paginated
     * @param string $filter
     * @param string $orderBy
     * @param string $order
     * @return \Zend\Db\ResultSet\ResultSet
     * @throws \Exception
     */
    public function fetchAll($paginated = false, $filter ='', $orderBy= 'contractBy', $order='ASC')
    {
        $view = 'vw_cr_contract';
        if($paginated){
           return $this->paginate($filter, $orderBy, $order, $view);
        }
        $select = new Select($view);
        return $this->selectOther($select);
    }

    /**
     * @param $id
     * @return array|\ArrayObject|null
     */
    public function getContract($id)
    {
        $id=(int)$id;
        $rowset=$this->select(array('contractId'=>$id));
        return $rowset->current();
    }

    /**
     * @param Contract $contract
     * @return Contract
     */
    public function saveContract(Contract $contract)
    {
        $id = $contract->getContractId();
        $data = $contract->getArrayCopy();
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

    /**
     * @param $id
     */
    public function deleteContract($id)
    {
        $this->delete(array('contractId'=>(int)$id));
    }
}