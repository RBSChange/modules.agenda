<?php
class agenda_CalendarItem extends ArrayObject
{
	const INACTIVE = 'inactif inactive';
	const ACTIVE = 'actif-link active-day';

	private $class =  self::INACTIVE;
	private $date = null;
	private $sibling = null;

	public function setSiblingId($val)
	{
		$this->sibling = $val;
	}

	public function getSiblingId()
	{
		return $this->sibling;
	}

	public function getLinkParameters()
	{
		$res = array();
		$res['agendaParam'] = array(K::COMPONENT_ID_ACCESSOR =>  $this->sibling, 'month' => $this->date->getMonth(), 'year' => $this->date->getYear());
		return $res;
	}
	public final function setDate($val)
	{
		$this->date = clone $val;
	}

	/**
	 * @return date_Calendar
	 */
	public final function getDate()
	{
		return $this->date;
	}

	public function getClass()
	{
		return $this->class;
	}

	public function isActive()
	{
		return $this->class == self::ACTIVE;
	}

	public function setActive()
	{
		$this->class = self::ACTIVE;
		return $this;
	}

	public function setInactive()
	{
		$this->class = self::INACTIVE;
		return $this;
	}

	public function isValid()
	{
		return ($this->date instanceof date_Calendar);
	}

	public final function getUrl()
	{
		if (!$this->isValid())
		{
			return null;
		}
		if (!is_null($this->getSiblingId()))
		{
			return LinkHelper::getUrl('agenda', 'ViewList', $this->getLinkParameters());
		}
		return LinkHelper::getCurrentUrl($this->getLinkParameters());
	}

}

class agenda_CalendarDay  extends agenda_CalendarItem
{
	public function getDateAsFormattedString()
	{
		return $this->getDate()->getDay();
	}

	public function getLinkParameters()
	{
		$res = parent::getLinkParameters();
		$res['agendaParam']['listday'] =  $this->getDate()->getTimestamp();
		$res['agendaParam']['listweek'] =  null;
		$res['agendaParam']['listmonth'] =  null;
		return $res;
	}
	
	function getDay()
	{
		$day = $this->getDate()->getDay(); 
		if (strlen($day) === 1)
		{
			return "0".$day;
		}
		return $day;
	}

	public function getYear()
	{
		return $this->getDate()->getYear();
	}
	
	public function getMonth()
	{
		$month = (string)$this->getDate()->getMonth();
		if (strlen($month) === 1)
		{
			return "0".$month;
		}
		return $month;
	}
}

class agenda_CalendarWeek extends agenda_CalendarItem
{
	public function getDateAsFormattedString()
	{
		return date('W', $this->getDate()->getTimestamp());
	}

	public function getLinkParameters()
	{
		$res = parent::getLinkParameters();
		$res['agendaParam']['listday'] =  null;
		$res['agendaParam']['listweek'] = $this->getDate()->getTimestamp();
		$res['agendaParam']['listmonth'] =  null;
		return $res;
	}
	
	public function getYear()
	{
		return $this->getDate()->getYear();
	}
}

class agenda_CalendarMonth extends agenda_CalendarItem
{
	/**
	 * @param Array $events
	 * @param date_Calendar $date
	 */
	public function __construct($events, $date, $sibling = null)
	{
		$this->setSiblingId($sibling);
		$this->setActive();
		$date->setDay(1)->toMidday();
		$this->setDate($date);
		$card = $this->getEventCardinalitiesForMonth($events, clone $date);
		$arrayRep = array();
		$i = 0;
		$offset = -1;
		$month = $date->getMonth();
		$currentWeek = null;
		while ($date->getMonth() == $month)
		{
			$day = new agenda_CalendarDay();
			$day->setSiblingId($sibling);
			$dayOfWeek = ($date->getDayOfWeek()+6)%7;
			if ( $i%7==0 )
			{
				if (!is_null($currentWeek))
				{
					$arrayRep[] = $currentWeek;
				}
				$currentWeek = new agenda_CalendarWeek();
				$currentWeek->setDate($date);
				$currentWeek->setSiblingId($sibling);
			}
			if ($dayOfWeek == $i%7)
			{
				$day->setDate($date);
				if ($offset == -1)
				{
					$offset = $i;
				}
				if (isset($card[$i+1-$offset]))
				{
					$day->setActive();
					$currentWeek->setActive();
				}
				$date->add(date_Calendar::DAY, 1);
			}
			if (!is_null($currentWeek))
			{
				$currentWeek[] = $day;
			}
			$i++;
		}
		if (!is_null($currentWeek))
		{
			$arrayRep[] = $currentWeek;
		}
		parent::__construct($arrayRep);
	}

	/**
	 * @param unknown_type $events
	 * @param date_Calendar $date
	 * @return unknown
	 */
	protected function getEventCardinalitiesForMonth($events, $date)
	{
		$result = array();
		$start = 0;
		$maxday = $date->getDaysInMonth();
		$endMonth = $date->getMonth();
		foreach ($events as $event)
		{
			$eventStartDate = date_Converter::convertDateToLocal(date_Calendar::getInstance($event['date']));
			$eventEndDate = date_Converter::convertDateToLocal(date_Calendar::getInstance($event['enddate']));
			$min = $eventStartDate->getDay();
			$max = $eventEndDate->getDay();

			if ($eventStartDate->getMonth() > $endMonth)
			{
				continue;
			}
			else if ($eventStartDate->getMonth() < $endMonth)
			{
				$min = 1;
			}

			if ($eventStartDate->getMonth() < $endMonth)
			{
				continue;
			}
			else if ($eventEndDate->getMonth() > $endMonth)
			{
				$max = $maxday;
			}

			for($i = $min ; $i <= $max ; $i++)
			{
				$result[$i] = true;
			}

		}
		return $result;
	}

	public function getLinkParameters()
	{
		$res = parent::getLinkParameters();
		$res['agendaParam']['listday'] =  null;
		$res['agendaParam']['listweek'] = null;
		$res['agendaParam']['listmonth'] =  $this->getDate()->getTimestamp();
		return $res;
	}
}
