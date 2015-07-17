<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 6/9/2015
 * Time: 1:38 PM
 */

namespace HumanResource\Controller;


use Application\DataAccess\CalendarDataAccess;
use Application\DataAccess\CalendarType;
use Application\DataAccess\ConstantDataAccess;
use Application\Entity\Calendar;
use HumanResource\Helper\HolidayHelper;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class HolidayController extends AbstractActionController
{
    private function calendarTable()
    {
        $adapter = $this->getServiceLocator()->get('Sundew\Db\Adapter');
        return new CalendarDataAccess($adapter);
    }

    private function holidayTypeCombo()
    {
        $adapter = $this->getServiceLocator()->get('Sundew\Db\Adapter');
        $dataAccess = new ConstantDataAccess($adapter);
        return $dataAccess->getComboByName('holiday_type');
    }

    public function jsonHolidayAction()
    {
        $year = (int)$this->params()->fromPost('year', date('Y'));
        $holiday = $this->calendarTable()->getHolidayByYear($year);

        return new JsonModel($holiday);
    }

    public function jsonWeeklyHolidayAction()
    {
        $result = $this->calendarTable()->getCalendarByType(CalendarType::holiday_weekly);
        return new JsonModel($result->toArray());
    }

    public function jsonCheckHolidayAction()
    {
        $date = $this->params()->fromQuery('date', date('Y-m-D', time()));
        $isHoliday = $this->calendarTable()->checkHoliday($date);
        return new JsonModel(array("status" => $isHoliday));
    }

    public function indexAction()
    {
        $helper = new HolidayHelper();
        $calendar = new Calendar();
        $form = $helper->getForm($this->holidayTypeCombo());
        $form->bind($calendar);
        $request = $this->getRequest();

        if($request->isPost()){
            $isDelete = $request->getPost('is_delete', 'no');
            $id = (int) $request->getPost('calendarId', 0);
            if($isDelete == 'yes' && $id > 0){
                $this->calendarTable()->delete(array('calendarId' => $id));
                $this->flashMessenger()->addInfoMessage('Delete successful.');
                return $this->redirect()->toRoute("hr_holiday");
            }else{
                $form->setData($request->getPost());
                if($form->isValid()){
                    if($calendar->getType() == 'holiday_w'){
                        $dw = (int)$request->getPost('weekCombo', 0);
                        $calendar->setDay($dw);
                    }
                    $this->calendarTable()->saveCalendar($calendar);
                    $this->flashMessenger()->addSuccessMessage('Save successful!');
                    return $this->redirect()->toRoute("hr_holiday");
                }
            }
        }

        return new ViewModel(array(
            'form' => $form,
        ));
    }
}