<?php
/**
 * Created by PhpStorm.
 * User: july
 * Date: 4/28/2015
 * Time: 1:37 PM
 */

namespace CustomerRelation\Helper;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\FileInput;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\File\Extension;

class ProposalHelper extends Form
{
    protected $dbAdapter;
    public function __construct($dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }

    protected $form;
    public function getForm(array $currencies,array $companies,
                            array $contacts, array $statusList)
    {
        if(!$this->form){
            $hidId=new Element\Hidden();
            $hidId->setName('proposalId');

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

            $txtCode=new Element\Text();
            $txtCode->setLabel('Code')
                ->setName('code')
                ->setAttribute('class','form-control');

            $txtName=new Element\Text();
            $txtName->setLabel('Name')
                ->setName('name')
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
                ->setAttribute('class','form-control')
                ->setEmptyOption('--Currency--')
                ->setValueOptions($currencies);

            $txtProposalDate=new Element\Date('proposalDate');
            $txtProposalDate->setLabel('Date')
                ->setAttributes(array(
                    'class'=>'form-control',
                    'allowPastDates'=>true,
                    'momentConfig'=>array(
                        'format'=>'YYYY-MM-DD'
                    )
                ));

            $txtProposalFile = new Element\File();
            $txtProposalFile->setName('proposalFile')
                ->setLabel('Upload file');

            $txtNodes=new Element\Textarea();
            $txtNodes->setLabel('Notes')
                ->setName('notes')
                ->setAttribute('class','form-control');

            $txtProposalBy=new Element\Text();
            $txtProposalBy->setName('proposalBy')
                ->setLabel('Proposal By')
                ->setAttribute('class','form-control');

            $txtGroupCode=new Element\Text();
            $txtGroupCode->setLabel('Group Code')
                ->setName('group_code')
                ->setAttribute('class','form-control');

            $txtStatus=new Element\Select();
            $txtStatus->setName('status')
                ->setLabel('Status')
                ->setAttribute('class','form-control')
                ->setValueOptions($statusList);

            $form=new Form();
            $form->setAttribute('class','form-horizontal');
            $form->setAttribute('enctype','multipart/form-data');
            $form->add($hidId);
            $form->add($txtCompanyId);
            $form->add($txtContactId);
            $form->add($txtCode);
            $form->add($txtName);
            $form->add($txtAmount);
            $form->add($selectCurrency);
            $form->add($txtProposalDate);
            $form->add($txtProposalFile);
            $form->add($txtNodes);
            $form->add($txtProposalBy);
            $form->add($txtGroupCode);
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
    public function getInputFilter($proposalId=0, $code = "")
    {
        if(!$this->inputFilter){
            $filter=new InputFilter();
            $filter->add(array(
                'name'=>'proposalId',
                'required'=>true,
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
                    array(
                        'name' => 'Db\NoRecordExists',
                        'options' => array(
                            'table' => 'tbl_cr_proposal',
                            'field' => 'code',
                            'adapter' => $this->dbAdapter,
                            'exclude' => "(proposalId != $proposalId AND deletedDate IS NULL and deletedBy IS NULL)",
                            'message' => 'This proposal code is already exists.',
                        ),
                    ),
                ),
            ));

            $filter->add(array(
                'name' => 'name',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'max' => 255,
                            'min' => 1,
                            'encoding' => 'UTF-8',
                        )
                    )
                ),
            ));

            $filter->add(array(
                'name' => 'amount',
                'required' => true,
            ));

            $filter->add(array(
                'name' => 'proposalDate',
                'required' => true,
            ));

            $filter->add(array(
                'name' => 'notes',
                'required' => false,
            ));

            $filter->add(array(
                'name' => 'group_code',
                'required' => false,
            ));

            $fileInput = new FileInput('proposalFile');
            $fileInput->setRequired(false);
            $fileInput->getValidatorChain()
                ->attach(new Extension(array('doc', 'docx', 'pdf')))
                ->attachByName('filesize', array('max' => '50MB'));
            $fileInput->getFilterChain()->attachByName(
                'filerenameupload',
                array(
                    'target' => sprintf('./data/uploads/proposal/%s', $code),
                    'use_upload_extension' => true,
                    'overwrite' => true,
                )
            );

            $filter->add($fileInput);
           $this->inputFilter = $filter;
        }

        return $this->inputFilter;
    }

}