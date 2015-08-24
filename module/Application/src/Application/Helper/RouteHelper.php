<?php
/**
 * Created by PhpStorm.
 * User: july
 * Date: 3/26/2015
 * Time: 1:06 AM
 */

namespace Application\Helper;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
class RouteHelper {

    protected $dbAdapter;
    public function __construct($dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }
    protected $form;
    public function getForm(array $controllerList)
    {
        if(!$this->form) {
            $hidId=new Element\Hidden();
            $hidId->setName('routeId');

            $txtName=new Element\Text();
            $txtName->setLabel('Route Name')
                ->setName("name")
                ->setAttribute('class','form-control');

            $txtRoute=new Element\Text();
            $txtRoute->setLabel('Route')
                ->setName("route")
                ->setAttribute('class','form-control');

            $txtModule=new Element\Text();
            $txtModule->setLabel("Module")
                ->setName('module')
                ->setAttribute('class','form-control');

            $cboController=new Element\Select();
            $cboController->setLabel("Controller")
                ->setName('controller')
                ->setAttribute('class', 'form-control');
            $cboController->setEmptyOption('-- Choose Controller --')
                ->setValueOptions($controllerList);

            $txtConstraints=new Element\Textarea();
            $txtConstraints->setLabel("Constraints")
                ->setName('constraints')
                ->setAttribute('class','form-control');

            $form=new Form();
            $form->setAttribute('class','form-horizontal');
            $form->add($hidId);
            $form->add($txtName);
            $form->add($txtRoute);
            $form->add($txtModule);
            $form->add($cboController);
            $form->add($txtConstraints);

            $this->form=$form;
        }
        return $this->form;
    }
    public function setForm($form)
    {
        $this->form=$form;
    }
    protected $inputFilter;
    public function getInputFilter($routeId)
    {
      if(!$this->inputFilter) {
          $filter=new InputFilter();
          $filter->add(array(
              'name'=>'routeId',
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
                          'max'=>255,
                          'min'=>1,
                          'encoding'=>'UTF-8',
                      ),
                  ),
                  array(
                      'name'=>'Db\NoRecordExists',
                      'options'=>array(
                          'table'=>'tbl_route',
                          'field'=>'name',
                          'adapter'=>$this->dbAdapter,
                          'exclude'=>array(
                              'field'=>'routeId',
                              'value'=>$routeId
                          ),
                          'message'=>'This route name is already exist.',
                      )
                  ),
              ),
          ));

          $filter->add(array(
              'name'=>'route',
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
              'name'=>'module',
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
              'name' => 'controller',
              'required' => true,
          ));
          $filter->add(array(
              'name'=>'constraints',
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
          $this->inputFilter=$filter;
      }
        return $this->inputFilter;
    }
    public function setInputFilter($filter)
    {
        $this->inputFilter=$filter;
    }
}


