<?php
/**
 * Created by PhpStorm.
 * User: Lwin
 * Date: 4/28/2015
 * Time: 12:36 PM
 */

namespace CustomerRelation\Helper;

use Zend\Form\Annotation\ElementAnnotationsListener;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;

class CompanyHelper{
    private $form;
    protected $dbAdapter;

    public function __construct($dbAdapter)
    {
        $this->dbAdapter=$dbAdapter;
    }

    public function getForm(array $companyTypes, array $statusList)
    {
        if(!$this->form){
            $companyId=new Element\Hidden();
            $companyId->setName('companyId');

            $name=new Element\Text();
            $name->setLabel('Name')
                ->setName("name")
                ->setAttribute('class', 'form-control');

            $phone=new Element\Text();
            $phone->setLabel('Phone')
                ->setName("phone")
                ->setAttribute('class', 'form-control');

            $address=new Element\Textarea();
            $address->setLabel('Address')
                ->setName("address")
                ->setAttribute('class', 'form-control');

            $website = new Element\Url();
            $website->setLabel('Website')
                ->setName("website")
                ->setAttribute('class', 'form-control');

            $type=new Element\Select();
            $type->setName("type")
                ->setLabel('Type')
                ->setAttribute('class', 'form-control')
                ->setValueOptions($companyTypes);

            $status=new Element\Select();
            $status->setName("status")
                ->setLabel('Status')
                ->setAttribute('class', 'form-control')
                ->setValueOptions($statusList);

            $form=new Form();
            $form->setAttribute('class', 'form-horizontal');
            $form->add($companyId);
            $form->add($name);
            $form->add($phone);
            $form->add($address);
            $form->add($website);
            $form->add($type);
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
    public function getInputFilter($companyId)
    {
        if(!$this->inputFilter){
            $filter=new InputFilter();

            $filter->add(array(
                'name'=>'companyId',
                'required'=>true,
                'filters'=>array(
                    array('name'=>'Int'),
                )
            ));

            $exclude="(companyId != $companyId AND status='A')";
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
                            'table'=>'tbl_cr_company',
                            'field'=>'name',
                            'adapter'=>$this->dbAdapter,
                            'exclude'=>$exclude,
                            'message'=>'This company name is already exist.',
                        )
                    ),
                ),
            ));

            $filter->add(array(
                'name'=>'phone',
                'required'=>true,
            ));

            $filter->add(array(
                'name'=>'address',
                'required'=>true,
            ));

            $filter->add(array(
                'name'=>'website',
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