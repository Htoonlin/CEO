<?php
/**
 * Created by PhpStorm.
 * User: july
 * Date: 3/5/2015
 * Time: 11:51 AM
 */

namespace Account\DataAccess;

use Account\Entity\Receivable;
use Core\SundewTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Hydrator\ClassMethods;

/**
 * Class ReceivableDataAccess
 * @package Account\DataAccess
 */
class ReceivableDataAccess extends SundewTableGateway
{
    protected $staffId;

    /**
     * @param Adapter $dbAdapter
     * @param Int $staffId
     * @param Int $userId
     */
    public function __construct(Adapter $dbAdapter, $staffId, $userId)
    {
        $this->staffId = $staffId;
        $this->table = "tbl_account_receivable";
        $this->adapter = $dbAdapter;
        $this->resultSetPrototype = new HydratingResultSet(new ClassMethods(), new Receivable());
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
        $generate = 'RV' . date('Ymd', $voucherDate) . sprintf('%03d', $number);

        return $generate;
    }

    /**
     * @param bool $paginated
     * @param string $filter
     * @param string $orderBy
     * @param string $order
     * @return \Zend\Db\ResultSet\ResultSet|Paginator
     */
    public function fetchAll($paginated=false,$filter='',$orderBy='voucherNo',$order='ASC')
    {
        $view = 'vw_account_receivable';
        $select = new Select($view);
        $select->order($orderBy . ' ' . $order);
        $where = new Where();
        $where->equalTo('depositBy', $this->staffId);

        if($paginated){
            $where->literal("concat_ws(' ',description, voucherNo, Type, amount, voucherDate, currencyCode) LIKE ?", '%' . $filter . '%');
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
    public function getReceivableView($id)
    {
        $select = new Select('vw_account_receivable');
        $select->where(array('receiveVoucherId' => $id, 'depositBy' => $this->staffId));
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    /**
     * @param $id
     * @param bool $withPermission
     * @return array|\ArrayObject|null
     */
    public function getReceivable($id, $withPermission = true)
    {
        $id=(int)$id;
        if($withPermission){
            $rowset = $this->select(array('receiveVoucherId'=>$id, 'depositBy'=> $this->staffId));
        }else{
            $rowset = $this->select(array('receiveVoucherId'=>$id));
        }
        return $rowset->current();
    }

    /**
     * @param Receivable $receivable
     * @return Receivable
     * @throws \Exception
     */
    public function saveReceivable(Receivable $receivable)
    {
        $id = $receivable->getReceiveVoucherId();
        $data = $receivable->getArrayCopy();
        if(is_array($receivable->getAttachmentFile())){
            $data['attachmentFile'] = $receivable->getAttachmentFile()['tmp_name'];
        }

        if(empty($data['status'])){
            $data['status']='R';
            if(empty($data['requestedDate'])) {
                $data['requestedDate'] = date('Y-m-d H:i:s', time());
            }
            $data['depositBy'] = $this->staffId;
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
        }else{
            throw new \Exception('Invalid Status');
        }

        if($id > 0){
            $this->update($data, array('receiveVoucherId' => $id));
        }else{
            unset($data['receiveVoucherId']);
            $this->insert($data);
        }

        if(!$receivable->getReceiveVoucherId())
        {
            $receivable->setReceiveVoucherId($this->getLastInsertValue());
        }

        return $receivable;
    }
}