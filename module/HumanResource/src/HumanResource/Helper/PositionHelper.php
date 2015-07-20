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
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;

class PositionHelper
{
    protected $dbAdapter;
    public function __construct($dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }

    protected $form;
    public function getForm(array $defaultStatus, array $currencyList)
    {
        if (!$this->form) {
            $positionId = new Element\Hidden();
            $positionId->setName('positionId');

            $name = new Element\Text();
            $name->setLabel('Name')
                ->setName("name")
                ->setAttribute('class', 'form-control');

            $minSalary = new Element\Text();
            $minSalary->setLabel('MinSalary')
                ->setName("min_Salary")
                ->setAttribute('class', 'form-control');

            $maxSalary = new Element\Text();
            $maxSalary->setLabel('MaxSalary')
                ->setName("max_Salary")
                ->setAttribute('class', 'form-control');

            $currency = new Element\Select();
            $currency->setName('currencyId')
                ->setLabel('Currency Type')
                ->setAttribute('class', 'form-control')
                ->setEmptyOption('-- Choose Currency --')
                ->setValueOptions($currencyList);

            $status=new Element\Select();
            $status->setName('status')
                ->setLabel('Status')
                ->setAttribute('class', 'form-control')
                ->setValueOptions($defaultStatus);

            $form = new Form();
            $form->setAttribute('class', 'form-horizontal');
            $form->add($positionId);
            $form->add($name);
            $form->add($currency);
            $form->add($minSalary);
            $form->add($maxSalary);
            $form->add($status);

            $this->form = $form;
        }

        return $this->form;
    }
    public function setForm($form)
    {
        $this->form = $form;
    }
    protected $inputFilter;
    public function getInputFilter($positionId)
    {
        if(!$this->inputFilter) {
            $filter = new InputFilter();

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
                        ),
                    ),
                    array(
                        'name' => 'Db\NoRecordExists',
                        'options' => array(
                            'table' => 'tbl_hr_position',
                            'field' => 'name',
                            'adapter' => $this->dbAdapter,
                            'exclude' => array(
                                'field' => 'positionId',
                                'value' => $positionId
                            ),
                            'message' => 'This position  name is already exist.',
                        )
                    ),
                ),
            ));
            $filter->add(array(
                'name' => 'currencyId',
                'required' => true,
            ));
            $filter->add(array(
                'name' => 'min_Salary',
                'required' => true,
            ));
            $filter->add(array(
                'name' => 'max_Salary',
                'required' => true,
            ));


            $this->inputFilter = $filter;
        }
        return $this->inputFilter;
    }
    public function setInputFilter($filter)
    {
        $this->inputFilter = $filter;
    }

}

