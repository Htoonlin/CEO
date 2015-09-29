<?php
namespace Application\ApiController;

use Application\DataAccess\UserDataAccess;
use Application\Entity\User;
use Application\Helper\AuthHelper;
use Core\Model\ApiModel;
use Core\SundewApiController;
use Zend\InputFilter\InputFilterInterface;
use Zend\Json\Json;
use Zend\Math\Rand;

/**
 * Created by PhpStorm.
 * User: Htoonlin
 * Date: 2015-09-22
 * Time: 02:41 PM
 */
class UserApiController extends SundewApiController
{
    /**
     * @return UserDataAccess
     */
    private function userTable()
    {
        return new UserDataAccess($this->getDbAdapter(), $this->getUser()->getUserId());
    }

    /**
     * @return ApiModel
     */
    public function getIndex()
    {
        $page = (int) $this->getContent('page', 1);
        $sort = $this->getContent('sort', 'userName');
        $sortBy = $this->getContent('by', 'asc');
        $filter = $this->getContent('filter', '');
        $pageSize = (int)$this->getContent('size', 10);

        $paginator = $this->userTable()->fetchAll(true,$filter, $sort, $sortBy);

        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($pageSize);

        return $paginator;
    }
}