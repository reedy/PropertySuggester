<?php

namespace PropertySuggester\Suggesters;

use DatabaseBase;
use MediaWikiTestCase;

use Wikibase\DataModel\Entity\PropertyId;

/**
 *
 * @covers PropertySuggester\Suggesters\SimplePHPSuggester
 *
 * @group Extensions/PropertySuggester
 *
 * @group API
 * @group Database
 *
 * @group medium
 *
 */
class SimplePHPSuggesterTest extends MediaWikiTestCase {

	/**
	 * @var DatabaseBase
	 */
	protected $dbr;

	/**
	 * @var SuggesterEngine
	 */
	protected $suggester;


	private function row($pid1, $pid2, $count, $probability) {
		return array('pid1' => $pid1, 'pid2' => $pid2, 'count' => $count, 'probability' => $probability);
	}

	public function addDBData() {
		$rows = array();
		$rows[] = $this->row(1, 2, 100, 0.1);
		$rows[] = $this->row(1, 3, 50, 0.05);
		$rows[] = $this->row(2, 3, 100, 0.1);
		$rows[] = $this->row(2, 4, 200, 0.2);
		$rows[] = $this->row(3, 1, 100, 0.5);

		$dbw = wfGetDB( DB_MASTER );
		$dbw->delete('wbs_propertypairs', "*");
		$dbw->insert('wbs_propertypairs', $rows);
	}

	public function setUp() {
		parent::setUp();
		$this->dbr = wfGetDB( DB_SLAVE );
		$this->suggester = new SimplePHPSuggester($this->dbr);

	}

	public function testDatabaseHasRows() {
		$res = $this->dbr->select('wbs_propertypairs', array( 'pid1', 'pid2'));
		$this->assertEquals(5, $res->numRows());
	}

	public function testSuggestByPropertyIds() {
		$ids = array( PropertyId::newFromNumber( 1 ) );

		$res = $this->suggester->suggestByPropertyIds($ids);

		$this->assertEquals(PropertyId::newFromNumber(2), $res[0]->getPropertyId());
		$this->assertEquals(PropertyId::newFromNumber(3), $res[1]->getPropertyId());
	}

	public function tearDown() {
		parent::tearDown();
	}
}

