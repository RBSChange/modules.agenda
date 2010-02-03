<?php
/**
 * @package modules.agenda.lib
 */
class agenda_ActionBase extends f_action_BaseAction
{

	/**
	 * Returns the agenda_EventService to handle documents of type "modules_agenda/event".
	 *
	 * @return agenda_EventService
	 */
	public function getEventService()
	{
		return agenda_EventService::getInstance();
	}

	/**
	 * Returns the agenda_PreferencesService to handle documents of type "modules_agenda/preferences".
	 *
	 * @return agenda_PreferencesService
	 */
	public function getPreferencesService()
	{
		return agenda_PreferencesService::getInstance();
	}
}