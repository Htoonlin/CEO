<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 4/6/2015
 * Time: 3:36 PM
 */

namespace Core\Helper\View;


use Zend\View\Helper\AbstractHelper;

class FormLoader extends AbstractHelper
{
    public function __invoke()
    {
        return $this->render();
    }

    public function render()
    {
        return '<div class="loader" data-initialize="loader"></div>';
    }
}