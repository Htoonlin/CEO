<?php

/**
 * Created by PhpStorm.
 * User: Htoonlin
 * Date: 2015-08-30
 * Time: 07:38 PM
 */
namespace Core\Model;

use Zend\Http\Request;
use Zend\View\Strategy\JsonStrategy;
use Zend\View\ViewEvent;

class SundewApiStrategy extends JsonStrategy
{
    function __construct(SundewApiRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function selectRenderer(ViewEvent $event)
    {
        $model = $event->getModel();
        if(!$model instanceof ApiModel){
            return;
        }
        //$this->validateRequest($event);
        $this->renderer->setViewEvent($event);
        return $this->renderer;
    }

    private function validateRequest(ViewEvent $event){
        $request = $event->getRequest();
        $model = $event->getModel();
        if(!$model instanceof ApiModel || !$request instanceof Request){
            return;
        }

        $mediaType = $request->getHeader('Content-Type');
        if(!$mediaType->match($model->getAllowMediaTypes())){
            $model->setStatusCode(415);
        }

        if(!in_array(strtoupper($request->getMethod()), $model->getAllowMethods())){
            $model->setStatusCode(405);
            return;
        }

        if(!$model->getAllowFlashRequest() && $request->isFlashRequest()){
            $model->setStatusCode(400);
            $model->setStatusMessage('Sorry! Doesn\'t supports Flash Request.');
            return;
        }

        if($model->isAjaxOnly() && !$request->isXmlHttpRequest()){
            $model->setStatusCode(400);
            $model->setStatusMessage('Sorry! Doesn\'t supports other request except AJAX.');
            return;
        }

    }
}