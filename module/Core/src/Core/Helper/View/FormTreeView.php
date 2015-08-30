<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 1/21/2015
 * Time: 4:09 PM
 */

namespace Core\Helper\View;

use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\AbstractHelper;

class FormTreeView extends AbstractHelper
{
    public function __invoke(array $entity, $active = null)
    {
        if(!$entity || empty($entity)){
            return '<div class="sundew-tree"></div>';
        }

        return $this->render($entity, $active);
    }

    public function render(array $entity, $active)
    {
        $markup = '';
        try{
           $markup .= $this->getList($entity, $active);
        }catch(\Exception $ex){
            throw $ex;
        }
        return '<div class="sundew-tree">' . $markup . '</div>';
    }

    private function getList($entity, $active)
    {
        $markup = '';
        $escapeHtml = $this->getEscapeHtmlHelper();
        foreach($entity as $record)
        {
            if(is_numeric($active))
                $isActive = ($active > 0 && $active == $record->getValue()) ? 'class="active"' : '';
            else if(is_array($active))
                $isActive = (in_array($record->getValue(), $active)) ? 'class="active"' : '';
            else
                $isActive = '';

            $title = '';
            if(method_exists($record, 'getDescription')){
                $title = $record->getDescription();
            }

            if(!empty($record->description)){
                $title = $record->description;
            }

            $markup .= '<li value="' . $escapeHtml($record->getValue()) . '" ' . $isActive . '>';
            $markup .= $this->getItem($escapeHtml($record->getLabel()), $escapeHtml($record->getUrl()),
                $escapeHtml($record->getIconClass()), $escapeHtml($title));
            if($record->hasChildren())
            {
                $markup .= $this->getList($record->getChildren(), $active);
            }
            $markup .= '</li>';
        }
        return '<ul>' . $markup . '</ul>';
    }

    private function getItem($label, $url, $icon, $title = '')
    {
        $markup = '';
        $markup .= '<div class="tree-item" title="' . $title . '">';
        $markup .= '<span id="icon" class="' . $icon . '"></span>';
        $markup .= '<a href="' . $url . '">';
        $markup .= $label;
        $markup .= '</a>';
        $markup .= '</div>';
        return $markup;
    }
}
