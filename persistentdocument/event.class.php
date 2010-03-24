<?php
class agenda_persistentdocument_event extends agenda_persistentdocument_eventbase implements indexer_IndexableDocument
{
	/**
	 * Get the indexable document
	 *
	 * @return indexer_IndexedDocument
	 */
	public function getIndexedDocument()
	{
		$indexedDoc = new indexer_IndexedDocument();
		$indexedDoc->setId($this->getId());
		$indexedDoc->setDocumentModel('modules_agenda/event');
		$indexedDoc->setLabel($this->getLabel());
		$indexedDoc->setLang(RequestContext::getInstance()->getLang());
		$indexedDoc->setText($this->getFullTextForIndexation());
		return $indexedDoc;
	}
	
	/**
	 * @return String
	 */
	private function getFullTextForIndexation()
	{
		$fullText = "";
		$attachement = $this->getAttachment();
		if ($attachement !== null)
		{
			$idxDoc = $attachement->getIndexedDocument();
			if ($idxDoc !== null)
			{
				$fullText = $idxDoc->getLabel() . " : " . $idxDoc->getText();
			}
		}
		
		$accessMap = $this->getAccessmap();
		if ($accessMap !== null)
		{
			$idxDoc = $accessMap->getIndexedDocument();
			if ($idxDoc !== null)
			{
				$fullText .= " " . $idxDoc->getLabel() . " : " . $idxDoc->getText();
			}
		}
		$fullText .= " " . $this->getSummary();
		$fullText .= " " . $this->getText();
		$fullText .= " " . $this->getDatetimeinfo();
		$fullText .= " " . $this->getPlace();
		$fullText .= " " . $this->getContact();
		return f_util_StringUtils::htmlToText($fullText, false);
	}
	

	/**
	 * @return date_TimeSpan
	 */
	public function getHomepagevisibilityTimeSpan()
	{
		$instSpan = $this->getHomepagespan();
		if (is_null($instSpan))
		{
			return new date_TimeSpan();
		}
		return new date_TimeSpan($instSpan >> 8, $instSpan >> 4, $instSpan * 7, 0, 0, 0);
	}
	
	/**
	 * @return boolean
	 */
	public function hasDatetimeinfo()
	{
		return !f_util_StringUtils::isEmpty($this->getDatetimeinfo());
	}
	
	/**
	 * @return boolean
	 */
	public function hasPlace()
	{
		return !f_util_StringUtils::isEmpty($this->getPlace());
	}
	
	/**
	 * @return boolean
	 */
	public function hasAccessmap()
	{		
		return $this->getAccessmap() !== null;
	}
	
	/**
	 * @return boolean
	 * @deprecated 
	 */
	public function hasAttachement()
	{
		return $this->hasAttachment();
	}
	
	/**
	 * @return boolean
	 */
	public function hasAttachment()
	{
		return $this->getAttachment() !== null;
	}
	
	/**
	 * @return boolean
	 */
	public function hasContact()
	{
		return !f_util_StringUtils::isEmpty($this->getContact());
	}
	
	/**
	 * @return boolean
	 */
	public function hasSubscriptionpage()
	{
		return $this->getSubscriptionpage() !== null;
	}
	
	/**
	 * @return Boolean
	 */
	public function hasListvisual()
	{
		return $this->getListvisual() !== null;
	}
	
	/**
	 * @return Boolean
	 */
	public function hasDetailvisual()
	{
		return $this->getDetailvisual() !== null;
	}
	
	/**
	 * @return Boolean
	 */
	public function hasText()
	{
		return !f_util_StringUtils::isEmpty($this->getText());
	}
	
	/**
	 * @return Boolean
	 */
	public function hasSummary()
	{
		return !f_util_StringUtils::isEmpty($this->getSummary());
	}
	
	public function hasLinkedpage()
	{
		return $this->getLinkedpage() !== null;
	}
	
	/**
	 * @return Boolean
	 */
	public function hasCategory()
	{
		return $this->getCategory() !== null;
	}
	
	public function getHCalendarEnddate()
	{
		$date = date_Calendar::getInstance($this->getEnddate());
		return date_DateFormat::format($date->add(date_Calendar::SECOND, intval(date('Z'))), 'Y-m-dTH:i:sZ');
	}
	
	public function getHCalendarDate()
	{
		$date = date_Calendar::getInstance($this->getDate());
		return date_DateFormat::format($date->add(date_Calendar::SECOND, intval(date('Z'))), 'Y-m-dTH:i:sZ');
	}
	
	/**
	 * For an event, get the Metatitle using the subsitution rule defined
	 * in the preferences.
	 *
	 * @return String
	 */
	public function getDetailmetatitle()
	{
		$prefs = ModuleService::getInstance()->getPreferencesDocument('agenda');
		if (!is_null($prefs))
		{
			return $this->replaceMeta($prefs->getDetailtitle());
		}
		return null;
	}
	
	/**
	 * For an event, get the Description Meta using the subsitution rule defined
	 * in the preferences.
	 *
	 * @return String
	 */
	public function getDetaildescription()
	{
		$prefs = ModuleService::getInstance()->getPreferencesDocument('agenda');
		if (!is_null($prefs))
		{
			return $this->replaceMeta($prefs->getDetaildescription());
		}
		return null;
	}
	
	/**
	 * For an event, get the Keywords Meta using the subsitution rule defined
	 * in the preferences.
	 *
	 * @return String
	 */
	public function getDetailkeywords()
	{
		$prefs = ModuleService::getInstance()->getPreferencesDocument('agenda');
		if (!is_null($prefs))
		{
			return $this->replaceMeta($prefs->getDetailkeywords());
		}
		return null;
	}
	
	/**
	 * @param String $target
	 * @return String
	 */
	private function replaceMeta($target)
	{
		$format = f_Locale::translate('&framework.date.date.smart-full-short;');
		
		$title = $this->getLabel();
		$summary = $this->getSummary();
		$date = date_Calendar::getInstance($this->getDate());
		$string = str_replace(array('TITLE', 'TITRE'), $title, $target);
		$string = str_replace(array('RESUME', 'SUMMARY'), $summary, $string);
		$string = str_replace('DATE', date_DateFormat::format($date, $format), $string);		
		return $string;
	}
	
	public function lastsOneDay()
	{
		$begin = date_Calendar::getInstance($this->getUIDate());
		$end = date_Calendar::getInstance($this->getUIEnddate())->toMidnight();
		return $end->equals($begin);
	}
	
	/**
	 * @param string $moduleName
	 * @param string $treeType
	 * @param array<string, string> $nodeAttributes
	 */
	protected function addTreeAttributes($moduleName, $treeType, &$nodeAttributes)
	{
		$nodeAttributes['date'] = date_DateFormat::format($this->getUIDate(), 'D d M Y H:i', RequestContext::getInstance()->getUILang());
	}
	
	/**
	 * @return Integer
	 */
	public function getDateYear()
	{
		return date_Calendar::getInstance($this->getDate())->getYear();
	}
	
	/**
	 * @return String
	 */
	public function getDateMonth()
	{
		return sprintf("%02d", date_Calendar::getInstance($this->getDate())->getMonth());
	}
	
	/**
	 * @return String
	 */
	public function getDateDay()
	{
		return sprintf("%02d", date_Calendar::getInstance($this->getDate())->getDay());
	}

}