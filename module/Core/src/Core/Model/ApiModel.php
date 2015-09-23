<?php

/**
 * Created by PhpStorm.
 * User: Htoonlin
 * Date: 2015-08-30
 * Time: 07:38 PM
 */
namespace Core\Model;

use Zend\InputFilter\InputFilter;
use Zend\Stdlib\ArrayUtils;
use Zend\View\Model\JsonModel;

/**
 * Class ApiModel
 * @package Core\Model
 */
class ApiModel extends JsonModel
{
    /**
     * @param null $data
     * @param null $values
     */
    function __construct($data = null, $values = null){
        if($data != null){
            $this->setResponseData($data);
        }

        parent::__construct(null, $values);
    }

    /**
     * @var
     */
    protected $responseData;

    /**
     * @var int
     */
    protected $statusCode = 200;

    /**
     * @param $data
     */
    public function setResponseData($data){
        $this->responseData = $data;
    }

    /**
     * @return mixed
     */
    public function getResponseData(){
        return $this->responseData;
    }

    /**
     * @param $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @var
     */
    protected $statusMessage;

    /**
     * @param $message
     */
    public function setStatusMessage($message){
        $this->statusMessage = $message;
    }

    /**
     * @return mixed
     */
    public function getStatusMessage(){
        return $this->statusMessage;
    }
}