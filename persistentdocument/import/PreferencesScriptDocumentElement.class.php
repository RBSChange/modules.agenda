<?php
class agenda_PreferencesScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return agenda_persistentdocument_preferences
     */
    protected function initPersistentDocument()
    {
    	$document = ModuleService::getInstance()->getPreferencesDocument('agenda');
    	return ($document !== null) ? $document : agenda_PreferencesService::getInstance()->getNewDocumentInstance();
    }
}