<?php
namespace CustomerRelation\Helper;

use Zend\Db\Adapter\Adapter;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\FileInput;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

/**
 * System Generated Code
 *
 * User : Khinmyatkyi
 * Date : 2015-08-18 09:27:03
 *
 * @package CustomerRelation\Helper
 */
class PaymentHelper extends Form
{

    protected $dbAdapter;
    public function __construct($dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }

    protected $form;
    public function getForm(array $currencies, array $contacts,
                            array $contracts, array $statusList)
    {
        if(!$this->form){
            $form = new Form();
            $paymentId = new Element\Hidden('paymentId');
            $form->add($paymentId);

            $contractId = new Element\Select();
            $contractId->setName('contractId')
                ->setLabel('Contract')
                ->setAttribute('class', 'form-control')
                ->setEmptyOption("--Contract--")
                ->setValueOptions($contracts);
            $form->add($contractId);

            $type = new Element\Text('type');
            $type->setAttribute('class', 'form-control');
            $type->setLabel('Type');
            $form->add($type);

            $amount = new Element\Number('amount');
            $amount->setAttributes(array(
                'min' => '0',
                'max' => '99999999999',
                'step' => '0.01',
            ));
            $amount->setLabel('Amount');
            $form->add($amount);

            $currencyId = new Element\Select();
            $currencyId->setName('currencyId')
                ->setLabel('Currency')
                ->setAttribute('class', 'form-control')
                ->setEmptyOption("--Choose Currency--")
                ->setValueOptions($currencies);
            $form->add($currencyId);

            $paymentDate = new Element\Date('paymentDate');
            $paymentDate->setAttributes(array(
                'allowPastDates' => true,
                'momentConfig' => array('format' => 'YYYY-MM-DD'),
            ));
            $paymentDate->setLabel('Payment Date');
            $form->add($paymentDate);

            $txtStatus=new Element\Select();
            $txtStatus->setName('status')
                ->setLabel('Status')
                ->setAttribute('class','form-control')
                ->setValueOptions($statusList);
            $form->add($txtStatus);

            $staffId = new Element\Select('staffId');
            $staffId->setAttribute('class', 'form-control');
            $staffId->setLabel('Staff Id');
            $form->add($staffId);

            $contactId = new Element\Select();
            $contactId->setName('contactId')
                ->setLabel('Contact')
                ->setAttribute('class', 'form-control')
                ->setEmptyOption("--Contact Name--")
                ->setValueOptions($contacts);
            $form->add($contactId);

            $remark = new Element\Textarea('remark');
            $remark->setAttribute('class', 'form-control');
            $remark->setLabel('Remark(*)');
            $form->add($remark);

            $this->form = $form;
        }
        return $this->form;
    }

    public function setForm(Form $form)
    {
        $this->form = $form;
    }

    public function getInputFilter()
    {
        if(!$this->inputFilter){
            $filter = new InputFilter();
            $filter->add(array(
                'name' => 'paymentId',
                'required' => true,
                'validators' => array(array('name' => 'Zend\I18n\Validator\IsInt')),
            ));
            $filter->add(array(
                'name' => 'contractId',
                'required' => true,
                'validators' => array(array('name' => 'Zend\I18n\Validator\IsInt')),
            ));
            $filter->add(array(
                'name' => 'type',
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
                'name' => 'amount',
                'required' => true,
                'validators' => array(array('name' => 'Zend\I18n\Validator\IsInt')),
            ));
            $filter->add(array(
                'name' => 'currencyId',
                'required' => true,
                'validators' => array(array('name' => 'Zend\I18n\Validator\IsInt')),
            ));
            $filter->add(array(
                'name' => 'paymentDate',
                'required' => true,
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
            $filter->add(array(
                'name' => 'staffId',
                'required' => true,
                'validators' => array(array('name' => 'Zend\I18n\Validator\IsInt')),
            ));
            $filter->add(array(
                'name' => 'contactId',
                'required' => true,
                'validators' => array(array('name' => 'Zend\I18n\Validator\IsInt')),
            ));
            $filter->add(array(
                'name' => 'remark',
                'required' => false,
            ));
            $this->inputFilter = $filter;
        }
        return $this->inputFilter;
    }

    public function setInputFilter(InputFilterInterface $filter)
    {
        $this->inputFilter = $filter;
    }


}