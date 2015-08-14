<?php
namespace ProjectManagement\Helper;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

/**
 * System Generated Code
 *
 * User : Htoonlin
 * Date : 2015-08-11 10:45:57
 *
 * @package ProjectManagement\Helper
 */
class TaskHelper
{

    protected $dbAdapter = null;

    protected $form = null;

    protected $inputFilter = null;

    /**
     *
     * @param array $projects
     * @return \Zend\Form\Form
     */
    public function getForm(array $projectList, array $staffList,
        array $currencyList, array $statusList)
    {
        if(!$this->form){
        	$form = new Form();
        	$taskId = new Element\Hidden('taskId');
        	$form->add($taskId);

        	$staffId = new Element\Select('staffId');
        	$staffId->setAttribute('class', 'form-control');
        	$staffId->setLabel('To Assign:');
        	$staffId->setValueOptions($staffList);
        	$form->add($staffId);

        	$projectId = new Element\Select('projectId');
        	$projectId->setAttribute('class', 'form-control');
        	$projectId->setLabel('Project *:');
        	$projectId->setValueOptions($projectList);
        	$projectId->setEmptyOption('- No project --');
        	$form->add($projectId);

        	$name = new Element\Text('name');
        	$name->setAttribute('class', 'form-control');
        	$name->setLabel('Name:');
        	$form->add($name);

        	$tag = new Element\Text('tag');
        	$tag->setAttribute('class', 'form-control');
        	$tag->setLabel('Tag:');
        	$form->add($tag);

        	$level = new Element\Number('level');
        	$level->setAttributes(array(
        		'min' => '0',
        		'max' => '10',
        		'step' => '1',
        	));
        	$level->setLabel('Level');
        	$form->add($level);

        	$fromTime = new Element\Date('fromTime');
        	$fromTime->setAttributes(array(
        		'allowPastDates' => true,
        		'momentConfig' => array('format' => 'YYYY-MM-DD'),
        	));
        	$fromTime->setLabel('From Time:');
        	$form->add($fromTime);

        	$toTime = new Element\Date('toTime');
        	$toTime->setAttributes(array(
        		'allowPastDates' => true,
        		'momentConfig' => array('format' => 'YYYY-MM-DD'),
        	));
        	$toTime->setLabel('To Time:');
        	$form->add($toTime);

        	$maxBudget = new Element\Number('maxBudget');
        	$maxBudget->setAttributes(array(
        		'min' => '0',
        		'max' => '99999999999',
        		'step' => '1',
        	));
        	$maxBudget->setLabel('Max Budget:');
        	$form->add($maxBudget);

        	$currencyId = new Element\Select('currencyId');
        	$currencyId->setAttribute('class', 'form-control');
        	$currencyId->setLabel('Currency:');
        	$currencyId->setEmptyOption('-Currency-');
        	$currencyId->setValueOptions($currencyList);
        	$form->add($currencyId);

        	$parentId = new Element\Hidden('parentId');
        	$form->add($parentId);

        	$predecessorId = new Element\Select('predecessorId');
        	$predecessorId->setAttribute('class', 'form-control');
        	$predecessorId->setLabel('Predecessor Id');
        	$form->add($predecessorId);

        	$priority = new Element\Number('priority');
        	$priority->setAttributes(array(
        		'min' => '0',
        		'max' => '99999999999',
        		'step' => '1',
        	));
        	$priority->setLabel('Priority:');
        	$form->add($priority);

        	$desc = new Element\Textarea('description');
        	$desc->setAttribute('class', 'form-control');
        	$desc->setLabel('Description:');
        	$form->add($desc);

        	$current = new Element\Number('current');
        	$current->setLabel('Current(%):');
        	$current->setAttributes(array(
        	    'min' => '0',
        	    'max' => '100',
        	    'step' => '0.25',
        	));
        	$form->add($current);

        	$status = new Element\Select('status');
        	$status->setAttribute('class', 'form-control');
        	$status->setLabel('Status:');
        	$status->setValueOptions($statusList);
        	$form->add($status);

        	$this->form = $form;
        }
        return $this->form;
    }

    /**
     *
     * @param Form $form
     */
    public function setForm(Form $form)
    {
        $this->form = $form;
    }

    /**
     *
     * @return \Zend\InputFilter\InputFilter
     */
    public function getInputFilter()
    {
        if(!$this->inputFilter){
        	$filter = new InputFilter();
        	$filter->add(array(
        		'name' => 'name',
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
        		'name' => 'current',
        		'required' => true,
        		'validators' => array(array('name' => 'Zend\I18n\Validator\IsFloat')),
        	));
        	$filter->add(array(
        		'name' => 'staffId',
        		'required' => true,
        		'validators' => array(array('name' => 'Zend\I18n\Validator\IsInt')),
        	));
        	$filter->add(array(
        		'name' => 'fromTime',
        		'required' => true,
        	));
        	$filter->add(array(
        		'name' => 'toTime',
        		'required' => true,
        	));
        	$filter->add(array(
        		'name' => 'projectId',
        		'required' => false,
        	));
        	$filter->add(array(
        		'name' => 'predecessorId',
        		'required' => false,
        	));
        	$filter->add(array(
        		'name' => 'level',
        		'required' => false,
        	));
        	$filter->add(array(
        		'name' => 'maxBudget',
        		'required' => false,
        	));
        	$filter->add(array(
        		'name' => 'currencyId',
        		'required' => false,
        	));
        	$filter->add(array(
        		'name' => 'priority',
        		'required' => true,
        		'validators' => array(array('name' => 'Zend\I18n\Validator\IsInt')),
        	));
        	$filter->add(array(
        		'name' => 'description',
        		'required' => false,
        	));
        	$filter->add(array(
        		'name' => 'tag',
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
        		'name' => 'finished',
        		'required' => false,
        	));
        	$filter->add(array(
        		'name' => 'status',
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
        	$this->inputFilter = $filter;
        }
        return $this->inputFilter;
    }

    public function setInputFilter(InputFilter $filter)
    {
        $this->inputFilter = $filter;
    }


}
