<?php

class agenda_BlockEventlistSuccessView extends block_BlockView
{
	/**
     * @param block_BlockContext $context
     * @param block_BlockRequest $request
     */
	public function execute($context, $request)
	{
		$subBlockContent = array(); 
		foreach ($this->getParameter('calendarParams') as $subCal)
		{
			$subBlock = $this->getNewBlockInstance()
			->setPackageName("modules_agenda")
			->setType("calendar")
			->setParameters($this->getParameters());
			$subBlock->setParameter('month', $subCal['month']);
			$subBlock->setParameter('type', $subCal['type']);
			$subBlockContent[] = $this->forward($subBlock);
		}
		$this->setParameter('calendars', $subBlockContent);
		$this->setTemplateName('Agenda-Block-List');
		$this->setAttributes($this->getParameters());
	}
}