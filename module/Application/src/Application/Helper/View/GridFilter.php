<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 7/3/2015
 * Time: 11:57 AM
 */

namespace Application\Helper\View;


use Zend\Stdlib\RequestInterface;
use Zend\View\Helper\AbstractHelper;

class GridFilter extends AbstractHelper{
    protected $request;
    public function __construct(RequestInterface $request){
        $this->request = $request;
    }

    protected $page;
    protected $sort;
    protected $filter;
    protected $pageSize;
    protected $sortBy;
    protected $url;
    protected $name;

    public function __invoke($name, $url = ''){
        $this->name = $name;
        $this->url = empty($url) ? $this->getView()->url() : $url;

        $this->page = $this->request->getQuery('page', 1);
        $this->sort = $this->request->getQuery('sort', '');
        $this->sortBy = $this->request->getQuery('by', 'asc');
        $this->filter = $this->request->getQuery('filter', '');
        $this->pageSize = $this->request->getQuery('size', 10);

        try{
            if(empty($this->sort)){
                $this->sort = $this->getView()->sort;
            }
        }catch(\Exception $ex){
            throw new \Exception('There is not sort variable in View');
        }

        return $this->render();
    }

    public function render(){
        $goto = $this->url . '?page=1&size=' . $this->pageSize;
        $goto .= '&sort=' . $this->sort;
        $goto .= '&by=' . $this->sortBy;
        $goto .= '&filter=';

        $html = <<<js
<script type="text/javascript">
    $(document).ready(function(){
        $("#{$this->name}").keydown(function(e){
            if(e.which == 13){
                var filter = $(this).val();
                window.location.href = '{$goto}' + filter;
            }
        });
    });
</script>
<div class="input-group">
    <span class="input-group-addon">
        <span class="glyphicon glyphicon-search"></span>
    </span>
    <input class="form-control" id="{$this->name}" value="{$this->filter}" type="text">
</div>
js;
        return $html;
    }
}