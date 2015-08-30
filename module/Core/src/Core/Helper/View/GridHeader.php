<?php
namespace Core\Helper\View;

use Zend\Form\View\Helper\AbstractHelper;
use Zend\Stdlib\RequestInterface;
/**
 *
 * @author htoonlin
 *
 */
class GridHeader extends AbstractHelper
{

    protected $request;
    /**
     *
     * @param RequestInterface $request
     */
    function __construct(RequestInterface $request)
    {
        $this->request = $request;
        $this->validGlobalAttributes['width'] = true;
    }

    /**
     * @param array $fieldList
     * @return string
     */
    public function __invoke(array $fieldList){
        return $this->render($fieldList);
    }

    public function render(array $fieldList){
        $html = '<thead>';
        foreach($fieldList as $field){
            $type = isset($field['type']) ? $field['type'] : 'custom';
            $attrs = '';
            if(isset($field['attr'])){
                $attrs = ' ' . $this->createAttributesString($field['attr']);
            }

            $html .= '<th' . $attrs . '>';
            if($type == 'db'){
                $html .= $this->dbCell($field['value']);
            }else if($type == 'checkbox'){
                $html .= $this->checkCell($field['value']);
            }else{
                $html .= $field['value'];
            }
            $html .= '</th>';
        }
        $html .= '</thead>';
        return $html;
    }

    /**
     * @param array $data
     * @return string
     */
    public function dbCell(array $data){
        $title = isset($data['title']) ? $data['title'] : '';
        $field = isset($data['col']) ? $data['col'] : '';
        $url = isset($data['url']) ? $data['url'] : $this->getView()->url();
        $gridHeaderCell = new GridHeaderCell($this->request);
        return $gridHeaderCell->render($title, $field, $url);
    }

    /**
     * @param array $data
     * @return string
     */
    public function checkCell(array $data){
        $id = isset($data['id']) ? (' id="' . $data['id'] . '"') : '';
        $html = '<div class="checkbox" style="margin:0">';
        $html .= '<label class="checkbox-custom" data-initialize="checkbox">';
        $html .= '<input type="checkbox"' . $id . ' />';
        $html .= '</label>';
        $html .= '</div>';
        return $html;
    }
}

?>