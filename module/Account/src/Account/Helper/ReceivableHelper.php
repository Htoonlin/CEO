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
use Zend\InputFilter\FileInput;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator\File\Extension;

class ReceivableHelper
{
    protected $form;
    public function getForm(array $currencies, array $paymentTypes)
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

            $cboPaymentType = new Element\Select();
            $cboPaymentType->setName('paymentType')
                ->setLabel('Payment Type')
                ->setAttribute('class', 'form-control')
                ->setEmptyOption('--Choose--')
                ->setValueOptions($paymentTypes);

            $txtAttachmentFile = new Element\File('attachmentFile');
            $txtAttachmentFile->setLabel('Attachment File Upload');

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
            $form->add($cboPaymentType);
            $form->add($txtAttachmentFile);
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
    public function getInputFilter($voucherNo = "")
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
            $fileInput = new FileInput('attachmentFile');
            $fileInput->setRequired(false);
            $fileInput->getValidatorChain()
                ->attach(new Extension(array('doc', 'docx', 'pdf')))
                ->attachByName('filesize', array('max'=> '50MB'));
            $fileInput->getFilterChain()->attachByName(
                'filerenameupload',
                array(
                    'target' => sprintf('./data/uploads/receivable/%s', $voucherNo),
                    'use_upload_extension' => true,
                    'overwrite' => true,
                )
            );
            $filter->add($fileInput);
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

