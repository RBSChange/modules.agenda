<?php
class agenda_BlockEventlistAction extends block_BlockAction
{
	const DEFAULT_ITEM_PER_PAGE = 5;
	
	/**
	 * Global request attribute key used to transmit
	 * a custom even list to the block
	 * @see agenda_BlockCalendarAction for a complete example
	 */
	const EVENT_LIST_ATTRIBUTE = "agenda.eventlist.key";
	
	/**
	 * @param website_Page $context
	 * @param block_BlockRequest $request
	 * @return void
	 */
	public function initialize($context, $request)
	{
		// Load prefs here...
		if ($request->hasParameter('listmonth'))
		{
			$start = date_Calendar::getInstance(date('Y-m-d 00:00:00', $request->getParameter('listmonth')));
			$this->setParameter('restrictionStart', $start);
			$this->setParameter('restrictionSpan', new date_TimeSpan(0, 1, 0, 0, 0, 0));
		} 
		else if ($request->hasParameter('listweek'))
		{
			$start = date_Calendar::getInstance(date('Y-m-d 00:00:00', $request->getParameter('listweek')));
			$this->setParameter('restrictionStart', $start);
			$this->setParameter('restrictionSpan', new date_TimeSpan(0, 0, 7, 0, 0, 0));
		} 
		else if ($request->hasParameter('listday'))
		{
			$start = date_Calendar::getInstance(date('Y-m-d 00:00:00', $request->getParameter('listday')));
			$this->setParameter('restrictionStart', $start);
			$this->setParameter('restrictionSpan', new date_TimeSpan(0, 0, 1, 0, 0, 0));
		}

		if (isset($start))
		{
			$toDisplay = clone $start;
		}
		else
		{
			$toDisplay = date_Calendar::now();
		}
		$this->setParameter('month', $toDisplay->getMonth());
		$this->setParameter('nextMonth', $toDisplay->add(date_Calendar::MONTH, 1)->getMonth());
	}

	/**
	 * @param website_Page $context
	 * @param block_BlockRequest $request
	 * @return String view name
	 */
	public function execute($context, $request)
	{		
		if (!$this->setDefaults())
		{
			if ($context->inBackofficeMode())
			{
				$this->setParameter('errorMsg', f_Locale::translate('&modules.agenda.frontoffice.Error-no-preferences;'));
			}
			return block_BlockView::ERROR;
		}
		$restrictions = null;
		$as = agenda_EventService::getInstance();
		$parentId = $this->getParentId($context);

		$displayType = $this->getParameter('type', 'classic');
		$includeChildren = ($this->getParameter('includechildren') == "true");

		$itemCount = $this->getParameter('itemcount');
		$chrono = ($this->getParameter('timeordered') == "true");

		if (f_util_Convert::fixDataType($this->getParameter('currentmonthevent')) === true)
		{
			$restrictions = $as->getDateAndTimeSpanRestriction(date_Calendar::now()->setDay(1)->toMidnight(), new date_TimeSpan(0, 1, 0, 0, 0, 0), true);
		}
		
		if ($this->hasParameter('restrictionStart'))
		{
			$restrictions = $as->getDateAndTimeSpanRestriction($this->getParameter('restrictionStart'), $this->getParameter('restrictionSpan'), true);
		}
		if ($displayType == 'homepage')
		{
			$this->setParameter('isHomepage', true);
		}

	    $globalRequest = $context->getGlobalRequest();
		if (!$globalRequest->hasAttribute(self::EVENT_LIST_ATTRIBUTE))
		{
		    $list = $as->getEventListByParentId($parentId, $displayType, $includeChildren, $chrono, $itemCount, $restrictions);
		    // For front edition
		    $container = DocumentHelper::getDocumentInstance($parentId);
		    if ($container instanceof website_persistentdocument_topic)
		    {
		    	$this->setParameter('container', $container);
		    }
		}
		else
		{
		    $list = $globalRequest->getAttribute(self::EVENT_LIST_ATTRIBUTE);
		}
		
		$currentPage = $request->getParameter('page', 1);
		$itemPerPage = $this->getParameter('item', self::DEFAULT_ITEM_PER_PAGE);
		$this->setParameter('paginator', new paginator_Paginator('agenda', $currentPage, $list, $itemPerPage));
		$this->setParameter('calendarParams', $this->setupCalendars());
		$this->setParameter('parentref', $parentId);
		$this->setParameter('hasEvents', f_util_ArrayUtils::isNotEmpty($list));
		$this->setParameter('visual', $this->getParameter('visual') == "true");
		$this->setParameter('shortdates', f_util_Convert::fixDataType($this->getParameter('shortdates', false)));
		$this->setParameter('extendedDateFormat', f_Locale::translate('&framework.date.date.smart-full;'));
		return block_BlockView::SUCCESS;
	}
	
	/**
	 * @return Boolean
	 */
	public function setDefaults()
	{
		$preferenceDocument = ModuleService::getInstance()->getPreferencesDocument('agenda');
		if (is_null($preferenceDocument))
		{
			return false;
		}
		$itemPerPage = $preferenceDocument->getItemperpage();
		if (is_null($itemPerPage))
		{
			$itemPerPage = self::DEFAULT_ITEM_PER_PAGE;
		}
		$this->setParameter('item', $itemPerPage);
		$alternator = new agenda_VisualAlternator($preferenceDocument->getListvisualposition());
		$this->setParameter('alternator', $alternator);
		return true;
	}

	/**
	 * @param website_Page $context
	 * @return Integer
	 */
	protected function getParentId($context)
	{
	  $ancestors = $context->getAncestors();
	  if (f_util_ArrayUtils::isEmpty($ancestors))
		{
			return null;
		}
	  return f_util_ArrayUtils::firstElement($ancestors);
	}

	/**
	 * @return Array<String, Mixed> 
	 */
	private function setupCalendars()
	{
		$count = (int)$this->getParameter('calendarcount', 0);
		$params = array();
		if ($count == 1)
		{
			$params[] = array('month' => $this->getParameter('month'), 'type' => 'standard', 'includechildren' => $this->getParameter('includechildren', $this->getParameter('includechildren', false)));
		} else if ($count == 2)
		{
			$params[] = array('month' => $this->getParameter('month'), 'type' => 'leftpanel');
			$params[] = array('month' => $this->getParameter('nextMonth'), 'type' => 'rightpanel', 'includechildren' => $this->getParameter('includechildren', $this->getParameter('includechildren', false)));
		}
		return $params;
	}
}