<?php
/**
 * @date Mon, 30 Jul 2007 09:05:15 +0200
 * @author intstaufl
 */
class agenda_PreferencesService extends f_persistentdocument_DocumentService
{
	/**
	 * @var agenda_PreferencesService
	 */
	private static $instance;

	/**
	 * @return agenda_PreferencesService
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}

	/**
	 * @return agenda_persistentdocument_preferences
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_agenda/preferences');
	}

	/**
	 * Create a query based on 'modules_agenda/preferences' model
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_agenda/preferences');
	}

	/**
	 * @param agenda_persistentdocument_preferences $document
	 * @param Integer $parentNodeId Parent node ID where to save the document (optionnal => can be null !).
	 * @return void
	 */
	protected function preSave($document, $parentNodeId = null)
	{
		$document->setLabel('&modules.agenda.bo.general.Module-name;');
	}
}