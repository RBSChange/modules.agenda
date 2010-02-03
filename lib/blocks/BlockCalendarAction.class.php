<?php

class agenda_BlockHelper
{
	/**
	 * @param website_persistentdocument_page $context
	 * @return Interger
	 */
	public static function getParentTopicId($page)
	{
		$parent = website_PageService::getInstance()->getParentOf($page);
		if ($parent instanceof website_persistentdocument_topic)
		{
			return $parent->getId();
		}
		return null;
	}
}

class agenda_BlockCalendarAction extends block_BlockAction
{
    /**
	 * Global request attribute key used to transmit
	 * a custom even list to the block
	 * <code>
	 * $myEventList = $this->getEventsBySomeMethod();
	 * $globalRequest->setAttribute(agenda_BlockCalendarAction::EVENT_LIST_ATTRIBUTE, $myEventList);
	 * $calendarBlock = $this->getNewBlockInstance()
     *   ->setPackageName("modules_agenda")
     *   ->setType("calendar");
     * $calendarBlockResult = $this->forward($calendarBlock);
     * $this->setParameter('calendarBlockResult', $calendarBlockResult);
     * </code>
	 */
    const EVENT_LIST_ATTRIBUTE = "agenda.calendareventlist.key";
    
	/**
     * @param block_BlockContext $context
     * @param block_BlockRequest $request
     * @return void
     */
	public function initialize($context, $request)
	{
	    
		$minBeginDate = date_Converter::convertDateToLocal(date_Calendar::now())->setDay(1)->toMidnight();
		$beginDate = clone $minBeginDate;
		if ($request->hasParameter('month'))
		{
			$beginDate->setMonth((int)$request->getParameter('month'));
		}
		if ($request->hasParameter('year'))
		{
			$beginDate->setYear((int)$request->getParameter('year'));
		}
		if ($beginDate->isBefore($minBeginDate))
		{
			$beginDate = $minBeginDate;
		}
		$this->setParameter('beginDate', $beginDate);
	}

	/**
     * @param block_BlockContext $context
     * @param block_BlockRequest $request
     * @return String view name
     */
	public function execute($context, $request)
	{
		$beginDate = $this->getParameter('beginDate');		
		$type = $this->getParameter('type');
		if ($this->hasParameter('parentopic'))
		{
			$parentTopicId = $this->getParameter('parentopic');
		}
		else 
		{
			$sibling = DocumentHelper::getDocumentInstance($context->getId());
			$parentTopicId = agenda_BlockHelper::getParentTopicId($sibling);
		}

		$includeChildren = $this->getParameter('includechildren', false);
		switch ($type)
		{
			case 'leftpanel':
				$this->setupLeftpanelDisplay($beginDate);
				break;
			case 'rightpanel':
				$beginDate->add(date_Calendar::MONTH, 1);
				$this->setupRightpanelDisplay($beginDate);
				break;
			case 'standard':
				$this->setupLeftpanelDisplay($beginDate);
				$this->setupRightpanelDisplay($beginDate, $parentTopicId, $includeChildren);
				break;
		}
		$globalRequest = Controller::getInstance()->getContext()->getRequest();
		if (!$globalRequest->hasAttribute(self::EVENT_LIST_ATTRIBUTE))
		{
		    $eventList = agenda_EventService::getInstance()->getEventListForMonth($parentTopicId, $beginDate, $includeChildren, true);    
		}
		else
		{
		    $eventList = $globalRequest->getAttribute(self::EVENT_LIST_ATTRIBUTE);
		}
		if (f_util_ArrayUtils::isNotEmpty($eventList))
		{
			$eventProps = f_util_ArrayUtils::firstElement($eventList);
			$siblingId = $eventProps['id'];	
		}
		else 
		{
			$siblingId = null;
		}
		$this->setParameter('calendarTitleDate', date_DateFormat::format($beginDate, 'F Y'));
		$this->setParameter('calendarMonth', new agenda_CalendarMonth($eventList, $beginDate, $siblingId));
		return block_BlockView::SUCCESS;
	}

	private function setupLeftpanelDisplay($beginDate)
	{
		if ( $beginDate->getMonth() != date_Calendar::now()->getMonth())
		{
			$prevMonth = clone $beginDate;
			$prevMonth->sub(date_Calendar::MONTH, 1);
			$this->setParameter('prevMonthUrl', $this->getUrlForDate($prevMonth));
		}
	}

	private function setupRightpanelDisplay($beginDate, $parentTopicId, $includeChildren)
	{
		$nextMonth = clone $beginDate;
		if ($this->getParameter('type') == 'standard')
		{
			$nextMonth->add(date_Calendar::MONTH, 1);
		}
		if (agenda_EventService::getInstance()->hasEventForMonth($nextMonth, $parentTopicId, $includeChildren))
		{
			$this->setParameter('nextMonthUrl', $this->getUrlForDate($nextMonth));
		}
	}
	
	private function getUrlForDate($date)
	{
		$params = array('month' => $date->getMonth(), 'year' => $date->getYear(), 'listmonth' => $date->getTimestamp());
		return LinkHelper::getCurrentUrl(array('agendaParam' => $params));
	}
}
