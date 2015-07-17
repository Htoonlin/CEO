<?php
/**
 * Created by PhpStorm.
 * User: Lwin
 * Date: 4/23/2015
 * Time: 4:26 PM
 */
namespace Account\Helper;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;

class CurrencyHelper{
    private $form;
    protected  $dbAdapter;

    public function __construct($dbAdapter)
    {
        $this->dbAdapter=$dbAdapter;
    }

    public function getForm(array $defaultStatus)
    {
       if(!$this->form){
           $currencyId=new Element\Hidden();
           $currencyId->setName('currencyId');

           $name=new Element\Text();
           $name->setLabel('Name')
               ->setName("name")
               ->setAttribute('class', 'form-control');

           $code=new Element\Text();
           $code->setLabel('Code')
               ->setName("code")
               ->setAttribute('class', 'form-control');

           $rate=new Element\Text();
           $rate->setLabel('Rate')
               ->setName("rate")
               ->setAttributes(array(
                   'class'=>'form-control',
               ));

           $status=new Element\Select();
           $status->setName('status')
               ->setLabel('Status')
               ->setAttribute('class','form-control')
               ->setValueOptions($defaultStatus);

           $changedRate = new Element\Checkbox();
           $changedRate->setName('changedRate')
               ->setLabel('Auto renew?')
               ->setAttribute('class', 'form-control');

           $form=new Form();
           $form->setAttribute('class', 'form-horizontal');
           $form->add($currencyId);
           $form->add($code);
           $form->add($name);
           $form->add($rate);
           $form->add($status);
           $form->add($changedRate);

           $this->form=$form;
       }
            return $this->form;
    }

    public function setForm($form)
    {
        $this->form=$form;
    }

    protected $inputFilter;
    public function getInputFilter($currencyId)
    {

       if(!$this->inputFilter){
           $filter=new InputFilter();

           $filter->add(array(
               'name'=>'currencyId',
               'required'=>true,
               'filters'=>array(
                   array('name'=>'Int'),
               )
           ));

           $exclude = "(currencyId != $currencyId AND status = 'A')";
           $filter->add(array(
               'name'=>'name',
               'required'=>true,
               'validators'=>array(
                   array(
                       'name'=>'StringLength',
                       'options'=>array(
                           'max'=>255,
                           'min'=>1,
                           'encoding'=>'UTF-8',
                       ),
                   ),
                   array(
                       'name'=>'Db\NoRecordExists',
                       'options'=>array(
                           'table'=>'tbl_account_currency',
                           'field'=>'name',
                           'adapter'=>$this->dbAdapter,
                           'exclude'=> $exclude,
                           'message'=>'This currency name is already exist.',
                       )
                   ),
               ),
           ));

           $filter->add(array(
               'name'=>'code',
               'required'=>true,
               'validators'=>array(
                   array(
                       'name'=>'StringLength',
                       'options'=>array(
                           'max'=>255,
                           'min'=>1,
                           'encoding'=>'UTF-8',
                       ),
                   ),

               array(
                   'name'=>'Db\NoRecordExists',
                   'options'=>array(
                       'table'=>'tbl_account_currency',
                       'field'=>'code',
                       'adapter'=>$this->dbAdapter,
                       'exclude'=>$exclude,
                       'message'=>'This currency code is already exist.',

                   )
               ),
                   ),
           ));

           $filter->add(array(
               'name'=>'rate',
               'required'=>true,
               'validators'=>array(
                   array('name'=>'Float'),
               )
           ));

           $filter->add(array(
               'name' => 'changedRate',
               'required' => false
           ));

           $this->inputFilter=$filter;
       }
        return $this->inputFilter;

    }

    public function setInputFilter($filter)
    {
        $this->inputFilter=$filter;
    }
}
