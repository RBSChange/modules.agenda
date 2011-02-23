<?php
class agenda_EventService extends f_persistentdocument_DocumentService
{
	/**
	 * @var agenda_EventService
	 */
	private static $instance;

	/**
	 * @return agenda_EventService
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}

	/**
	 * @return agenda_persistentdocument_event
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_agenda/event');
	}

	/**
	 * Create a query based on 'modules_agenda/event' model
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_agenda/event');
	}

	/**
	 * @param agenda_persistentdocument_event $document
	 * @param Integer $parentNodeId Parent node ID where to save the document (optionnal => can be null !).
	 * @return void
	 */
	protected function preSave($document, $parentNodeId = null)
	{
		// We fix the event date (aka start date) to Midnight the day it was created.
		$date = date_Calendar::getInstance($document->getDate());
        $endStringDate = $document->getEnddate();
		
		if (is_null($endStringDate))
		{
		    $enddate = $date->add(date_Calendar::DAY, 1)->sub(date_Calendar::SECOND, 1);
			$document->setEnddate($enddate);
		} 
		else 
		{
		    $enddate = date_Calendar::getInstance($document->getEnddate());
    		if ($enddate->isBefore($date))
    		{
    			throw new IllegalArgumentException('enddate must be after date');
    		}
		}
		
		$start = $document->getStartpublicationdate();
		$end = $document->getEndpublicationdate();

		// Set the end of homepage visibility.
		$endVisibility = date_Calendar::getInstance($start);
		$endVisibility->addTimeSpan($document->getHomepagevisibilityTimeSpan(), false);
		$endPublication = date_Calendar::getInstance($end);
		if ($endVisibility->isAfter($endPublication))
		{
			$document->setEndhomepagedate($endPublication);
		}
		$document->setEndhomepagedate($endVisibility);
		// Update visibility status
		$this->updateHomepageVisibility($document);
	}

	/**
	 * Update the field homepagevisibility according the the end homepage date.
	 *
	 * @param agenda_persistentdocument_event $document
	 */
	protected function updateHomepageVisibility($document)
	{
		$now = date_Calendar::now();
		if ($now->isAfter(date_Calendar::getInstance($document->getEndhomepagedate())))
		{
			$document->setHomepagevisibility(false);
		}
		else
		{
			$document->setHomepagevisibility(true);
		}
	}

	/**
	 *	Listener task
	 */
	public function onDayChange()
	{
		$query = $this->createQueryByParent(null, true);
		$document = new agenda_persistentdocument_event();
		foreach ($this->pp->find($query) as $document)
		{
			$this->updateHomepageVisibility($document);
			if ($document->isModified())
			{
			    try 
			    {
			        $modifiedPropertyNames = $document->getModifiedPropertyNames();
    			    try
    			    {
    			        $this->tm->beginTransaction();
    			        $this->pp->updateDocument($document);       
    			        $this->tm->commit();
    			    }
    			    catch  (Exception $e)
    			    {
    			        $this->tm->rollBack($e);
    			    }
    			    
    			    f_event_EventManager::dispatchEvent('persistentDocumentUpdated', $this,
    			        array("document" => $document, 
    			        	"modifiedPropertyNames" => $modifiedPropertyNames, 
    			        	"oldPropertyValues" => array()));
			    }
			    catch (Exception $e)
			    {
			        Framework::exception($e);		    
			    }
			}
		}
	}

	/**
	 * Get the basic query on event's by "Parent"...
	 *
	 * @param Integer $docId
	 * @param Boolean $includeChildren
	 * @return f_persistentdocument_criteria_Query
	 */
	protected function createQueryByParent($docId, $includeChildren)
	{
		$query = $this->createQuery();
		if (!is_null($docId))
		{
			if($includeChildren == false)
			{
				$query->add(Restrictions::childOf($docId));
			}
			else
			{
				$query->add(Restrictions::descendentOf($docId));
			}
		}
		$query->add(Restrictions::published());
		return $query;
	}

