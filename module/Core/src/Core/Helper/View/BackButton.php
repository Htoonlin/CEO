<?php
/**
 * Created by PhpStorm.
 * User: Htoonlin
 * Date: 8/15/2015
 * Time: 12:59 PM
 */

namespace Core\Helper\View;

use Zend\Form\View\Helper\AbstractHelper;
use Zend\Http\Header\Referer;
use Zend\Http\Request;
use Zend\Mvc\Router\RouteMatch;
use Zend\Stdlib\RequestInterface;
use Zend\Uri\Http;

class BackButton extends AbstractHelper
{
    /**
     * @var Request
     */
    protected $request;
    protected $routeMatch;
    public function __construct(Request $request, RouteMatch $match){
        $this->request = $request;
        $this->routeMatch = $match;
    }

    public function __invoke($text = 'Go back', $icon = 'fa fa-arrow-circle-o-left', array $attr = array()){
        if(!isset($attr['class'])){
            $attr['class'] = 'btn btn-default';
        }
        return $this->render($text, $icon, $attr);
    }

    private function clean($uri, $char){
        $pos = strpos($uri, $char);
        if($pos > 0){
            return substr($uri, 0, $pos);
        }
        return $uri;
    }

    private function checkUri($uriA, $uriB){
        //Remove js parameters
        $uriA = $this->clean($uriA, '#');
        $uriB = $this->clean($uriB, '#');

        //Remove query
        $uriA = $this->clean($uriA, '?');
        $uriB = $this->clean($uriB, '?');

        return (strcmp($uriA, $uriB) == 0);
    }

    public function render($text, $icon = '', array $attr){
        $referer = $this->request->getHeader('Referer');
        if($referer){
            $href = $referer->getUri();
        }else{
            $href = $this->getView()->url($this->routeMatch->getMatchedRouteName());
        }

        $currentUri = $this->request->getUriString();
        if($this->checkUri($currentUri, $href)){
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