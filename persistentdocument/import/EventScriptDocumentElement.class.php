<?php
class agenda_EventScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return agenda_persistentdocument_event
     */
    protected function initPersistentDocument()
    {
        return agenda_EventService::getInstance()->getNewDocumentInstance();
    }
    
	public function endProcess()
    {
        $document = $this->getPersistentDocument();
        if ($document->getPublicationstatus() == 'DRAFT')
        {
            $document->activate();
        }
    }
    
    /**
     * @return array
     */
    protected function getDocumentProperties()
    {
        $properties = parent::getDocumentProperties();
        if (isset($properties['listvisualid']))
        {
            $media = $this->script->getElementById($properties['listvisualid']);
            if ($media !== null)
            {
                $properties['listvisual'] = $media->getPersistentDocument();
            }
            unset($properties['listvisualid']);
        }
        
        if (isset($properties['detailvisualid']))
        {
            $media = $this->script->getElementById($properties['detailvisualid']);
            if ($media !== null)
            {
                $properties['detailvisual'] = $media->getPersistentDocument();
            }
            unset($properties['detailvisualid']);
        }
        
        if (isset($properties['accessmapid']))
        {
            $media = $this->script->getElementById($properties['accessmapid']);
            if ($media !== null)
            {
                $properties['accessmap'] = $media->getPersistentDocument();
            }
            unset($properties['accessmapid']);
        }
        
        if (isset($properties['attachmentid']))
        {
            $media = $this->script->getElementById($properties['attachmentid']);
            if ($media !== null)
            {
                $properties['attachment'] = $media->getPersistentDocument();
            }
            unset($properties['attachmentid']);
        }
        
        return $properties;
    }
}