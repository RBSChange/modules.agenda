<?php

class agenda_BlockEventDummyView extends block_BlockView
{
	/**
     * @param block_BlockContext $context
     * @param block_BlockRequest $request
     * @return String view name
     */
	public function execute($context, $request)
	{
		$this->setTemplateName('Agenda-Block-Event-Dummy');
		$this->setAttributes($this->getParameters());
	}
}