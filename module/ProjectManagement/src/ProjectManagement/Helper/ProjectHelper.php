<?php
/**
 * Created by PhpStorm.
 * User: Sundew
 * Date: 5/25/2015
 * Time: 1:21 PM
 */

namespace ProjectManagement\Helper;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class ProjectHelper{
    protected $dbAdapter;

    public function __construct($dbAdapter){
        $this->dbAdapter=$dbAdapter;
    }

    protected $form;
    public function getForm($managers){
        if(!$this->form){
            $projectId=new Element\Hidden();
            $projectId->setName('projectId');

            $code=new Element\Text();
            $code->setLabel('Code')
                ->setName('code')
                ->setAttributes(array(
                    'class'=>'form-control'),
                    'placeholder','Enter Code'
                );

            $name=new Element\Text();
            $name->setLabel('Name')
                ->setName('name')
                ->setAttributes(array(
                    'class'=>'form-control'),
                    'placeholder','Enter Name'
                );

            $description=new Element\Textarea();
            $description->setLabel('Description')
                ->setName('description')
                ->setAttributes(array(
                    'class'=>'form-control'),
                    'placeholder','Enter Description'
                );

            $manager=new Element\Select();
            $manager->setLabel('Manager')
                ->setAttribute('class', 'form-control')
                ->setName('managerId')
                ->setEmptyOption('---Choose Manager---')
                ->setValueOptions($managers);

            $startDate=new Element\Date('startDate');
            $startDate->setLabel('Start Date')
                ->setName('startDate')
                ->setAttributes(array(
                    'class'=>'form-control',
                    'allowPastDates'=>true,
                    'momentConfig'=>array('format'=>'YYYY-MM-DD')
                ));

            $endDate=new Element\Date('endDate');
            $endDate->setLabel('End Date')
                ->setName('endDate')
                ->setAttributes(array(
                    'class'=>'form-control',
                    'allowPastDates'=>true,
                    'momentConfig'=>array('format'=>'YYYY-MM-DD')
                ));

            $group_code=new Element\Text();
            $group_code->setLabel('Group code *')
                ->setName('group_code')
                ->setAttributes(array(
                    'class'=>'form-control',
                ));

            $repository=new Element\Text();
            $repository->setLabel('Repository *')
            ->setName('repository')
            ->setAttributes(array(
                'class'=>'form-control',
                'placeholder' => 'Enter svn/github repo'
            ));

            $status=new Element\Select();
            $status->setLabel('Status')
                ->setName('status')
                ->setAttribute('class','form-control')
                ->setValueOptions(array(
                    'A'=>'Active',
                    'D'=>'Inactive'
                ));

            $remark=new Element\Textarea();
            $remark->setLabel('Remark')
                ->setName('remark')
                ->setAttributes(array(
                    'class'=>'form-control'
                ));

            $form=new Form();
            $form->setAttributes(array(
                'class'=>'form-horizontal',
                'enctype'=>'multipart/form-data'
            ));

            $form->add($projectId);
            $form->add($code);
            $form->add($name);
            $form->add($description);
            $form->add($manager);
            $form->add($startDate);
            $form->add($endDate);
            $form->add($group_code);
            $form->add($repository);
            $form->add($status);
            $form->add($remark);

            $this->form=$form;
        }

        return $this->form;
    }
    public function setForm($form){
        $this->form=$form;
    }

    private $inputFilter;
    public function getInputFilter($projectId){
        if(!$this->inputFilter){
            $filter=new InputFilter();

            /*Code*/
            $filter->add(array(
                'name'=>'code',
                'required'=>true,
                'filters'=>array(
                    array('name'=>'StripTags'),
                    array('name'=>'StringTrim')),
                'validators'=>array(
                    array(
                        'name'=>'StringLength',
                        'options'=>array(
                            'encoding'=>'UTF-8',
                            'min'=>1,
                            'max'=>50
                        )
                    ),
                    array(
                        'name'=>'Db/NoRecordExists',
                        'options'=>array(
                            'table'=>'tbl_pm_project',
                            'field'=>'code',
                            'adapter'=>$this->dbAdapter,
                            'exclude'=>"(projectId != $projectId AND deletedDate IS NULL AND deletedBy IS NULL)",
                            'message'=>'This project code is already exist.'
                        )
                    )
                )
            ));

            /*Name*/
            $filter->add(array(
                'name'=>'name',
                'required'=>true,
                'filters'=>array(
                    array('name'=>'StripTags'),
                    array('name'=>'StringTrim')),
                'validators'=>array(
                    array(
                        'name'=>'StringLength',
                        'options'=>array(
                            'encoding'=>'UTF-8',
                            'min'=>1,
                            'max'=>50
                        )
                    )
                )
            ));

            /*Description*/
            $filter->add(array(
                'name'=>'description',
                'required'=>true,
                'filters'=>array(
                    array('name'=>'StripTags'),
                    array('name'=>'StringTrim')),
                'validators'=>array(
                    array(
                        'name'=>'StringLength',
                        'options'=>array(
                            'encoding'=>'UTF-8',
                            'min'=>1,
                            'max'=>500
                        )
                    )
                )
            ));

            /*Manager*/
            $filter->add(array(
                'name'=>'managerId',
                'required'=>true
            ));

            /*Start Date*/
            $filter->add(array(
                'name'=>'startDate',
                'required'=>true
            ));

            $filter->add(array(
                'name' => 'repository',
                'required' => false,
            ));

            /*Group Code*/
            $filter->add(array(
                'name'=>'group_code',
                'required'=>false,
                'filters'=>array(
                    array('name'=>'StripTags'),
                    array('name'=>'StringTrim')),
                'validators'=>array(
                    array(
                        'name'=>'StringLength',
                        'options'=>array(
                            'encoding'=>'UTF-8',
                            'min'=>1,
                            'max'=>50
                        )
                    )
                )
            ));

            $filter->add(array(
                'name'=>'status',
                'required'=>true
            ));

            $this->inputFilter=$filter;
        }

        return $this->inputFilter;
    }
    public function setInputFilter($filter){
        $this->inputFilter=$filter;
    }
}