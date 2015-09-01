<?php
/**
 * Created by PhpStorm.
 * User: july
 * Date: 3/5/2015
 * Time: 11:33 AM
 */

namespace Account\Entity;


use Zend\Stdlib\ArraySerializableInterface;
use Zend\Form\Annotation;

class Receivable implements ArraySerializableInterface
{
    protected $receiveVoucherId;
    public function getReceiveVoucherId(){return $this->receiveVoucherId;}
    public function setReceiveVoucherId($value){$this->receiveVoucherId=$value;}
    
    protected $voucherNo;
    public function getVoucherNo(){return $this->voucherNo;}
    public function setVoucherNo($value){$this->voucherNo=$value;}

    protected $voucherDate;
    public function getVoucherDate(){return $this->voucherDate;}
    public function setVoucherDate($value){$this->voucherDate=$value;}

    protected $accountType;
    public function getAccountType(){return $this->accountType;}
    public function setAccountType($value){$this->accountType=$value;}

    protected $description;
    public function getDescription(){return $this->description;}
    public function setDescription($value){$this->description=$value;}

    protected $amount;
    public function getAmount(){return $this->amount;}
    public function setAmount($value){$this->amount=$value;}

    protected $paymentType;
    public function getPaymentType(){return $this->paymentType;}
    public function setPaymentType($value){$this->paymentType=$value;}

    protected $attachmentFile;
    public function getAttachmentFile(){return $this->attachmentFile;}
    public function setAttachmentFile($value){$this->attachmentFile=$value;}

    protected $currencyId;
    public function getCurrencyId(){return $this->currencyId;}
    public function setCurrencyId($value){$this->currencyId = $value;}

    protected $depositBy;
    public function getDepositBy(){return $this->depositBy;}
    public function setDepositBy($value){$this->depositBy=$value;}

    protected $approveBy;
    public function getApproveBy(){return $this->approveBy;}
    public function setApproveBy($value){$this->approveBy=$value;}

    protected $status;
    public function getStatus(){return $this->status;}
    public function setStatus($value){$this->status=$value;}

    protected $approvedDate;
    public function getApprovedDate(){return $this->approvedDate;}
    public function setApprovedDate($value){$this->approvedDate=$value;}

    protected $reason;
    public function getReason(){return $this->reason;}
    public function setReason($value){$this->reason=$value;}

    protected $requestedDate;
    public function getRequestedDate(){return $this->requestedDate;}
    public function setRequestedDate($value){$this->requestedDate=$value;}

    protected $group_code;
    public function getGroup_code(){return $this->group_code;}
    public function setGroup_code($value){$this->group_code=$value;}

    public function exchangeArray(array $data)
    {
        $this->receiveVoucherId = (!empty($data['receiveVoucherId'])) ? $data['receiveVoucherId'] : null;
        $this->voucherNo = (!empty($data['voucherNo'])) ? $data['voucherNo'] : null;
        $this->voucherDate = (!empty($data['voucherDate'])) ? $data['voucherDate'] : null;
        $this->accountType = (!empty($data['accountType'])) ? $data['accountType'] : null;
        $this->description = (!empty($data['description'])) ? $data['description'] : null;
        $this->amount = (!empty($data['amount'])) ? $data['amount'] : null;
        $this->paymentType = (!empty($data['paymentType'])) ? $data['paymentType'] : '-';
        $this->attachmentFile = (!empty($data['attachmentFile'])) ? $data['attachmentFile'] : null;
        $this->currencyId = (!empty($data['currencyId'])) ? $data['currencyId'] : 0;
        $this->depositBy = (!empty($data['depositBy'])) ? $data['depositBy'] : null;
        $this->approveBy = (!empty($data['approveBy'])) ? $data['approveBy'] : null;
        $this->status = (!empty($data['status'])) ? $data['status'] : null;
        $this->approvedDate = (!empty($data['approvedDate'])) ? $data['approvedDate'] : null;
        $this->reason = (!empty($data['reason'])) ? $data['reason'] : null;
        $this->requestedDate = (!empty($data['requestedDate'])) ? $data['requestedDate'] : null;
        $this->group_code = (!empty($data['group_code'])) ? $data['group_code'] : null;
    }

    public function getArrayCopy()
    {
        return array(
            "receiveVoucherId" => $this->receiveVoucherId,
            "voucherNo" => $this->voucherNo,
            "voucherDate" => $this->voucherDate,
            "accountType" => $this->accountType,
            "description" => $this->description,
            "amount" => $this->amount,
            "paymentType" => $this->paymentType,
            "attachmentFile" => $this->attachmentFile,
            "currencyId" => $this->currencyId,
            "depositBy" => $this->depositBy,
            "approveBy" => $this->approveBy,
            "status" => $this->status,
            "approvedDate" => $this->approvedDate,
            "reason" => $this->reason,
            "requestedDate" => $this->requestedDate,
            "group_code" => $this->group_code,

        );
    }
}