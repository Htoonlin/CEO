<?php
/**
 * Created by PhpStorm.
 * User: july
 * Date: 4/28/2015
 * Time: 1:36 PM
 */

namespace CustomerRelation\DataAccess;

use CustomerRelation\Entity\Proposal;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Hydrator\ClassMethods;
class ProposalDataAccess extends AbstractTableGateway
{
    protected $staffId;
    public function __construct(Adapter $dbAdapter,$staffId)
    {
        $this->staffId=$staffId;
        $this->table="tbl_cr_proposal";
        $this->adapter=$dbAdapter;
        $this->resultSetPrototype=new HydratingResultSet(new ClassMethods(),new Proposal());
        $this->initialize();
    }
    public function getProposalView($id)
    {
        $select=new Select('vw_cr_proposal');
        $select->where(array('proposalId'=>$id,'proposalBy'=>$this->staffId));
        $statement=$this->sql->prepareStatementForSqlObject($select);
        $result=$statement->execute();
        return $result->current();
    }

   public function fetchAll($paginated = false, $filter ='', $orderBy= 'proposalDate', $order='ASC')
   {
    if($paginated){
        $select=new Select($this->table);
        $select->order($orderBy . ' ' . $order);
        $where=new Where();
        $where->literal("Concat_ws(' ', proposalId, code) LIKE ?", '%' . $filter . '%')
            ->and->equalTo('proposalBy',$this->staffId);
        $select->where($where);
        $paginatorAdapter=new DbSelect($select, $this->adapter);
        $paginator=new Paginator($paginatorAdapter);
        return $paginator;
    }
       $proposalView=new TableGateway($this->table, $this->adapter);
       return $proposalView->select(array('proposalBy'=>$this->staffId));
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

    public function getProposal($id)
    {
        $id=(int)$id;
        $rowset=$this->select(array('proposalId'=>$id,'proposalBy'=>$this->staffId));
        return $rowset->current();
    }
    public function saveProposal(Proposal $proposal)
    {
        $id=$proposal->getProposalId();
        $data=$proposal->getArrayCopy();
        $data['proposalBy']=$this->staffId;
        if(is_array($proposal->getProposalFile())){
            $data['proposalFile']=$proposal->getProposalFile()['tmp_name'];
        }
        if($id > 0){
            $this->update($data, array('proposalId'=>$id));
        }else{
            unset($data['proposalId']);
            $this->insert($data);
        }
        if(!$proposal->getProposalId())
        {
            $proposal->setProposalId($this->getLastInsertValue());
        }
        return $proposal;
    }
    public function deleteProposal($id)
    {
        $this->delete(array('proposalId'=>(int)$id));
    }
}