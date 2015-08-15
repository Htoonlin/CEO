<?php
/**
 * Created by PhpStorm.
 * User: Htoonlin
 * Date: 8/15/2015
 * Time: 12:59 PM
 */

namespace Application\Helper\View;


use Zend\Form\View\Helper\AbstractHelper;
use Zend\Mvc\Router\RouteMatch;
use Zend\Stdlib\RequestInterface;

class BackButton extends AbstractHelper
{
    protected $request;
    protected $routeMatch;
    public function __construct(RequestInterface $request, RouteMatch $match){
        $this->request = $request;
        $this->routeMatch = $match;
    }

    public function __invoke($text = 'Go back', $icon = 'fa fa-arrow-circle-o-left', array $attr = array()){
        if(!isset($attr['class'])){
            $attr['class'] = 'btn btn-default';
        }
        return $this->render($text, $icon, $attr);
    }

    public function render($text, $icon = '', array $attr){
        $referer = $this->request->getHeader('Referer');
        if($referer){
            $href = $referer->getUri();
        }else{
            $href = $this->getView()->url($this->routeMatch->getMatchedRouteName());
        }

        $html = '<a href="' . $href .'"';
        $html .= ' ' . $this->createAttributesString($attr);
        $html .= '>';
        if(!empty($icon)){
            $html .= '<span class="' . $icon . '"></span>&nbsp;';
        }
        $html .= $text;
        $html .= '</a>';
        return $html;
    }
}