<?php
namespace CustomerRelation\Entity;

use Zend\Stdlib\ArraySerializableInterface;
use Zend\Form\Annotation as Form;
/**
 * System Generated Code
 *
 * User : Khinmyatkyi
 * Date : 2015-08-18 09:52:32
 *
 * @package CustomerRelation\Entity
 */
class Payment implements ArraySerializableInterface
{

    protected $paymentId;
    public function getPaymentId(){return $this->paymentId;}
    public function setPaymentId($value){$this->paymentId=$value;}

    protected $contractId;
    public function getContractId(){return $this->contractId;}
    public function setContractId($value){$this->contractId=$value;}

    protected $type;
    public function getType(){return $this->type;}
    public function setType($value){$this->type=$value;}

    protected $amount;
    public function getAmount(){return $this->amount;}
    public function setAmount($value){$this->amount=$value;}

    protected $currencyId;
    public function getCurrencyId(){return $this->currencyId;}
    public function setCurrencyId($value){$this->currencyId=$value;}

    protected $paymentDate;
    public function getPaymentDate(){return $this->paymentDate;}
    public function setPaymentDate($value){$this->paymentDate=$value;}

    protected $status;
    public function getStatus(){return $this->status;}
    public function setStatus($value){$this->status=$value;}

    protected $staffId;
    public function getStaffId(){return $this->staffId;}
    public function setStaffId($value){$this->staffId=$value;}

    protected $contactId;
    public function getContactId(){return $this->contactId;}
    public function setContactId($value){$this->contactId=$value;}

    protected $remark;
    public function getRemark(){return $this->remark;}
    public function setRemark($value){$this->remark=$value;}

    public function exchangeArray(array $data)
    {
        $this->paymentId = (!empty($data['paymentId'])) ? $data['paymentId'] : null;
        $this->contractId = (!empty($data['contractId'])) ? $data['contractId'] : null;
        $this->type = (!empty($data['type'])) ? $data['type'] : null;
        $this->amount = (!empty($data['amount'])) ? $data['amount'] : null;
        $this->currencyId = (!empty($data['currencyId'])) ? $data['currencyId'] : null;
        $this->paymentDate = (!empty($data['paymentDate'])) ? $data['paymentDate'] : null;
        $this->status = (!empty($data['status'])) ? $data['status'] : null;
        $this->staffId = (!empty($data['staffId'])) ? $data['staffId'] : null;
        $this->contactId = (!empty($data['contactId'])) ? $data['contactId'] : null;
        $this->remark = (!empty($data['remark'])) ? $data['remark'] : null;
    }

    public function getArrayCopy()
    {
        return array(
            'paymentId' => $this->paymentId,
            'contractId' => $this->contractId,
            'type' => $this->type,
            'amount' => $this->amount,
            'currencyId' => $this->currencyId,
            'paymentDate' => $this->paymentDate,
            'status' => $this->status,
            'staffId' => $this->staffId,
            'contactId' => $this->contactId,
            'remark' => $this->remark,
        );
    }


}