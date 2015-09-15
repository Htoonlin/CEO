<?php
/**
 * Created by PhpStorm.
 * User: Lwin
 * Date: 4/28/2015
 * Time: 11:11 AM
 */
namespace CustomerRelation\DataAccess;

use Core\SundewTableGateway;
use CustomerRelation\Entity\Contact;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Stdlib\Hydrator\ClassMethods;

/**
 * Class ContactDataAccess
 * @package CustomerRelation\DataAccess
 */
class ContactDataAccess extends SundewTableGateway
{
    /**
     * @param Adapter $dbAdapter
     * @param Int $userId
     */
    public function __construct(Adapter $dbAdapter, $userId)
    {
        $this->table="tbl_cr_contact";

        $this->adapter=$dbAdapter;

        $this->resultSetPrototype=new HydratingResultSet(new ClassMethods(), new Contact());
        $this->initialize();

        $this->useSoftDelete = true;
        parent::__construct($userId);
    }

    /**
     * @param bool $paginated
     * @param string $filter
     * @param string $orderBy
     * @param string $order
     * @return \Zend\Db\ResultSet\ResultSet|\Zend\Paginator\Paginator
     * @throws \Exception
     */
    public function fetchAll($paginated=false, $filter='', $orderBy='contactName', $order='ASC')
    {
        $view='vw_cr_contact';
        if($paginated){
            return $this->paginate($filter, $orderBy, $order, $view);
        }

        $contactView=new TableGateway($view, $this->adapter);
        return $contactView->select();
    }

    /**
     * @param $id
     * @return array|\ArrayObject|null
     */
    public function getContact($id)
    {
        $id=(int)$id;
        $rowset=$this->select(array('contactId'=>$id));
        return $rowset->current();
    }

    /**
     * @param Contact $contact
     * @return Contact
     */
    public function saveContact(Contact $contact)
    {
        $id=$contact->getContactId();
        $data=$contact->getArrayCopy();
        if($id>0){
            $this->update($data, array('contactId'=>$id));
        }else{
            unset($data['contactId']);
            $this->insert($data);
        }
        if(!$contact->getContactId()){
            $contact->setContactId($this->getLastInsertValue());
        }
        return $contact;
    }

    /**
     * @param $id
     */
    public function deleteContact($id)
    {
        $this->delete(array('contactId'=>(int)$id));
    }
}