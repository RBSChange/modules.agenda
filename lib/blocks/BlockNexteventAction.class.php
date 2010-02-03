<?php
class agenda_BlockNexteventAction extends block_BlockAction
{
	/**
     * @param block_BlockContext $context
     * @param block_BlockRequest $request
     * @return String view name
     */
	public function execute($context, $request)
	{
		$sibling = DocumentHelper::getDocumentInstance($context->getId());
		$parentTopicId = agenda_BlockHelper::getParentTopicId($sibling);
		$nextEvent = agenda_EventService::getInstance()->getNextEvent($parentTopicId);
		if (is_null($nextEvent))
		{
			$this->handleNoNextEvent();
			return block_BlockView::ERROR;
		}
		$this->handleHasNextEvent();
		$this->setParameter('event', $nextEvent);
		$this->setParameter('shortdates', f_util_Convert::fixDataType($this->getParameter('shortdates', false)));
		$this->setParameter('extendedDateFormat', f_Locale::translate('&framework.date.date.smart-full;'));
		return block_BlockView::SUCCESS;
	}

	protected function handleNoNextEvent()
	{
		$this->setParameter('errorMsg', f_Locale::translate('&modules.agenda.frontoffice.No-next-event;'));
	}
	
	protected function handleHasNextEvent()
	{
	}
}