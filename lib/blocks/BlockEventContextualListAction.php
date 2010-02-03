<?php
class agenda_BlockEventContextualListAction extends agenda_BlockEventlistAction
{
	protected function getParentId($context)
	{
		$ancestors = $context->getAncestors();
		if (f_util_ArrayUtils::isEmpty($ancestors))
		{
			return null;
		}
		return f_util_ArrayUtils::lastElement($ancestors);
	}
}

class agenda_BlockEventContextualListSuccessView extends agenda_BlockEventlistSuccessView 
{

}
?>