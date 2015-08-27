<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 3/30/2015
 * Time: 2:29 PM
 */

namespace Application\Helper\View;


use Zend\Stdlib\RequestInterface;
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
    protected $pageSize;

    protected $request;

    public function __construct(RequestInterface $request){
        $this->request = $request;
    }

    public function __invoke($title, $col = '', $url = '')
    {
        return $this->render($title, $col, $url);
    }

    public function render($title, $col = '', $url = '')
    {
        $this->title = $title;
        $this->col = $col;
        $this->url = empty($url) ? $this->getView()->url() : $url;

        $this->page = $this->request->getQuery('page', 1);
        $this->sort = $this->request->getQuery('sort', '');
        $this->sortBy = $this->request->getQuery('by', '');
        $this->filter = $this->request->getQuery('filter', '');
        $this->pageSize = $this->request->getQuery('size', 10);

        $html = '';
        if(empty($this->col)){
            $query = '#';
        }else{
            $query = $this->url . '?page=' . $this->page . '&size=' . $this->pageSize
                . '&filter=' . $this->filter . '&sort=' . $this->col . '&by=';
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