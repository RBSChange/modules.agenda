<?php
/**
 * agenda_persistentdocument_preferences
 * @package agenda
 */
class agenda_persistentdocument_preferences extends agenda_persistentdocument_preferencesbase 
{
	/**
	 * @see f_persistentdocument_PersistentDocumentImpl::getLabel()
	 *
	 * @return String
	 */
	public function getLabel()
	{
		return f_Locale::translateUI(parent::getLabel());
	}

}