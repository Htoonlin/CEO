<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 6/5/2015
 * Time: 4:39 PM
 */

namespace Application\Entity;


class Calendar
{
    protected $calendarId;
    public function getCalendarId(){return $this->calendarId;}
    public function setCalendarId($value){$this->calendarId = $value; }

    protected $day;
    public function getDay(){return $this->day;}
    public function setDay($value){$this->day = $value;}

    protected $month;
    public function getMonth(){return $this->month;}
    public function setMonth($value){$this->month = $value;}

    protected $year;
    public function getYear(){return $this->year;}
    public function setYear($value){$this->year = $value;}

    protected $type;
    public function getType(){return $this->type;}
    public function setType($value){$this->type = $value;}

    protected $title;
    public function getTitle(){return $this->title;}
    public function setTitle($value){$this->title = $value;}

    protected $linkId;
    public function getLinkId(){return $this->linkId;}
    public function setLinkId($value){$this->linkId = $value;}

    public function getDate(){
        return strtotime(sprintf('%04d-%02d-%02d', $this->year, $this->month, $this->day));
    }
    public function getDateString()
    {
        return sprintf('%04d-%02d-%02d', $this->year, $this->month, $this->day);
    }
    public function setDate($value){
        if(is_string($value)){
            $value = strtotime($value);
        }
        $this->year = (int)date('Y', $value);
        $this->month = (int)date('m', $value);
        $this->day = (int)date('d', $value);
    }

    public function exchangeArray(array $data)
    {
        $this->calendarId = (!empty($data['calendarId'])) ? $data['calendarId'] : null;
        $this->day = (!empty($data['day'])) ? $data['day'] : null;
        $this->month = (!empty($data['month'])) ? $data['month'] : null;
        $this->year = (!empty($data['year'])) ? $data['year'] : null;
        $this->type = (!empty($data['type'])) ? $data['type'] : null;
        $this->title = (!empty($data['title'])) ? $data['title'] : null;
        $this->linkId = (!empty($data['linkId'])) ? $data['linkId'] : null;
        if(!empty($data['date'])){
            $this->setDate($data['date']);
        }
    }

    public function getArrayCopy()
    {
        return array(
            'calendarId' => $this->calendarId,
            'day' => $this->day,
            'month' => $this->month,
            'year' => $this->year,
            'type' => $this->type,
            'title' => $this->title,
            'linkId' => $this->linkId
        );
    }
}