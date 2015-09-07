<?php
/**
 * Created by PhpStorm.
 * User: july
 * Date: 4/5/2015
 * Time: 5:02 AM
 */

namespace Account\Helper;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\FileInput;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\File\Extension;

class PayableHelper extends Form
{
    protected $form;
    public function getForm(array $currencies, array $paymentTypes)
    {
        if(!$this->form){
            $hidId=new Element\Hidden();
            $hidId->setName('payVoucherId');

            $txtVoucherNo=new Element\Text();
            $txtVoucherNo->setLabel('Number')
                ->setName("voucherNo")
                ->setAttribute('class','form-control text-center')
                ->setAttribute('readonly','readonly');

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
                ->setAttributes(array(
                    'min'=>'10',
                    'max'=>'99999999999',
                    'step'=>'1'
                ));

            $cboPaymentType=new Element\Select();
            $cboPaymentType->setName('paymentType')
                ->setLabel('Payment Type')
                ->setAttribute('class', 'form-control')
                ->setEmptyOption('--choose--')
                ->setValueOptions($paymentTypes);

            $txtAttachmentFile=new Element\File('attachmentFile');
            $txtAttachmentFile->setLabel('Attachment File');

            $cboCurrency=new Element\Select();
            $cboCurrency->setName('currencyId')
                ->setLabel('Currency Type')
                ->setAttribute('class','form-control')
                ->setEmptyOption('---Choose--')
                ->setValueOptions($currencies);


            $txtWithdrawBy=new Element\Hidden();
            $txtWithdrawBy->setName("withdrawBy");

            $txtReason=new Element\Textarea();
            $txtReason->setName('reason');

            $txtGroupCode=new Element\Text();
            $txtGroupCode->setName('group_code')
                ->setLabel('Group Code(*)')
                ->setAttribute('class','form-control');

            $form=new Form();
            $form->setAttribute('role','form');
            $form->setAttribute('enctype', 'multipart/form-data');
            $form->add($hidId);
            $form->add($txtVoucherNo);
            $form->add($txtVoucherDate);
            $form->add($txtAccountType);
            $form->add($txtDescription);
            $form->add($txtAmount);
            $form->add($cboPaymentType);
            $form->add($txtAttachmentFile);
            $form->add($cboCurrency);
            $form->add($txtWithdrawBy);
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
        if(!$this->inputFilter){
            $filter=new InputFilter();
            $filter->add(array(
                'name' => 'voucherNo',
                'required' => true,
                'filters' => array(
                    array('name' => 'Zend\Filter\StripTags'),
                    array('name' => 'Zend\Filter\StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'max' => 50,
                        'min' => 1,
                        'encoding' => 'UTF-8',
                    ),
                ),
            ));
            $filter->add(array(
                'name' => 'voucherDate',
                'required' => true,
            ));
            $filter->add(array(
                'name' => 'accountType',
                'required' => true,
                'validators' => array(array('name' => 'Zend\I18n\Validator\IsInt')),
            ));
            $filter->add(array(
                'name' => 'description',
                'required' => true,
                'filters' => array(
                    array('name' => 'Zend\Filter\StripTags'),
                    array('name' => 'Zend\Filter\StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'max' => 500,
                        'min' => 1,
                        'encoding' => 'UTF-8',
                    ),
                ),
            ));
            $filter->add(array(
                'name' => 'amount',
                'required' => true,
                'validators' => array(array('name' => 'Zend\I18n\Validator\IsInt')),
            ));
            $filter->add(array(
                'name' => 'paymentType',
                'required' => true,
                'filters' => array(
                    array('name' => 'Zend\Filter\StripTags'),
                    array('name' => 'Zend\Filter\StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'max' => 1,
                        'min' => 1,
                        'encoding' => 'UTF-8',
                    ),
                ),
            ));
            $filter->add(array(
                'name' => 'currencyId',
                'required' => true,
                'validators' => array(array('name' => 'Zend\I18n\Validator\IsInt')),
            ));
            $filter->add(array(
                'name' => 'withdrawBy',
                'required' => true,
                'validators' => array(array('name' => 'Zend\I18n\Validator\IsInt')),
            ));
            $filter->add(array(
                'name' => 'approveBy',
                'required' => false,
            ));
            $filter->add(array(
                'name' => 'approvedDate',
                'required' => false,
            ));
            $filter->add(array(
                'name' => 'reason',
                'required' => false,
            ));
            $filter->add(array(
                'name' => 'group_code',
                'required' => false,
            ));
            $fileInput = new FileInput('attachmentFile');
            $fileInput->setRequired(false);
            $fileInput->getValidatorChain()
                ->attachByName('filesize',array('max'=> '50MB'));
            $fileInput->getFilterChain()->attachByName(
                'filerenameupload',
                array(
                    'target' => sprintf('./data/uploads/payable/%s', $voucherNo),
                    'use_upload_extension' => true,
                    'overwrite' => true,
                )
            );
            $filter->add($fileInput);
            $this->inputFilter=$filter;
        }
        return $this->inputFilter;
    }
    public function setInputFilter(InputFilterInterface $filter)
    {
        $this->inputFilter=$filter;
    }

}


