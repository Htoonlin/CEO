<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 8/4/2015
 * Time: 2:07 PM
 */

namespace Development\Helper;

use Zend\Form\Element\Select;
use Zend\Form\Element\Textarea;
use Zend\Form\Form;

class GenerateHelper
{
    protected $form;
    public function getForm(array $tableList, array $typeList, array $moduleList){

        if(!$this->form){
            $cboTable = new Select('tbl_name');
            $cboTable->setAttribute('class', 'form-control')
                ->setValueOptions($tableList)
                ->setEmptyOption("-- Choose Table --");

            $cboGenerate = new Select('type');
            $cboGenerate->setAttribute('class', 'form-control')
                ->setValueOptions($typeList)
                ->setEmptyOption('-- Choose Type --');

            $cboModule = new Select('module');
            $cboModule->setAttribute('class', 'form-control')
                ->setValueOptions($moduleList)
                ->setEmptyOption('-- Choose Module --');

            $txtGenerate = new Textarea('txtGenerate');

            $form = new Form();
            $form->setAttributes(array(
                'role' => 'form',
                'id' => 'frmGenerate',
                'method' => 'post'
            ));
            $form->add($txtGenerate);
            $form->add($cboTable);
            $form->add($cboGenerate);
            $form->add($cboModule);

            $this->form = $form;
        }

        return $this->form;
    }
}