<?php

/**
 * Created by PhpStorm.
 * User: Htoonlin
 * Date: 2015-08-30
 * Time: 07:39 PM
 */
namespace Core\Model;

use Core\Helper\Entity\TreeViewEntityInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Paginator\Paginator;
use Zend\Stdlib\ArrayObject;
use Zend\Stdlib\ArraySerializableInterface;
use Zend\View\Renderer\JsonRenderer;
use Zend\View\ViewEvent;

class SundewApiRenderer extends JsonRenderer
{
    protected $referenceLink = 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html';

    function setReferenceLink($referenceLink){
        $this->referenceLink = $referenceLink;
    }

    public function getReferenceLink(){
        return $this->referenceLink;
    }

    /**
     * @var ViewEvent
     */
    protected $viewEvent;

    /**
     * @param ViewEvent $event
     */
    function setViewEvent(ViewEvent $event){
        $this->viewEvent = $event;
    }

    /**
     * @return ViewEvent
     */
    function getViewEvent(){
        return $this->viewEvent;
    }

    /**
     * @var Paginator|ArraySerializableInterface|TreeViewEntityInterface
     */
    protected $renderType;


    /**
     * @param $renderType
     */
    function setRenderType($renderType){
        $this->renderType = $renderType;
    }

    /**
     * @return TreeViewEntityInterface|Paginator|ArraySerializableInterface
     */
    function getRenderType(){
        return $this->renderType;
    }

    /**
     * @param string|\Zend\View\Model\ModelInterface $nameOrModel
     * @param null $values
     * @return string
     */
    public function render($nameOrModel, $values = null){
        if(!$nameOrModel instanceof ApiModel){
            return parent::render($nameOrModel, $values);
        }

        if($nameOrModel->getStatusCode() >= 400 && $nameOrModel->getStatusCode() < 511){
            return parent::render($this->renderProblem($nameOrModel));
        }

        $responseData = $nameOrModel->getResponseData();

        if($nameOrModel->getResponseData() instanceof Paginator) {
            $this->renderPaginator($nameOrModel);
        }else{
            if($responseData instanceof ArraySerializableInterface){
                $responseData = $this->extractEntity($responseData);
            }

            if($responseData instanceof HydratingResultSet){
                $resultArray = array();
                foreach($responseData as $entity){
                    $resultArray[] = $this->extractEntity($entity);
                }
                $responseData = $resultArray;
            }
            $nameOrModel->setVariables(array(
                'data' => $responseData,
                'status' => $this->createStatus($nameOrModel)
            ));
        }

        return parent::render($nameOrModel, $values);
    }

    public function extractEntity(ArraySerializableInterface $entity){
        $result = $entity->getArrayCopy();
        return $result;
    }

    public function renderProblem(ApiModel $nameOrModel){
        $response = $this->viewEvent->getResponse();
        if($response instanceof Response){
            $response->setStatusCode($nameOrModel->getStatusCode());
        }

        return array(
            'status' => $this->createStatus($nameOrModel)
        );
    }

    protected function createStatus(ApiModel $nameOrModel)
    {
        $response = $this->viewEvent->getResponse();
        if($response instanceof Response){
            $response->setStatusCode($nameOrModel->getStatusCode());
        }

        $message = empty($nameOrModel->getStatusMessage()) ? $response->getReasonPhrase() : $nameOrModel->getStatusMessage();
        return array(
            'code' => $nameOrModel->getStatusCode(),
            'message' => $message,
            'referenceLink' => $this->referenceLink,
        );
    }

    protected function renderTreeView(ApiModel $nameOrModel){
        $data = $nameOrModel->getResponseData();
        $result = array(
            'data' => $this->extractTreeView($data),
            'status' => $this->createStatus($nameOrModel)
        );

        $nameOrModel->setVariables($result);
    }

    protected function extractTreeView(array $data)
    {
        $result = array();

        foreach($data as $entity){
            if(!$entity instanceof TreeViewEntityInterface){
                continue;
            }
            $children = null;
            if($entity->hasChildren()){
                $children = $this->extractTreeView($entity->getChildren());
            }

            if($entity instanceof ArraySerializableInterface){
                $entity = $entity->getArrayCopy();
            }
            $entity['children'] = $children;
            $result[] = $entity;
        }

        return $result;
    }

    protected function renderPaginator(ApiModel $nameOrModel){
        $responseData = $nameOrModel->getResponseData();

        if(!$responseData instanceof Paginator){
            return;
        }

        $request = $this->viewEvent->getRequest();
        $options = array();
        if($request instanceof Request){

            $options['paging'] = array(
                'current' => $responseData->getCurrentPageNumber(),
                'total' => $responseData->getTotalItemCount(),
                'pageSize' => $responseData->getItemCountPerPage()
            );

            if(!empty($request->getQuery('sort', ''))){
                $options['sort'] = $request->getQuery('sort', '') . ' ' . $request->getQuery('by', 'asc');
            }
            if(!empty($request->getQuery('filter', ''))){
                $options['filter'] = $request->getQuery('filter', '');
            }
        }


        $result = array(
            'options' => $options,
            'data' => $responseData->getCurrentItems(),
            'status' => $this->createStatus($nameOrModel),
        );
        $nameOrModel->setVariables($result);
    }
}