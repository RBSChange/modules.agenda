<?php
class agenda_AgendaListener
{
	public function onDayChange($sender, $params)
	{
		if (Framework::isDebugEnabled())
		{
			Framework::debug(__METHOD__);
		}
		agenda_EventService::getInstance()->onDayChange();
	}
}
