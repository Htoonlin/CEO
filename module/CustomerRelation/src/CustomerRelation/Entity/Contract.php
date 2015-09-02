<?php
namespace CustomerRelation\Entity;

use Zend\Stdlib\ArraySerializableInterface;

/**
 * System Generated Code
 *
 * User : Htoonlin
 * Date : 2015-09-02 17:23:19
 *
 * @package CustomerRelation\Entity
 */
class Contract implements ArraySerializableInterface
{

    protected $contractId = null;

    protected $companyId = null;

    protected $contactId = null;

    protected $projectId = null;

    protected $code = null;

    protected $name = null;

    protected $amount = null;

    protected $currencyId = null;

    protected $contractFile = null;

    protected $contractBy = null;

    protected $contractDate = null;

    protected $notes = null;

    protected $status = null;

    public function getContractId()
    {
        return $this->contractId;
    }

    public function setContractId($value)
    {
        $this->contractId = $value;
    }

    public function getCompanyId()
    {
        return $this->companyId;
    }

    public function setCompanyId($value)
    {
        $this->companyId = $value;
    }

    public function getContactId()
    {
        return $this->contactId;
    }

    public function setContactId($value)
    {
        $this->contactId = $value;
    }

    public function getProjectId()
    {
        return $this->projectId;
    }

    public function setProjectId($value)
    {
        $this->projectId = $value;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($value)
    {
        $this->code = $value;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($value)
    {
        $this->name = $value;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($value)
    {
        $this->amount = $value;
    }

    public function getCurrencyId()
    {
        return $this->currencyId;
    }

    public function setCurrencyId($value)
    {
        $this->currencyId = $value;
    }

    public function getContractFile()
    {
        return $this->contractFile;
    }

    public function setContractFile($value)
    {
        $this->contractFile = $value;
    }

    public function getContractBy()
    {
        return $this->contractBy;
    }

    public function setContractBy($value)
    {
        $this->contractBy = $value;
    }

    public function getContractDate()
    {
        return $this->contractDate;
    }

    public function setContractDate($value)
    {
        $this->contractDate = $value;
    }

    public function getNotes()
    {
        return $this->notes;
    }

    public function setNotes($value)
    {
        $this->notes = $value;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($value)
    {
        $this->status = $value;
    }

    public function exchangeArray(array $data)
    {
        $this->contractId = (!empty($data['contractId'])) ? $data['contractId'] : null;
        $this->companyId = (!empty($data['companyId'])) ? $data['companyId'] : null;
        $this->contactId = (!empty($data['contactId'])) ? $data['contactId'] : null;
        $this->projectId = (!empty($data['projectId'])) ? $data['projectId'] : null;
        $this->code = (!empty($data['code'])) ? $data['code'] : null;
        $this->name = (!empty($data['name'])) ? $data['name'] : null;
        $this->amount = (!empty($data['amount'])) ? $data['amount'] : null;
        $this->currencyId = (!empty($data['currencyId'])) ? $data['currencyId'] : null;
        $this->contractFile = (!empty($data['contractFile'])) ? $data['contractFile'] : null;
        $this->contractBy = (!empty($data['contractBy'])) ? $data['contractBy'] : null;
        $this->contractDate = (!empty($data['contractDate'])) ? $data['contractDate'] : null;
        $this->notes = (!empty($data['notes'])) ? $data['notes'] : null;
        $this->status = (!empty($data['status'])) ? $data['status'] : null;
    }

    public function getArrayCopy()
    {
        return array(
            'contractId' => $this->contractId,
            'companyId' => $this->companyId,
            'contactId' => $this->contactId,
            'projectId' => $this->projectId,
            'code' => $this->code,
            'name' => $this->name,
            'amount' => $this->amount,
            'currencyId' => $this->currencyId,
            'contractFile' => $this->contractFile,
            'contractBy' => $this->contractBy,
            'contractDate' => $this->contractDate,
            'notes' => $this->notes,
            'status' => $this->status,
        );
    }
}
