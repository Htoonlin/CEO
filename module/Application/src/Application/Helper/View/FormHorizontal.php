<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 1/22/2015
 * Time: 2:33 PM
 */

namespace Application\Helper\View;

use Zend\Form\Element\Button;
use Zend\Form\Element\Captcha;
use Zend\Form\Element\Checkbox;
use Zend\Form\Element\MonthSelect;
use Zend\Form\Element\Radio;
use Zend\Form\ElementInterface;
use Zend\Form\LabelAwareInterface;
use Zend\Form\View\Helper\AbstractHelper;
use Zend\Form\View\Helper\FormLabel;
use Zend\Form\View\Helper\FormRow;

class FormHorizontal extends FormRow
{
    /**
     * The class that is added to element that have errors
     *
     * @var string
     */
    protected $inputErrorClass = 'input-error';

    /**
     * Utility form helper that renders a label (if it exists), an element and errors
     *
     * @param  ElementInterface $element
     * @throws \Zend\Form\Exception\DomainException
     * @return string
     */
    public function render(ElementInterface $element, $labelPosition  = null)
    {
        $escapeHtmlHelper    = $this->getEscapeHtmlHelper();
        $elementHelper       = $this->getElementHelper();
        $elementErrorsHelper = $this->getElementErrorsHelper();

        $label           = $element->getLabel();
        $inputErrorClass = $this->getInputErrorClass();

        if (isset($label) && '' !== $label) {
            // Translate the label
            if (null !== ($translator = $this->getTranslator())) {
                $label = $translator->translate($label, $this->getTranslatorTextDomain());
            }
        }

        // Does this element have errors ?
        if (count($element->getMessages()) > 0 && !empty($inputErrorClass)) {
            $classAttributes = ($element->hasAttribute('class') ? $element->getAttribute('class') . ' ' : '');
            $classAttributes = $classAttributes . $inputErrorClass;

            $element->setAttribute('class', $classAttributes);
        }

        if ($this->partial) {
            $vars = array(
                'element'           => $element,
                'label'             => $label,
                'labelAttributes'   => $this->labelAttributes,
                'labelPosition'     => $this->labelPosition,
                'renderErrors'      => $this->renderErrors,
            );

            return $this->view->render($this->partial, $vars);
        }

        $rowOpen = '<div class="form-group">';
        $rowClose = '</div>';

        $elementString = $elementHelper->render($element);

        if ($elementErrorsHelper->render($element)) {
            $rowOpen = '<div class="form-group has-error">';
            $elementErrors = $elementErrorsHelper
                ->setMessageOpenFormat('<p class="text-danger">')
                ->setMessageSeparatorString('<br />')
                ->setMessageCloseString('</p>')
                ->render($element);
            $elementString .= $elementErrors;
        }

        // hidden elements do not need a <label> -https://github.com/zendframework/zf2/issues/5607
        $type = $element->getAttribute('type');
        if (isset($label) && '' !== $label && $type !== 'hidden') {

            if (! $element instanceof LabelAwareInterface || ! $element->getLabelOption('disable_html_escape')) {
                $label = $escapeHtmlHelper($label);
            }

            if ($label !== '' && (!$element->hasAttribute('id'))
                || ($element instanceof LabelAwareInterface && $element->getLabelOption('always_wrap'))
            ) {
                $label = '<label class="col-sm-3 control-label">' . $label . '</label>';
            }

            $elementString = '<div class="col-sm-9">' . $elementString . '</div>';

            // Button element is a special case, because label is always rendered inside it
            if ($element instanceof Button || $element instanceof Checkbox || $element instanceof Radio) {
                $label = '<div class="col-sm-3"></div>';
            }

            $markup = $label . $elementString;

        } else {
            $markup = $elementString;
        }

        return $rowOpen . $markup . $rowClose;
    }

}