	/**
	 * Get the list of event with parent $docId, displayed in $displayType mode. If $includeChildren is
	 * set to true, all the events that are descendents of $docId will be potential results (otherwise only direct children).
	 *
	 * @param Integer $docId
	 * @param String $displayType
	 * @param Boolean $includeChildren
	 * @param Boolean $timeOrdered
	 * @param Integer $limit
	 * @return Array<agenda_persistentdocument_event>
	 */
	public function getEventListByParentId($docId, $displayType = 'classic', $includeChildren = true, $timeOrdered = false, $limit = -1, $restrictions = null)
	{	    
		$query = $this->createQueryByParent($docId, $includeChildren);
		if ($displayType == 'homepage')
		{
			$query->add(Restrictions::eq('homepagevisibility', true));			
		}

		if ($timeOrdered)
		{
			$query->addOrder(Order::asc('date'));
		}
		else
		{
			$query->addOrder(Order::desc('date'));
		}
		$query->addOrder(Order::desc('priority'));
		
		if ($limit > 0)
		{
			$query->setMaxResults($limit);
		}
		
		if (!is_null($restrictions))
		{
			$query->add($restrictions);
		}
		return $this->pp->find($query);
	}

	/**
	 * Returns
	 *
	 * @param Integer $parentId
	 * @param date_Calendar $date
	 * @param Boolean $includeChildren
	 * @param Boolean $isUiDate
	 * @return array(array( 'date' => String, 'enddate' => String ));
	 */
	public function getEventListForMonth($parentId, $date, $includeChildren, $isUiDate = false)
	{
		$query = $this->createQueryByParent($parentId, $includeChildren)
			->add($this->getDateAndTimeSpanRestriction($date, new date_TimeSpan(0, 1, 0, 0, 0, 0), $isUiDate))
			->setProjection(Projections::property('date'), Projections::property('enddate'), Projections::property('id'));
		return $this->pp->find($query);
	}

	/**
	 * Returns the next event, attached to topic id$parentTopicId
	 *
	 * @return agenda_persistentdocument_event
	 */
	public function getNextEvent($parentTopicId)
	{
		$query = $this->createQueryByParent($parentTopicId, true);
		$query->setMaxResults(1);
		$query->add(Restrictions::ge('date', date_Calendar::now(false)->toString()));
		$query->addOrder(Order::asc('date'));
		$query->addOrder(Order::desc('priority'));
		$list = $this->pp->find($query);
		if (!isset($list[0]))
		{
			return null;
		}
		return $list[0];
	}

	/**
	 * Returns a restriction on all events taking place between $date and $date + $span.
	 *
	 * @param date_Calendar $date
	 * @param date_TimeSpan $span
	 * @param Boolean $isUiDate
	 * @return f_persistentdocument_criteria_Query
	 */
	public function getDateAndTimeSpanRestriction($date, $span, $isUiDate = false)
	{
		$startDate = $date->toString();
		if (!$isUiDate)
		{
			$startDate = date_Converter::convertDateToLocal($startDate);
		}
		
		if (is_null($span))
		{
			return Restrictions::ge('enddate', $startDate);
		}
		$endDate = $date->addTimeSpan($span)->sub(date_Calendar::SECOND, 1)->toString();
		return Restrictions::andExp(Restrictions::ge('enddate', date_Converter::convertDateToGMT($startDate)), Restrictions::le('date', date_Converter::convertDateToGMT($endDate)));
	}
	
	/**
	 * @param date_Calendar $date
	 * @param Integer $topicId
	 * @param Boolean $includeChildren
	 * @return Boolean
	 */
	public function hasEventForMonth($date, $topicId, $includeChildren)
	{
		$query = $this->createQueryByParent($topicId, $includeChildren);
		$query->add(Restrictions::gt('date', $date->toString()));
		$query->setProjection(Projections::rowCount('count'));
		$result = $query->find();
		return ($result[0]['count'] > 0);
	}
	
	
	/**
	 * this method is call before save the duplicate document.
	 *
	 * @param agenda_persistentdocument_event $newDocument
	 * @param agenda_persistentdocument_event $originalDocument
	 * @param Integer $parentNodeId
	 */
	protected function preDuplicate($newDocument, $originalDocument, $parentNodeId)
	{
	}
	
	/**
	 * @param agenda_persistentdocument_event $document
	 * @param string $moduleName
	 * @param string $treeType
	 * @param array<string, string> $nodeAttributes
	 */
	public function addTreeAttributes($document, $moduleName, $treeType, &$nodeAttributes)
	{
		$nodeAttributes['date'] = date_DateFormat::format($document->getUIDate(), 'D d M Y H:i', RequestContext::getInstance()->getUILang());
	}
}