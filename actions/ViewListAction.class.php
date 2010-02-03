<?php

class agenda_ViewListAction extends agenda_Action
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$page = null;
		$module = 'generic';
		$action = 'ViewList';
		$documentId = $request->getModuleParameter('agenda', K::COMPONENT_ID_ACCESSOR);
		$document = null;
		if ($documentId === null)
		{
			$documentId = $request->getParameter(K::PARENT_ID_ACCESSOR);
		}
		if ($documentId !== null)
		{
			$document = DocumentHelper::getDocumentInstance($documentId);
		}
		if (!is_null($document))
		{
			$tag = 'functional_agenda_event-list';
			try
			{
				$page = TagService::getInstance()->getDocumentBySiblingTag($tag, $document);
			}
			catch (TagException $e)
			{	
				try
				{
					$page = $this->getDocumentService()->getDocumentByContextualTag(
					'contextual_website_website_modules_agenda_page-list',
					website_WebsiteModuleService::getInstance()->getCurrentWebsite()
					);
				}
				catch (TagException $e)
				{
					//No taged Page found
					Framework::exception($e);
				}
			}			
			
			if ( ! is_null($page) )
			{
				$request->setParameter(K::PAGE_REF_ACCESSOR, $page->getId());
				$module = 'website';
				$action = 'Display';
			}
			else 
			{
				$module = 'website';
				$action = 'Error404';
			}
		}		
		$context->getController()->forward($module, $action);
		return View::NONE;
	}

	public function isSecure()
	{
		return false;
	}

	/**
	 * @return Boolean
	 */
	protected function suffixSecureActionByDocument()
	{
		return false;
	}
}