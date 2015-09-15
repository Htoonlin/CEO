<?php
/**
 * Created by PhpStorm.
 * User: linn
 * Date: 3/6/2015
 * Time: 1:15 PM
 */

namespace HumanResource\Helper;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class StaffHelper
{
    protected $form;
    protected $dbAdapter;

    public function __construct($dbAdapter)
    {
        $this->dbAdapter=$dbAdapter;
    }

    public function getForm(array $usersData, array $positionsData, array $currencyData, array $defaultStatus)
    {
        if (!$this->form) {
            $hidId = new Element\Hidden();
            $hidId->setName('staffId');

            $selectUsers=new Element\Select();
            $selectUsers->setName('userId')
                ->setLabel('User')
                ->setAttribute('class', 'form-control')
                ->setEmptyOption("--Choose User --")
                ->setValueOptions($usersData);

            $staffCode = new Element\Text();
            $staffCode->setLabel('Code')
                ->setName("staffCode")
                ->setAttribute('class', 'form-control');

            $staffName = new Element\Text();
            $staffName->setLabel('Name')
                ->setName("staffName")
                ->setAttribute('class', 'form-control');

            $selectPosition=new  Element\Select();
            $selectPosition->setName('positionId')
                ->setLabel('Position')
                ->setAttribute('class','form-control')
                ->setEmptyOption("-- Choose Position --")
                ->setValueOptions($positionsData);

            $selectDepartment=new Element\Hidden('departmentId');

            $salary = new Element\Number();
            $salary->setLabel('Salary')
                ->setName("salary")
                ->setAttributes(array(
                    'min' => '0',
                    'max' => '999999999999',
                    'step' => '1'
                ));

            $leave = new Element\Number();
            $leave->setLabel('Leave')
                ->setName("annual_leave")
                ->setAttributes(array(
                    'min' => '0.5',
                    'max' => '100',
                    'step' => '0.5'
                ));

            $PermanentDate=new Element\Date('permanentDate');
            $PermanentDate->setLabel('P-Date')
                ->setAttributes(array(
                    'allowPastDates' => true,
                    'momentConfig' => array(
                        'format' => 'YYYY-MM-DD'
                    )
                ));

            $birthDay=new Element\Date('birthday');
            $birthDay->setLabel('Birthday')
                ->setAttributes(array(
                    'allowPastDates' => true,
                    'momentConfig' => array(
                        'format' => 'YYYY-MM-DD'
                    )
                ));

            $selectCurrency=new Element\Select();
            $selectCurrency->setName('currencyId')
                ->setAttribute('class', 'form-control')
                ->setValueOptions($currencyData);

            $status=new Element\Select();
            $status->setName('status')
                ->setLabel('Status')
                ->setAttribute('class', 'form-control')
                ->setValueOptions($defaultStatus);

            $bankCode = new Element\Text('bankCode');
            $bankCode->setLabel('Bank Account')
                ->setAttributes(array('class'=>'form-control', 'placeholder'=>'Aya Bank Account No'));

            $workHours = new Element\Hidden('workHours');

            $form = new Form();
            $form->setAttribute('class', 'form-horizontal');
            $form->add($hidId);
            $form->add($selectUsers);
            $form->add($staffCode);
            $form->add($staffName);
            $form->add($selectPosition);
            $form->add($selectDepartment);
            $form->add($salary);
            $form->add($leave);
            $form->add($workHours);
            $form->add($PermanentDate);
            $form->add($status);
            $form->add($birthDay);
            $form->add($selectCurrency);
            $form->add($bankCode);
            $this->form = $form;
        }
        return $this->form;
    }

    public function setForm($form)
    {
        $this->form = $form;
    }

    protected $inputFilter;

    public function getInputFilter($staffId)
    {
        if (!$this->inputFilter) {
           $filter = new InputFilter();
            $filter->add(
                array(
                    'name' => 'staffId',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Int'),
                    )
                )
            );

            $filter->add(array(
                'name' => 'userId',
                'required' => true,
            ));

            $filter->add(array(
                'name'=>'staffCode',
                'required'=>true,
                'validators' => array(
                    array(
                        'name'=>'StringLength',
                        'max'=>200,
                        'min'=>1,
                        'encoding'=>'UTF-8',
                    ),
                    array(
                        'name'=>'Db/NoRecordExists',
                        'options'=>array(
                            'table'=>'tbl_hr_staff',
                            'field'=>'staffCode',
                            'adapter'=>$this->dbAdapter,
                            'exclude'=>"(staffId != $staffId AND deletedDate IS NULL AND deletedBy IS NULL)",
                            'message'=>'This staff code is already exist.',
                        )
                    ),
                ),
            ));

            $filter->add(array(
                'name'=>'staffName',
                'required'=> true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'=>'StringLength',
                        'max'=>200,
                        'min'=>1,
                        'encoding'=>'UTF-8',
                    ),
                ),
            ));

            $filter->add(array(
                'name' => 'workHours',
                'required' => true,
                'filters' => array(
                    array('name' => 'Zend\Filter\StripTags'),
                    array('name' => 'Zend\Filter\StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'max' => 250,
                        'min' => 1,
                        'encoding' => 'UTF-8',
                    ),
                ),
            ));

            $filter->add(array(
                'name' => 'salary',
                'required' => true,
            ));

            $filter->add(array(
                'name' => 'annual_leave',
                'required' => true,
            ));

            $filter->add(array(
                'name'=>'permanentDate',
                'required'=>true,
            ));


            $filter->add(array(
                'name'=>'birthday',
                'required'=>true,
            ));


            $filter->add(array(
                'name'=>'currencyId',
                'required'=>true,
            ));

            $filter->add(array(
                'name' => 'bankCode',
                'required' => false,
            ));
            $this->inputFilter=$filter;
        }
        return $this->inputFilter;
    }


    public function setInputFilter($filter)
    {
        $this->inputFilter = $filter;
    }
}