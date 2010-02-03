<?php

class agenda_BlockNexteventErrorView extends block_BlockView
{
	/**
     * @param block_BlockContext $context
     * @param block_BlockRequest $request
     */
	public function execute($context, $request)
	{
		$this->setTemplateName('Agenda-Block-Nextevent-Error');
		$this->setAttributes($this->getParameters());
	}
}