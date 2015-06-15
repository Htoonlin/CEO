<?php
/**
 * Created by PhpStorm.
 * User: july
 * Date: 5/4/2015
 * Time: 2:36 PM
 */

namespace CustomerRelation\Entity;

use Zend\Stdlib\ArraySerializableInterface;
use Zend\Form\Annotation as Form;
class Contract implements ArraySerializableInterface
{
    protected $contractId;
    public function getContractId(){return $this->contractId;}
    public function setContractId($value){$this->contractId=$value;}

    protected $companyId;
    public function getCompanyId(){return $this->companyId;}
    public function setCompanyId($value){$this->companyId=$value;}

    protected $contactId;
    public function getContactId(){return $this->contactId;}
    public function setContactId($value){$this->contactId=$value;}

    protected $projectId;
    public function getProjectId(){return $this->projectId;}
    public function setProjectId($value){$this->projectId=$value;}

    protected $code;
    public function getCode(){return $this->code;}
    public function setCode($value){$this->code=$value;}

    protected $amount;
    public function getAmount(){return $this->amount;}
    public function setAmount($value){$this->amount=$value;}

    protected $currencyId;
    public function getCurrencyId(){return $this->currencyId;}
    public function setCurrencyId($value){$this->currencyId=$value;}

    protected $contractFile;
    public function getContractFile(){return $this->contractFile;}
    public function setContractFile($value){$this->contractFile=$value;}

    protected $contractBy;
    public function getContractBy(){return $this->contractBy;}
    public function setContractBy($value){$this->contractId=$value;}

    protected $contractDate;
    public function getContractDate(){return $this->contractDate;}
    public function setContractDate($value){$this->contractDate=$value;}

    protected $notes;
    public function getNotes(){return $this->notes;}
    public function setNotes($value){$this->notes=$value;}

    protected $status;
    public function getStatus(){return $this->status;}
    public function setStatus($value){$this->status=$value;}

    public function exchangeArray(array $data)
    {
        $this->contractId=(!empty($data['contractId']))?$data['contractId']:null;
        $this->companyId=(!empty($data['companyId']))?$data['companyId']:null;
        $this->contactId=(!empty($data['contactId']))?$data['contactId']:null;
        $this->projectId=(!empty($data['projectId']))?$data['projectId']:null;
        $this->code=(!empty($data['code']))?$data['code']:null;
        $this->amount=(!empty($data['amount']))?$data['amount']:null;
        $this->currencyId=(!empty($data['currencyId']))?$data['currencyId']:null;
        $this->contractFile=(!empty($data['contractFile']))?$data['contractFile']:null;
        $this->contractBy=(!empty($data['contractBy']))?$data['contractBy']:null;
        $this->contractDate=(!empty($data['contractDate']))?$data['contractDate']:null;
        $this->notes=(!empty($data['notes']))?$data['notes']:null;
        $this->status=(!empty($data['status']))?$data['status']:null;
    }
    public function getArrayCopy()
    {
        return array(
            'contractId'=>$this->contractId,
            'companyId'=>$this->companyId,
            'contactId'=>$this->contactId,
            'projectId'=>$this->projectId,
            'code'=>$this->code,
            'amount'=>$this->amount,
            'currencyId'=>$this->currencyId,
            'contractFile'=>$this->contractFile,
            'contractBy'=>$this->contractBy,
            'contractDate'=>$this->contractDate,
            'notes'=>$this->notes,
            'status'=>$this->status,
        );
    }
}