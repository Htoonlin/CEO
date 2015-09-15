<?php
/**
 * Created by PhpStorm.
 * User: july
 * Date: 4/28/2015
 * Time: 1:36 PM
 */

namespace CustomerRelation\DataAccess;

use Core\SundewTableGateway;
use CustomerRelation\Entity\Proposal;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Stdlib\Hydrator\ClassMethods;

/**
 * Class ProposalDataAccess
 * @package CustomerRelation\DataAccess
 */
class ProposalDataAccess extends SundewTableGateway
{
    /**
     * @param Adapter $dbAdapter
     * @param Int $userId
     */
    public function __construct(Adapter $dbAdapter,$userId)
    {
        $this->table="tbl_cr_proposal";
        $this->adapter=$dbAdapter;
        $this->resultSetPrototype=new HydratingResultSet(new ClassMethods(),new Proposal());
        $this->initialize();

        $this->useSoftDelete = true;
        parent::__construct($userId);
    }

    /**
     * @param bool $paginated
     * @param string $filter
     * @param string $orderBy
     * @param string $order
     * @return \Zend\Db\ResultSet\ResultSet|\Zend\Paginator\Paginator
     * @throws \Exception
     */
    public function fetchAll($paginated = false, $filter ='', $orderBy= 'proposalDate', $order='ASC')
    {
        $view = 'vw_cr_proposal';
        if($paginated){
           return $this->paginate($filter, $orderBy, $order, $view);
        }
        $select = new Select($view);
        return $this->selectOther($view);
    }

    /**
     * @param $id
     * @return array|\ArrayObject|null
     */
    public function getProposal($id)
    {
        $id=(int)$id;
        $rowSet=$this->select(array('proposalId'=>$id));
        return $rowSet->current();
    }

    /**
     * @param Proposal $proposal
     * @return Proposal
     */
    public function saveProposal(Proposal $proposal)
    {
        $id=$proposal->getProposalId();
        $data=$proposal->getArrayCopy();
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

    /**
     * @param $id
     */
    public function deleteProposal($id)
    {
        $this->delete(array('proposalId'=>(int)$id));
    }
}