<?php
namespace Core\Helper\View;

use Zend\Form\ElementInterface;
use Zend\Form\Exception;
use Zend\Form\View\Helper\FormInput;

class FormNumber extends FormInput
{
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
        $name = $element->getName();
        if (empty($name) && $name !== 0) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has an assigned name; none discovered',
                __METHOD__
            ));
        }

        $attributes            = $element->getAttributes();
        $attributes['name']    = $name;
        $attributes['type']    = 'text';
        $divClass = isset($attributes['class']) ? $attributes['class'] : 'form-control';
        $attributes['class']   = 'form-control input-mini spinbox-input';
        $attributes['value']   = $element->getValue();
        $closingBracket        = $this->getInlineClosingBracket();

        $jsAttributes = array();
        if(isset($attributes['min']))
        {
            $jsAttributes['min'] = $attributes['min'];
            unset($attributes['min']);
        }
        if(isset($attributes['max']))
        {
            $jsAttributes['max'] = $attributes['max'];
            unset($attributes['max']);
        }
        if(isset($attributes['step']))
        {
            $jsAttributes['step'] = $attributes['step'];
            unset($attributes['step']);
        }
        if(isset($attributes['units']))
        {
            $jsAttributes['units'] = $attributes['units'];
            unset($attributes['units']);
        }

        $script = $this->scriptAttributes($name, $jsAttributes);

        $input = sprintf(
            '<input %s%s',
            $this->createAttributesString($attributes),
            $closingBracket
        );

        $spinBox = '';
        $spinBox .= '<div class="spinbox-buttons btn-group btn-group-vertical">';
        $spinBox .= '<button type="button" class="btn btn-default spinbox-up btn-xs">';
        $spinBox .= '<span class="glyphicon glyphicon-chevron-up"></span><span class="sr-only">Increase</span>';
        $spinBox .= '</button>';
        $spinBox .= '<button type="button" class="btn btn-default spinbox-down btn-xs">';
        $spinBox .= '<span class="glyphicon glyphicon-chevron-down"></span><span class="sr-only">Decrease</span>';
        $spinBox .= '</button>';
        $spinBox .= '</div>';

        $rendered = '<div class="' . $divClass . '">' . $script . '<div class="spinbox" id="' . $name . '">' .
            $input . $spinBox . '</div></div>';

        return $rendered;
    }

    private function scriptAttributes($name, $attributes)
    {
        $json = json_encode($attributes);
        $script = '<script type="text/javascript">';
        $script .= "$('document').ready(function(){";
        $script .= "$('div#$name').spinbox(" . $json . ")";
        $script .= "});";
        $script .= '</script>';

        return $script;
    }

    /**
     * Attributes valid for the input tag type="number"
     *
     * @var array
     */
    protected $validTagAttributes = array(
        'name'           => true,
        'autocomplete'   => true,
        'autofocus'      => true,
        'disabled'       => true,
        'form'           => true,
        'list'           => true,
        'max'            => true,
        'min'            => true,
        'step'           => true,
        'placeholder'    => true,
        'readonly'       => true,
        'required'       => true,
        'type'           => true,
        'value'          => true,
        'units'          => true,
    );

    /**
     * Determine input type to use
     *
     * @param  ElementInterface $element
     * @return string
     */
    protected function getType(ElementInterface $element)
    {
        return 'number';
    }
}
