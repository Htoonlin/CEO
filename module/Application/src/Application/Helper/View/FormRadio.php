<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 4/6/2015
 * Time: 3:39 PM
 */

namespace Application\Helper\View;

use Zend\Form\ElementInterface;
use Zend\Form\Element\Checkbox as CheckboxElement;
use Zend\Form\Exception;
use Zend\Form\View\Helper\FormInput;

class FormRadio extends FormInput{

    public function render(ElementInterface $element)
    {
        $name = $element->getName();
        if (empty($name) && $name !== 0) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has an assigned name; none discovered',
                __METHOD__
            ));
        }

        $label = $element->getLabel();

        $attributes            = $element->getAttributes();
        $attributes['name']    = $name;
        $attributes['type']    = $this->getInputType();
        $attributes['class'] = 'sr-only';
        $attributes['value'] = $element->getValue();
        $closingBracket        = $this->getInlineClosingBracket();

        $input = sprintf(
            '<input %s%s',
            $this->createAttributesString($attributes),
            $closingBracket
        );

        $rendered = '<div class="radio"><label class="radio-custom" data-initialize="radio">' .
            $input . '</label>' . $label . '</div>';

        return $rendered;
    }

    /**
     * Return input type
     *
     * @return string
     */
    protected function getInputType()
    {
        return 'radio';
    }
}