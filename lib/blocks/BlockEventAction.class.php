<?php
class agenda_BlockEventAction extends block_BlockAction
{
	/**
     * @param block_BlockContext $context
     * @param block_BlockRequest $request
     * @return String view name
     */
	public function execute($context, $request)
	{
		$doc = $this->getDocumentParameter();
		if (is_null($doc) && $request->hasParameter(K::COMPONENT_ID_ACCESSOR))
		{
			$doc = DocumentHelper::getDocumentInstance($request->getParameter(K::COMPONENT_ID_ACCESSOR));
		}
		if (!is_null($doc))
		{
			$this->replaceMetas($context, $doc);
		}
		else
		{
			if (!$context->inIndexingMode())
			{
				return block_BlockView::NONE;
			}
			return block_BlockView::DUMMY;
		}
		$this->setParameter('shortdates', f_util_Convert::fixDataType($this->getParameter('shortdates', true)));
		$this->setParameter('extendedDateFormat', f_Locale::translate('&framework.date.date.smart-full;'));
		$this->setParameter("event", $doc);
		return block_BlockView::SUCCESS;
	}

	/**
	 * @param block_BlockContext $context
	 * @param agenda_persistentdocument_event $doc
	 */
	private function replaceMetas($context, $doc)
	{
		$context->setMetatitle($doc->getDetailmetatitle());
		$context->setNavigationtitle($doc->getDetailmetatitle());
		$context->appendToDescription($doc->getDetaildescription());
		$context->addKeyword($doc->getDetailkeywords());
	}
}