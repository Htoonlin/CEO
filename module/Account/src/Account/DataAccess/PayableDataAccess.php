<?php
/**
 * Created by PhpStorm.
 * User: july
 * Date: 4/5/2015
 * Time: 5:02 AM
 */

namespace Account\DataAccess;
use Account\Entity\Payable;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Hydrator\ClassMethods;
class PayableDataAccess extends AbstractTableGateway
{
    protected $staffId;
    public function __construct(Adapter $dbAdapter,$staffId)
    {
        $this->staffId=$staffId;
        $this->table="tbl_account_payable";
        $this->adapter=$dbAdapter;
        $this->resultSetPrototype=new HydratingResultSet(new ClassMethods(),new Payable());
        $this->initialize();
    }
    public function getVoucherNo($date)
    {
        $select = new Select($this->table);
        $select->where(array('voucherDate' => $date));
        $select->columns(array(new Expression('max(voucherNo) as MaxVoucherNo')));
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $maxVoucherNo = $result->current()['MaxVoucherNo'];

        $voucherDate = strtotime($date);
        $number = ($maxVoucherNo == null)? 0 : substr($maxVoucherNo, -4);
        $number = (int)$number + 1;
        $generate = 'PV' . date('Ymd', $voucherDate) . sprintf('%04d', $number);

        return $generate;
    }

    public  function fetchAll($paginated=false,$filter='',$orderBy='voucherNo',$order='ASC')
    {
        $view='vw_account_payable';
        if($paginated){
            $select=new Select($view);
            $select->order($orderBy . ' ' . $order);
            $where=new Where();
            $where->literal("Concat_ws(' ',voucherNo,description, accountType) LIKE ?",'%'.$filter.'%')
                ->and->equalTo('withdrawBy',$this->staffId);
            $select->where($where);
            $paginatorAdapter=new DbSelect($select,$this->adapter);
            $paginator=new Paginator($paginatorAdapter);
            return $paginator;
        }
        $tableGateway=new TableGateway($view,$this->adapter);
        return $tableGateway->select(array('withdrawBy'=>$this->staffId));
    }
    public function getPayableView($id)
    {
        $select=new Select('vw_account_payable');
        $select->where(array('payVoucherId'=>$id,'withdrawBy'=>$this->staffId));
        $statement=$this->sql->prepareStatementForSqlObject($select);
        $result=$statement->execute();
        return $result->current();
    }
   public function getPayable($id, $withPermission = true)
   {
       $id=(int)$id;
       if($withPermission){
           $rowset=$this->select(array('payVoucherId'=>$id,'withdrawBy'=>$this->staffId));
       }else{
           $rowset=$this->select(array('payVoucherId'=>$id));
       }

       return $rowset->current();
   }
    public function savePayable(Payable $payable)
    {
        $id=$payable->getPayVoucherId();
        $data=$payable->getArrayCopy();

        if(empty($data['status'])){
            $data['status']='R';
            if(empty($data['requestedDate'])) {
                $data['requestedDate'] = date('Y-m-d H:i:s', time());
            }
            $data['withdrawBy'] = $this->staffId;
        }else if($data['status'] === 'A'){
            if(empty($data['approvedDate'])) {
                $data['approvedDate'] = date('Y-m-d H:i:s', time());
            }
            $data['approveBy'] = $this->staffId;
        }else if($data['status'] === 'C'){
            if(empty($data['approvedDate'])) {
                $data['approvedDate'] = date('Y-m-d H:i:s', time());
            }
            $data['approveBy'] = $this->staffId;
        }

        if($id>0){
            $this->update($data,array('payVoucherId'=>$id));
        }else{
            unset($data['payVoucherId']);
            $this->insert($data);
        }
        if(!$payable->getPayVoucherId())
        {
            $payable->setPayVoucherId($this->getLastInsertValue());
        }
        return $payable;
    }
}