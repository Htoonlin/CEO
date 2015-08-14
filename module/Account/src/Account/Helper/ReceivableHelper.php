<?php
/**
 * Created by PhpStorm.
 * User: linn
 * Date: 3/6/2015
 * Time: 1:15 PM
 */

namespace Account\Helper;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;

class ReceivableHelper
{
    protected $form;
    public function getForm(array $currencies)
    {
        if(!$this->form) {
            $hidId=new Element\Hidden();
            $hidId->setName('receiveVoucherId');

            $txtVoucherNo=new Element\Text();
            $txtVoucherNo->setLabel('Number')
                ->setName("voucherNo")
                ->setAttribute('class','form-control text-center')
                ->setAttribute('readonly', 'readonly');

            $txtVoucherDate = new Element\Date('voucherDate');
            $txtVoucherDate->setLabel('Date')
                ->setAttributes(array(
                    'class'=> 'form-control',
                    'allowPastDates' => true,
                    'momentConfig' => array(
                        'format' => 'YYYY-MM-DD'
                    )
                ));
            $txtAccountType=new Element\Hidden('accountType');

            $txtDescription=new Element\Textarea();
            $txtDescription->setName("description")
                ->setLabel('Description')
                ->setAttribute('class','form-control');

            $txtAmount=new Element\Number();
            $txtAmount->setName("amount")
                ->setLabel('Amount')
                ->setAttribute('class','form-control')
                ->setattributes(array(
                    'min' => '10',
                    'max' => '99999999999',
                    'step' => '1'
                ));

            $cboCurrency = new Element\Select();
            $cboCurrency->setName('currencyId')
                ->setLabel('Currency Type')
                ->setAttribute('class', 'form-control')
                ->setEmptyOption('-- Choose --')
                ->setValueOptions($currencies);

            $txtDepositBy=new Element\Hidden();
            $txtDepositBy->setName("depositBy");

            $txtReason=new Element\Textarea();
            $txtReason->setName('reason');

            $txtGroupCode=new Element\Text();
            $txtGroupCode->setName('group_code')
                ->setLabel('Group Code (*)')
                ->setAttribute('class', 'form-control');

            $form=new Form();
            $form->setAttribute('role', 'form');
            $form->add($hidId);
            $form->add($txtVoucherNo);
            $form->add($txtVoucherDate);
            $form->add($txtAccountType);
            $form->add($txtDescription);
            $form->add($txtAmount);
            $form->add($cboCurrency);
            $form->add($txtDepositBy);
            $form->add($txtReason);
            $form->add($txtGroupCode);

            $this->form=$form;
        }
        return $this->form;
    }
    public function setForm($form)
    {
        $this->form=$form;

    }
    protected $inputFilter;
    public function getInputFilter()
    {
        if(!$this->inputFilter) {
            $filter=new InputFilter();
            $filter->add(array(
                'name'=>'receiveVoucherId',
                'required'=>true,
                'filters'=>array(
                    array('name'=>'Int'),
                )
            ));
            $filter->add(array(
                'name'=>'voucherNo',
                'required'=>true,
                'validators'=>array(
                    array(
                        'name'=>'StringLength',
                        'options'=>array(
                            'max'=>255
                        ),
                    ),
                ),
            ));

            $filter->add(array(
                'name' => 'currencyId',
                'requried' => true,
            ));

            $filter->add(array(
                'name'=>'description',
                'required'=>true,
                'validators'=>array(
                    array(
                        'name'=>'StringLength',
                        'options'=>array(
                            'max'=>255
                        ),
                    ),
                ),
            ));
            $filter->add(array('name' => 'accountType', 'required' => true));
            $this->inputFilter=$filter;
        }
        return $this->inputFilter;
    }
    public function setInputFilter($filter)
    {
        $this->inputFilter=$filter;
    }
}

