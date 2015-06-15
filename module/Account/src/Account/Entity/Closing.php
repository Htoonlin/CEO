<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 5/22/2015
 * Time: 7:22 PM
 */

namespace Account\Entity;


use Zend\Stdlib\ArraySerializableInterface;

class Closing implements ArraySerializableInterface
{
    protected $closingId;
    public function getClosingId(){return $this->closingId;}
    public function setClosingId($value){$this->closingId = $value;}

    protected $currencyId;
    public function getCurrencyId(){return $this->currencyId;}
    public function setCurrencyId($value){$this->currencyId = $value;}

    protected $openingDate;
    public function getOpeningDate(){return $this->openingDate;}
    public function setOpeningDate($value){$this->openingDate = $value;}

    protected $receivableId;
    public function getReceivableId(){return $this->receivableId;}
    public function setReceivableId($value){$this->receivableId = $value;}

    protected $openingAmount;
    public function getOpeningAmount(){return $this->openingAmount;}
    public function setOpeningAmount($value){$this->openingAmount = $value;}

    protected $closingDate;
    public function getClosingDate(){return $this->closingDate;}
    public function setClosingDate($value){$this->closingDate = $value;}

    protected $payableId;
    public function getPayableId(){return $this->payableId;}
    public function setPayableId($value){$this->payableId = $value;}

    protected $closingAmount;
    public function getClosingAmount(){return $this->closingAmount;}
    public function setClosingAmount($value){$this->closingAmount = $value;}


    public function exchangeArray(array $data)
    {
        $this->currencyId = (!empty($data['currencyId'])) ? $data['currencyId'] : null;
        $this->openingDate = (!empty($data['openingDate'])) ? $data['openingDate'] : null;
        $this->receivableId = (!empty($data['receivableId'])) ? $data['receivableId'] : null;
        $this->openingAmount = (!empty($data['openingAmount'])) ? $data['openingAmount'] : null;
        $this->closingId = (!empty($data['closingId'])) ? $data['closingId'] : null;
        $this->closingDate = (!empty($data['closingDate'])) ? $data['closingDate'] : null;
        $this->payableId = (!empty($data['payableId'])) ? $data['payableId'] : null;
        $this->closingAmount = (!empty($data['closingAmount'])) ? $data['closingAmount'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}