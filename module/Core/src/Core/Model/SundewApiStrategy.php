<?php

/**
 * Created by PhpStorm.
 * User: Htoonlin
 * Date: 2015-08-30
 * Time: 07:38 PM
 */
namespace Core\Model;

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

        $this->renderer->setViewEvent($event);
        return $this->renderer;
    }
}