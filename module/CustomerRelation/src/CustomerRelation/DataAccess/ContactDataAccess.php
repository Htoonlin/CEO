<?php
/**
 * Created by PhpStorm.
 * User: Lwin
 * Date: 4/28/2015
 * Time: 11:11 AM
 */
namespace CustomerRelation\DataAccess;

use HumanResource\Entity\Position;
use CustomerRelation\Entity\Contact;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Db\Sql\Where;

class ContactDataAccess extends AbstractTableGateway
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
            $select=new Select($view);
            $select->order($orderBy.' '. $order);
            $where=new Where();
            $where->literal("Concat_ws(' ',contactName, Tag, Phone) LIKE ?", '%'. $filter . '%');
            $select->where($where);
            $paginatorAdapter=new DbSelect($select, $this->adapter);
            $paginator=new Paginator($paginatorAdapter);
            return $paginator;
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