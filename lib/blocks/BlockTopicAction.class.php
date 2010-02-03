<?php

class agenda_BlockTopicAction extends agenda_BlockEventlistAction
{
	/**
	 * @param block_BlockContext $context
	 * @param block_BlockRequest $request
	 * @return String view name
	 */
	public function execute($context, $request)
	{
	    $view  = parent::execute($context, $request);
	    if ($view == block_BlockView::ERROR)
	    {
	        return array('agenda', 'eventlist', block_BlockView::ERROR);
	    }
	    else
	    {
	        return array('agenda', 'eventlist', block_BlockView::SUCCESS);
	    }
	}
	
	/**
	 * @param block_BlockContext $context
	 * @return Integer
	 */
	protected function getParentId($context)
	{
		return $this->getDocumentIdParameter();
	}
}