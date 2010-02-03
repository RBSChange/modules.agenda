<?php

class agenda_BlockNexteventSuccessView extends block_BlockView
{
	/**
     * @param block_BlockContext $context
     * @param block_BlockRequest $request
     */
	public function execute($context, $request)
	{
		$this->setTemplateName('Agenda-Block-Nextevent-Success');
		$this->setAttributes($this->getParameters());
	}
}