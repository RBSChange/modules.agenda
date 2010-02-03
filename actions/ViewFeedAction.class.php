<?php

class agenda_ViewFeedAction extends agenda_Action
{
	private $writer = null;

	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$as = $this->getEventService();
		$list = $as->getEventListByParentId(website_WebsiteModuleService::getInstance()->getCurrentWebsite()->getId());
		$this->writer = new XMLWriter();
		$this->writer->openMemory();
		$this->writer->startDocument('1.0','UTF-8');
		$this->writer->startElement('rss');
		$this->writer->writeAttribute('version', '2.0');
		$this->writer->startElement('channel');
		$this->setDefaults();
		foreach ($list as $event)
		{
			$this->writer->startElement('item');
			$this->add('title', $event->getLabel());
			$this->add('description', $event->getSummary());
			$this->add('guid', str_replace('&amp;', '&', LinkHelper::getUrl($event)));
			$date = date_Calendar::getInstance($event->getDate());
			$this->add('pubDate', date_DateFormat::format($date,'d M Y H:i:s', 'en') . ' GMT');
			$this->writer->endElement();
		}
		$this->writer->endElement();
		$this->writer->endElement();
		echo $this->writer->outputMemory(true);
	}
	/**
	 * @param String $name
	 * @param String $value
	 * @return XMLWriter
	 */
	private function add($name, $value)
	{
		$this->writer->startElement($name);
		$this->writer->text($value);
		$this->writer->endElement();
		return $this->writer;
	}

	private function setDefaults()
	{
		$prefs = ModuleService::getInstance()->getPreferencesDocument('agenda');
		$website = website_WebsiteModuleService::getInstance()->getCurrentWebsite();
		$feedURL = $prefs->getRssfeedurl();
		if (empty($feedURL))
		{
			$feedURL = $website->getUrl();
		}
		$description = $prefs->getRssfeeddescription();
		if (is_null($description))
		{
			$description = f_Locale::translate('&modules.agenda.frontoffice.Default-feed-description;', array('label' => $website->getLabel()));
		}

		$title = $prefs->getRssfeedtitle();
		if (is_null($title))
		{
			$title = $website->getLabel();
		}
		$this->add('link', $feedURL);
		$this->add('description', $description);
		$this->add('title', $title);
		$this->add('language', RequestContext::getInstance()->getLang());
	}

	public function isSecure()
	{
		return false;
	}
	/**
	 * @return Boolean
	 */
	protected function suffixSecureActionByDocument()
	{
		return false;
	}
}