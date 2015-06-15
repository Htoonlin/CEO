<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 3/30/2015
 * Time: 2:29 PM
 */

namespace Application\Helper\View;


use Zend\View\Helper\AbstractHelper;

class GridHeaderCell extends AbstractHelper
{
    protected $title;
    protected $col;
    protected $sort;
    protected $sortBy;
    protected $filter;
    protected $icon;
    protected $page;
    protected $url;

    public function __invoke($title, $col = '', $filter = '', $page = '', $sort = '', $sortBy = '', $url = '')
    {
        $this->title = $title;
        $this->page = $page;
        $this->col = $col;
        $this->sort = $sort;
        $this->sortBy = $sortBy;
        $this->filter = $filter;
        $this->url = empty($url) ? $this->getView()->url() : $url;

        return $this->render();
    }

    public function render()
    {
        $html = '';
        if(empty($this->col)){
            $query = '#';
        }else{
            $query = $this->url . '?page=' . $this->page . '&filter=' . $this->filter . '&sort=' . $this->col . '&by=';
            $icon = 'fa fa-';
            if($this->sort == $this->col) {
                $query .= ($this->sortBy == 'asc') ? 'desc' : 'asc';
                $icon .= ($this->sortBy == 'asc') ? 'caret-up' : 'caret-down';
            }
            else {
                $query .= 'asc';
                $icon .= 'sort';
            }
            $html .= '<span class="' . $icon . '"></span> ';
        }

        $html .= '<a href="' . $query . '">' . $this->title . '</a>';

        return $html;
    }
}