<?php
/**
 * Created by PhpStorm.
 * User: Htoonlin
 * Date: 2015-08-26
 * Time: 09:51 AM
 */

namespace Application\Helper\View;


use Zend\Paginator\Paginator;
use Zend\Stdlib\RequestInterface;
use Zend\View\Helper\AbstractHelper;

class SundewGrid extends AbstractHelper
{
    protected $request;

    function __construct(RequestInterface $request)
    {
        $this->request = $request;
        $this->validGlobalAttributes['width'] = true;
    }

    public function __invoke($name, array $columns, Paginator $data, $multiSelect = true)
    {
        $this->getView()->headScript()->appendFile(APP_PATH . '/js/grid.js');
        return $this->render($name, $columns, $data, $multiSelect);
    }

    public function render($name, array $columns, Paginator $data, $mutliSelect = true)
    {
        $html = '<div class="grid" id="' . $name . '">';

        $html .= '</div>';
        return $html;
    }


}