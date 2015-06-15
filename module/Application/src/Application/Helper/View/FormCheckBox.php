<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 4/6/2015
 * Time: 2:49 PM
 */

namespace Application\Helper\View;

use Zend\Form\ElementInterface;
use Zend\Form\Element\Checkbox as CheckboxElement;
use Zend\Form\Exception;
use Zend\Form\View\Helper\FormInput;

class FormCheckBox extends FormInput{
    /**
     * Render a form <input> element from the provided $element
     *
     * @param  ElementInterface $element
     * @throws Exception\InvalidArgumentException
     * @throws Exception\DomainException
     * @return string
     */
    public function render(ElementInterface $element)
    {
        if (!$element instanceof CheckboxElement) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s requires that the element is of type Zend\Form\Element\Checkbox',
                __METHOD__
            ));
        }

        $name = $element->getName();
        if (empty($name) && $name !== 0) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has an assigned name; none discovered',
                __METHOD__
            ));
        }

        $attributes            = $element->getAttributes();
        $attributes['name']    = $name;
        $attributes['type']    = $this->getInputType();
        $attributes['value']   = $element->getCheckedValue();
        $attributes['class'] = 'sr-only';
        $closingBracket        = $this->getInlineClosingBracket();

        if ($element->isChecked()) {
            $attributes['checked'] = 'checked';
        }

        $input = sprintf(
            '<input %s%s',
            $this->createAttributesString($attributes),
            $closingBracket
        );

        $label = $element->getLabel();

        $rendered = '<div class="checkbox"><label class="checkbox-custom" data-initialize="checkbox">' .
                    $input . '<span class="checkbox-label">' . $label . '</span>' . '</label></div>';

        if ($element->useHiddenElement()) {
            $hiddenAttributes = array(
                'name'  => $attributes['name'],
                'value' => $element->getUncheckedValue(),
            );

            $rendered = sprintf(
                    '<input type="hidden" %s%s',
                    $this->createAttributesString($hiddenAttributes),
                    $closingBracket
                ) . $rendered;
        }

        return $rendered;
    }

    /**
     * Return input type
     *
     * @return string
     */
    protected function getInputType()
    {
        return 'checkbox';
    }
}