<?php


class agenda_BlockCalendarSuccessView extends block_BlockView
{
	/**
     * @param block_BlockContext $context
     * @param block_BlockRequest $request
     * @return String view name
     */
	public function execute($context, $request)
	{
		$this->setTemplateName('Agenda-Block-Calendar');
		$this->setAttributes($this->getParameters());
	}
}