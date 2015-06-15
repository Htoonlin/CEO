<?php
/**
 * Created by PhpStorm.
 * User: july
 * Date: 5/4/2015
 * Time: 2:37 PM
 */

namespace CustomerRelation\Helper;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\FileInput;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator\Db\NoRecordExists;
use Zend\Validator\File\IsImage;
class ContractHelper
{
    protected $form;
    public function getForm(array $currencies, array $companies, array $contacts)
    {
        if(!$this->form){
            $hidId=new Element\Hidden();
            $hidId->setName('contractId');

            $txtCompanyId=new Element\Select();
            $txtCompanyId->setLabel('Company Name')
                ->setName("companyId")
                ->setAttribute('class','form-control')
                ->setEmptyOption("--Choose Company--")
                ->setValueOptions($companies);

            $txtContactId=new Element\Select();
            $txtContactId->setLabel('Contact Name')
                ->setName('contactId')
                ->setAttribute('class','form-control')
                ->setEmptyOption("--Choose Contact--")
                ->setValueOptions($contacts);

            $txtProjectId=new Element\Text();
            $txtProjectId->setLabel('Project Id')
                ->setName('projectId')
                ->setAttribute('class','form-control');

            $txtCode=new Element\Text();
            $txtCode->setLabel('Code')
                ->setName('code')
                ->setAttribute('class','form-control');

            $txtAmount=new Element\Number();
            $txtAmount->setName("amount")
                ->setLabel('Amount')
                ->setAttribute('class','form-control')
                ->setAttributes(array(
                    'min'=>'100',
                    'max'=>'99999999999',
                    'step'=>'100'
                ));

            $selectCurrency=new Element\Select();
            $selectCurrency->setName('currencyId')
                ->setLabel('Currency')
                ->setAttribute('class','form-control')
                ->setEmptyOption("---Choose Currency---")
                ->setValueOptions($currencies);

            $txtContractFile=new Element\File('contractFile');
            $txtContractFile->setLabel('File Upload')
                ->setAttribute('class','form-control');

            $txtContractBy=new Element\Text();
            $txtContractBy->setLabel('Contract By')
                ->setName('contractBy')
                ->setAttribute('class','form-control');

            $txtContractDate=new Element\Date('contractDate');
            $txtContractDate->setLabel('Date')
                ->setAttributes(array(
                    'class'=>'form-control',
                    'allowPastDates'=>true,
                    'momentConfig'=>array(
                        'format'=>'YYYY-MM-DD'
                    )
                ));

            $txtNotes=new Element\Text();
            $txtNotes->setLabel('Notes')
                ->setName('notes')
                ->setAttribute('class','form-control');

            $txtStatus=new Element\Select();
            $txtStatus->setName('status')
                ->setLabel('Status')
                ->setAttribute('class','form-control')
                ->setValueOptions(array(
                    'A'=>'Active',
                    'D'=>'Inactive',
                ));

            $form=new Form();
            $form->setAttribute('class','form-horizontal');
            $form->setAttribute('enctype','multipart/form-data');
            $form->add($hidId);
            $form->add($txtCompanyId);
            $form->add($txtContactId);
            $form->add($txtProjectId);
            $form->add($txtCode);
            $form->add($txtAmount);
            $form->add($selectCurrency);
            $form->add($txtContractFile);
            $form->add($txtContractBy);
            $form->add($txtContractDate);
            $form->add($txtNotes);
            $form->add($txtStatus);
            $this->form=$form;
        }
        return $this->form;
    }
    public function setForm($form)
    {
        $this->form=$form;
    }
    public function setInputFilter(InputFilterInterface $filter)
    {
        $this->inputFilter=$filter;
    }
    protected $inputFilter;
    public function getInputFilter($contractId = 0, $code = "")
    {
        if(!$this->inputFilter){
            $filter=new InputFilter();
            $filter->add(array(
                'name'=>'contractId',
                'required'=>true,
                'filters'=>array(
                    array('name'=>'Int'),
                )
            ));
            $filter->add(array(
                'name'=>'contactID',
                'required'=>false,
                'filters'=>array(
                    array('name'=>'Int'),
                )
            ));

            $filter->add(array(
                'name'=>'code',
                'required'=>true,
                'validators'=>array(
                    array(
                        'name'=>'StringLength',
                        'options'=>array(
                            'max'=>50,
                            'min'=>1,
                            'encoding'=>'UTF-8',
                        ),
                    ),
                ),
            ));
            $filter->add(array(
                'name'=>'notes',
                'required'=>true,
                'validators'=>array(
                    array(
                        'name'=>'StringLength',
                        'options'=>array(
                            'max'=>500,
                            'min'=>1,
                            'encoding'=>'UTF-8',
                        ),
                    ),
                ),
            ));

            $fileInput = new FileInput('contractFile');
            $fileInput->setRequired(false);

            $fileInput->getFilterChain()->attachByName(
                'filerenameupload',
                array(
                    'target' => sprintf('./data/uploads/contract/%s', $code),
                    'use_upload_extension' => true,
                    'overwrite' => true,
                )
            );

            $filter->add($fileInput);
            $this->InputFilter=$filter;
        }
        return $this->InputFilter;
    }
}