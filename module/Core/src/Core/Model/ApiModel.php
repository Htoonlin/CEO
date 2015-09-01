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
    function __construct($data, $values = null){
        $this->setResponseData($data);
        parent::__construct(null, $values);
    }

    protected $responseData;

    protected $ajaxOnly = false;
    /**
     * @var int
     */
    protected $statusCode = 200;
    /**
     * @var InputFilter
     */
    protected $inputFilter;
    /**
     * @var array
     */
    protected $allowMethods = ['GET' , 'POST'];

    public function setAjaxOnly($is){
        $this->ajaxOnly = $is;
    }

    public function isAjaxOnly(){
        return $this->ajaxOnly;
    }

    public function setResponseData($data){
        $this->responseData = $data;
    }

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

    protected $statusMessage;
    public function setStatusMessage($message){
        $this->statusMessage = $message;
    }
    public function getStatusMessage(){
        return $this->statusMessage;
    }

    /**
     * @param InputFilter $filter
     */
    public function setInputFilter(InputFilter $filter){
        $this->inputFilter = $filter;
    }

    /**
     * @return InputFilter
     */
    public function getInputFilter(){
        return $this->inputFilter;
    }

    /**
     * @param array $allowMethods
     */
    public function setAllowMethods(array $allowMethods){
        $this->allowMethods = $allowMethods;
    }

    /**
     * @return array
     */
    public function getAllowMethods(){
        return $this->allowMethods;
    }
}