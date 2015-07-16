<?php
/**
 * Created by PhpStorm.
 * User: Lwin
 * Date: 4/28/2015
 * Time: 11:11 AM
 */
namespace CustomerRelation\DataAccess;

use Application\Service\SundewTableGateway;
use CustomerRelation\Entity\Contact;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Stdlib\Hydrator\ClassMethods;

class ContactDataAccess extends SundewTableGateway
{
    public function __construct(Adapter $dbAdapter)
    {
        $this->table="tbl_cr_contact";

        $this->adapter=$dbAdapter;

        $this->resultSetPrototype=new HydratingResultSet(new ClassMethods(), new Contact());
        $this->initialize();
    }

    public function fetchAll($paginated=false, $filter='', $orderBy='contactName', $order='ASC')
    {
        $view='vw_cr_contact';
        if($paginated){
            return $this->paginate($filter, $orderBy, $order, $view);
        }

        $contactView=new TableGateway($view, $this->adapter);
        return $contactView->select();
    }

    public function getComboData($key, $value)
    {
        $results=$this->select();
        $selectData=array();
        foreach($results as $contact){
            $data=$contact->getArrayCopy();
            $selectData[$data[$key]]=$data[$value];
        }
        return $selectData;
    }

    public function getContact($id)
    {
        $id=(int)$id;
        $rowset=$this->select(array('contactId'=>$id));
        return $rowset->current();
    }

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

    public function deleteContact($id)
    {
        $this->delete(array('contactId'=>(int)$id));
    }
}