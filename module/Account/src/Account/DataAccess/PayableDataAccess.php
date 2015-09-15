<?php
/**
 * Created by PhpStorm.
 * User: july
 * Date: 4/5/2015
 * Time: 5:02 AM
 */

namespace Account\DataAccess;
use Account\Entity\Payable;
use Core\SundewTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Hydrator\ClassMethods;

/**
 * Class PayableDataAccess
 * @package Account\DataAccess
 */
class PayableDataAccess extends SundewTableGateway
{
    protected $staffId;

    /**
     * @param Adapter $dbAdapter
     * @param Int $staffId
     * @param Int $userId
     */
    public function __construct(Adapter $dbAdapter,$staffId, $userId)
    {
        $this->staffId=$staffId;
        $this->table="tbl_account_payable";
        $this->adapter=$dbAdapter;
        $this->resultSetPrototype=new HydratingResultSet(new ClassMethods(),new Payable());
        $this->initialize();

        $this->useSoftDelete = true;
        parent::__construct($userId);
    }

    /**
     * @param $date
     * @return string
     */
    public function getVoucherNo($date)
    {
        $select = new Select($this->table);
        $select->where(array('voucherDate' => $date));
        $select->columns(array(new Expression('max(voucherNo) as MaxVoucherNo')));
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $maxVoucherNo = $result->current()['MaxVoucherNo'];
        $voucherDate = strtotime($date);
        $number = ($maxVoucherNo == null)? 0 : substr($maxVoucherNo, -3);
        $number = (int)$number + 1;
        $generate = 'PV' . date('Ymd', $voucherDate) . sprintf('%03d', $number);
        return $generate;
    }

    /**
     * @param bool $paginated
     * @param string $filter
     * @param string $orderBy
     * @param string $order
     * @return \Zend\Db\ResultSet\ResultSet|Paginator
     */
    public  function fetchAll($paginated=false,$filter='',$orderBy='voucherNo',$order='ASC')
    {
        $view = 'vw_account_payable';
        $select = new Select($view);
        $select->order($orderBy . ' ' . $order);
        $where = new Where();
        $where->equalTo('withdrawBy', $this->staffId);

        if($paginated){
            $where->literal("concat_ws(' ',description, voucherNo, Type, amount, voucherDate, currencyCode) LIKE ?",'%'.$filter.'%');
            $select->where($where);
            return $this->paginateWith($select);
        }

        $select->where($where);
        return $this->selectOther($select);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getPayableView($id)
    {
        $select=new Select('vw_account_payable');
        $select->where(array('payVoucherId'=>$id,'withdrawBy'=>$this->staffId));
        $statement=$this->sql->prepareStatementForSqlObject($select);
        $result=$statement->execute();
        return $result->current();
    }

    /**
     * @param $id
     * @param bool $withPermission
     * @return array|\ArrayObject|null
     */
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

    /**
     * @param Payable $payable
     * @return Payable
     */
    public function savePayable(Payable $payable)
    {
        $id=$payable->getPayVoucherId();
        $data=$payable->getArrayCopy();
        if(is_array($payable->getAttachmentFile())){
            $data['attachmentFile'] = $payable->getAttachmentFile()['tmp_name'];
        }
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