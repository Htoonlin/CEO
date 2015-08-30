<?php

namespace Core\Helper\View;

use Zend\Form\ElementInterface;
use Zend\Form\Exception;
use Zend\Form\View\Helper\FormInput;

class FormDate extends FormInput
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
        $attributes['class'] = 'form-control';
        $attributes['value'] = $element->getValue();
        $attributes['date'] = $element->getValue();
        $attributes['readonly'] = 'readonly';
        $closingBracket        = $this->getInlineClosingBracket();

        $jsAttributes = array();
        if(isset($attributes['allowPastDates']))
        {
            $jsAttributes['allowPastDates'] = $attributes['allowPastDates'];
            unset($attributes['allowPastDates']);
        }
        if(isset($attributes['date']))
        {
            $jsAttributes['date'] = $attributes['date'];
            unset($attributes['date']);
        }
        if(isset($attributes['formatDate']))
        {
            $jsAttributes['formatDate'] = $attributes['formatDate'];
            unset($attributes['formatDate']);
        }
        if(isset($attributes['momentConfig']))
        {
            $jsAttributes['momentConfig'] = $attributes['momentConfig'];
            unset($attributes['momentConfig']);
        }
        if(isset($attributes['sameYearOnly']))
        {
            $jsAttributes['sameYearOnly'] = $attributes['sameYearOnly'];
            unset($attributes['sameYearOnly']);
        }

        $script = $this->scriptAttributes($name, $jsAttributes);

        $input = sprintf(
            '<input %s%s',
            $this->createAttributesString($attributes),
            $closingBracket
        );

        $wrapper = '<div class="dropdown-menu dropdown-menu-right datepicker-calendar-wrapper" role="menu">';
        $wrapper .= $this->calendar();
        $wrapper .= $this->monthWheel();
        $wrapper .= '</div>';

        $datePicker = '<div class="input-group">';
        $datePicker .= $input;
        $datePicker .= '<div class="input-group-btn">';
        $datePicker .= '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">';
        $datePicker .= '<span class="glyphicon glyphicon-calendar"></span>';
        $datePicker .= '<span class="sr-only">Toggle Calendar</span>';
        $datePicker .= '</button>';
        $datePicker .= $wrapper;
        $datePicker .= '</div>';
        $datePicker .= '</div>';

        $rendered = $script . '<div class="datepicker" id="' . $name
            . '">' . $datePicker . '</div>';

        return $rendered;
    }

    private function monthWheel()
    {
        return <<<wheel
        <div class="datepicker-wheels" aria-hidden="true">
            <div class="datepicker-wheels-month">
                <h2 class="header">Month</h2>
                <ul>
                    <li data-month="0"><button type="button">Jan</button></li>
                    <li data-month="1"><button type="button">Feb</button></li>
                    <li data-month="2"><button type="button">Mar</button></li>
                    <li data-month="3"><button type="button">Apr</button></li>
                    <li data-month="4"><button type="button">May</button></li>
                    <li data-month="5"><button type="button">Jun</button></li>
                    <li data-month="6"><button type="button">Jul</button></li>
                    <li data-month="7"><button type="button">Aug</button></li>
                    <li data-month="8"><button type="button">Sep</button></li>
                    <li data-month="9"><button type="button">Oct</button></li>
                    <li data-month="10"><button type="button">Nov</button></li>
                    <li data-month="11"><button type="button">Dec</button></li>
                </ul>
            </div>
            <div class="datepicker-wheels-year">
                <h2 class="header">Year</h2>
                <ul></ul>
            </div>
            <div class="datepicker-wheels-footer clearfix">
                <button type="button" class="btn datepicker-wheels-back"><span class="glyphicon glyphicon-arrow-left"></span><span class="sr-only">Return to Calendar</span></button>
                <button type="button" class="btn datepicker-wheels-select">Select <span class="sr-only">Month and Year</span></button>
            </div>
        </div>
wheel;

    }

    private function calendar()
    {
        return <<<calendar
    <div class="datepicker-calendar">
        <div class="datepicker-calendar-header">
            <button type="button" class="prev"><span class="glyphicon glyphicon-chevron-left"></span><span class="sr-only">Previous Month</span></button>
            <button type="button" class="next"><span class="glyphicon glyphicon-chevron-right"></span><span class="sr-only">Next Month</span></button>
            <button type="button" class="title">
                <span class="month">
                    <span data-month="0">January</span>
                    <span data-month="1">February</span>
                    <span data-month="2">March</span>
                    <span data-month="3">April</span>
                    <span data-month="4">May</span>
                    <span data-month="5">June</span>
                    <span data-month="6">July</span>
                    <span data-month="7">August</span>
                    <span data-month="8">September</span>
                    <span data-month="9">October</span>
                    <span data-month="10">November</span>
                    <span data-month="11">December</span>
                </span>
                <span class="year"></span>
            </button>
        </div>
        <table class="datepicker-calendar-days">
            <thead>
                <tr>
                    <th>Su</th>
                    <th>Mo</th>
                    <th>Tu</th>
                    <th>We</th>
                    <th>Th</th>
                    <th>Fr</th>
                    <th>Sa</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <div class="datepicker-calendar-footer">
            <button type="button" class="datepicker-today">Today</button>
        </div>
  </div>
calendar;

    }

    private function scriptAttributes($name, $attributes)
    {
        $json = json_encode($attributes);
        $script = '<script type="text/javascript">';
        $script .= "$('document').ready(function(){";
        $script .= "$('div#$name').datepicker(" . $json . ")";
        $script .= "});";
        $script .= '</script>';

        return $script;
    }

    /**
     * @var array
     */
    protected $validTagAttributes = array(
        'name'           => true,
        'autocomplete'   => true,
        'autofocus'      => true,
        'disabled'       => true,
        'allowPastDates' => true,
        'date'           => true,
        'formatDate'     => true,
        'momentConfig'   => true,
        'sameYearOnly'   => true,
        'placeholder'    => true,
        'readonly'       => true,
        'required'       => true,
        'value'          => true,
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
