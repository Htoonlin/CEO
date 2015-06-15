<?php
/**
 * Created by PhpStorm.
 * User: Lwin
 * Date: 4/28/2015
 * Time: 10:09 AM
 */

namespace CustomerRelation\Helper;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\FileInput;
use Zend\Validator\Db\NoRecordExists;

class ContactHelper{
    private $form;
    protected $companies;
    protected $dbAdapter;

    public function __construct(array $companies,$dbAdapter)
    {
        $this->companies=$companies;
        $this->dbAdapter=$dbAdapter;
    }

    public function getForm()
    {
        if(!$this->form){
            $contactId=new Element\Hidden();
            $contactId->setName('contactId');

            $name=new Element\Text();
            $name->setLabel('Name')
                ->setName("name")
                ->setAttribute('class','form-control');

            $phone=new Element\Text();
            $phone->setLabel('Phone')
                ->setName("phone")
                ->setAttribute('class', 'form-control');

            $email=new Element\Email();
            $email->setLabel('Email')
                ->setName("email")
                ->setAttribute('class', 'form-control');

            $address=new Element\Textarea();
            $address->setLabel('Address')
                ->setName("address")
                ->setAttribute('class', 'form-control');

            $website=new Element\Url();
            $website->setLabel('Website')
                ->setName("website")
                ->setAttribute('class', 'form-control');

            $selectCompany=new Element\Select();
            $selectCompany->setName('companyId')
                ->setLabel('Company')
                ->setAttribute('class', 'form-control')
                ->setEmptyOption("---Choose Company---")
                ->setValueOptions($this->companies);

            $notes=new Element\Textarea();
            $notes->setLabel('Notes')
                ->setName("notes")
                ->setAttribute('class', 'form-control');

            $tag=new Element\Text();
            $tag->setLabel('Tag')
                ->setName("tag")
                ->setAttribute('class','form-control');

            $status=new Element\Select();
            $status->setName('status')
                ->setLabel('Status')
                ->setAttribute('class','form-control')
                ->setValueOptions(array(
                    'A'=>'Active',
                    'D'=>'Inactive',
                ));

            $form=new Form();
            $form->setAttribute('class','form-horizontal');
            $form->setAttribute('enctype','multipart/form-data');
            $form->add($contactId);
            $form->add($name);
            $form->add($phone);
            $form->add($email);
            $form->add($address);
            $form->add($website);
            $form->add($selectCompany);
            $form->add($notes);
            $form->add($tag);
            $form->add($status);
            $this->form=$form;
        }
        return $this->form;
    }

    public function setForm($form)
    {
        $this->form=$form;
    }

    protected $inputFilter;
    public function getInputFilter($contactId=0)
    {
        if(!$this->inputFilter){
            $filter=new InputFilter();
            $filter->add(array(
                'name'=>'contactId',
                'required'=>true,
                'filters'=>array(
                    array('name'=>'Int'),
                )
            ));

            $filter->add(array(
                'name'=>'name',
                'required'=>true,
                'validators'=>array(
                    array(
                        'name'=>'StringLength',
                        'options'=>array(
                            'max'=>200,
                            'min'=>1,
                            'encoding'=>'UTF-8',
                        ),
                    ),

                    array(
                        'name'=>'Db\NoRecordExists',
                        'options'=>array(
                            'table'=>'tbl_cr_contact',
                            'field'=>'name',
                            'adapter'=>$this->dbAdapter,
                            'exclude'=>array(
                                'field'=>'contactId',
                                'value'=>$contactId
                            ),
                            'message'=>'This contact name is already exist.',
                        )
                    ),
                ),
            ));

            $filter->add(array(
                'name'=>'phone',
                'required'=>true,
            ));

            $filter->add(array(
                'name'=>'email',
                'required'=>false,
            ));

            $filter->add(array(
                'name'=>'address',
                'required'=>true,

            ));

            $filter->add(array(
                'name'=>'website',
                'required'=>false,

            ));

            $filter->add(array(
                'name'=>'companyId',
                'required'=>false,
            ));

            $filter->add(array(
                'name'=>'notes',
                'required'=>false,

            ));

            $filter->add(array(
                'name'=>'tag',
                'required'=>true,

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