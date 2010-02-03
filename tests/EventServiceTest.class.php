<?php

class agenda_EventServiceTest extends f_tests_AbstractBaseTest
{
	protected function prepareTestCase()
	{
		$this->truncateAllTables();
	}
	protected function prepareTest()
	{
		RequestContext::getInstance('fr en')->setLang('fr');
	}

	protected function endTest()
	{
		RequestContext::clearInstance();
	}

	public function testBla()
	{
		
	}
/*	public function testGetEventListForMonth()
	{
		$now = date_Calendar::now();
		$service = agenda_EventService::getInstance();
		$myEvent1 = $service->getNewDocumentInstance();
		$myEvent1->setDate($now->toString());
		$myEvent1->setLabel('News de '. date_DateFormat::format($now, 'F', 'fr'));
		$myEvent1->save();
		$this->addToRootFolder($myEvent1);
		$myEvent2 = $service->getNewDocumentInstance();
		$myEvent2->setDate($now->toString());
		$myEvent2->setLabel('Une autre news de '. date_DateFormat::format($now, 'F', 'fr'));
		$myEvent2->save();
		$this->addToRootFolder($myEvent2);
		$myEvent3 = $service->getNewDocumentInstance();
		$myEvent3->setDate($now->add(date_Calendar::MONTH, 1)->toString());
		$myEvent3->setLabel('Une autre news de '. date_DateFormat::format($now, 'F', 'fr'));
		$myEvent3->save();
		$this->addToRootFolder($myEvent3);


		$now = date_Calendar::now();
		$list = $service->getEventListForMonth(null, $now, true);

		$this->assertCount(2, $list);
		$this->assertContains($myEvent1, $list);
		$this->assertContains($myEvent2, $list);
		$this->assertNotContains($myEvent3, $list);

		$now->add(date_Calendar::MONTH, 1);
		$list = $service->getEventListForMonth(null, $now, true);
		$this->assertCount(1, $list);
		$this->assertNotContains($myEvent1, $list);
		$this->assertNotContains($myEvent2, $list);
		$this->assertContains($myEvent3, $list);

		$now->add(date_Calendar::MONTH, 1);
		$list = $service->getEventListForMonth(null, $now, true);
		$this->assertCount(0, $list);
		$this->assertNotContains($myEvent1, $list);
		$this->assertNotContains($myEvent2, $list);
		$this->assertNotContains($myEvent3, $list);

		$myEvent1->delete();
		$myEvent2->delete();
		$myEvent3->delete();
	}

	public function testGetFormattedEventListForMonth()
	{
		$now = date_Calendar::now();
		$service = agenda_EventService::getInstance();
		$myEvent1 = $service->getNewDocumentInstance();
		$myEvent1->setDate($now->toString());
		$myEvent1->setLabel('News de '. date_DateFormat::format($now, 'F', 'fr'));
		$myEvent1->save();
		$this->addToRootFolder($myEvent1);
		$myEvent2 = $service->getNewDocumentInstance();
		$myEvent2->setDate($now->toString());
		$notnow = date_Calendar::now()->add(date_Calendar::DAY, 7);
		$myEvent2->setEnddate($notnow->toString());
		$myEvent2->setLabel('Une autre news de '. date_DateFormat::format($now, 'F', 'fr'));
		$myEvent2->save();
		$this->addToRootFolder($myEvent2);
		$myEvent3 = $service->getNewDocumentInstance();
		$myEvent3->setDate($now->add(date_Calendar::MONTH, 1)->toString());
		$myEvent3->setLabel('Une autre news de '. date_DateFormat::format($now, 'F', 'fr'));
		$myEvent3->save();
		$this->addToRootFolder($myEvent3);

		$now = date_Calendar::now();
		//var_dump($service->getEventCardinalitiesForMonth(null, $now, true));
		var_dump(new agenda_CalendarMonth(null, $now, true));
		$myEvent1->delete();
		$myEvent2->delete();
		$myEvent3->delete();
	}
	private function addToRootFolder($doc)
	{
		$rootId = ModuleService::getInstance()->getRootFolderId('agenda');
		TreeService::getInstance()->newLastChild($rootId, $doc->getId());
	}*/

}