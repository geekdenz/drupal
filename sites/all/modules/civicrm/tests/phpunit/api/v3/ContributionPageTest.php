<?php
// $Id$

require_once 'CiviTest/CiviUnitTestCase.php';
class api_v3_ContributionPageTest extends CiviUnitTestCase {
  protected $_apiversion = 3;
  protected $params;
  protected $ids = array();
  protected $_entity = 'contribution_page';
  protected $contribution_result = null;
  public $_eNoticeCompliant = TRUE;
  public $DBResetRequired = FALSE;
  public function setUp() {
    parent::setUp();
    $this->ids[] = $this->individualCreate();
    $this->params = array(
      'version' => 3,
      'contribution_type_id' => 1,
      'currency' => 'NZD',
      'goal_amount' => 1234,
    );
  }

  function tearDown() {
    foreach ($this->ids as $id) {
      civicrm_api('contact', 'delete', array('version' => $this->_apiversion, 'id' => $id));
    }
    $tablesToTruncate = array(
      'civicrm_contact',
      //'civicrm_contribution_type', // hack: in final tear down
      'civicrm_contribution',
      'civicrm_contribution_page',
    );
    $this->quickCleanup($tablesToTruncate);
  }

  public function testCreateContributionPage() {
    $result = civicrm_api($this->_entity, 'create', $this->params);
    $this->documentMe($this->params, $result, __FUNCTION__, __FILE__);
    $this->assertAPISuccess($result, 'In line ' . __LINE__);
    $this->assertEquals(1, $result['count'], 'In line ' . __LINE__);
    $this->assertNotNull($result['values'][$result['id']]['id'], 'In line ' . __LINE__);
    $this->getAndCheck($this->params, $result['id'], $this->_entity);
  }

  public function testGetContributionPage() {
    $result = civicrm_api($this->_entity, 'create', $this->params);
    $getParams = array(
      'version' => $this->_apiversion,
      //'amount' => '500',
      'currency' => 'NZD',
      'contribution_type_id' => 1,
    );
    $getResult = civicrm_api($this->_entity, 'get', $getParams);
    $this->documentMe($getParams, $getResult, __FUNCTION__, __FILE__);
    $this->assertAPISuccess($getResult, 'In line ' . __LINE__);
    $this->assertEquals(1, $getResult['count'], 'In line ' . __LINE__);
  }

  public function testDeleteContributionPage() {
    $result = civicrm_api($this->_entity, 'create', $this->params);
    $deleteParams = array('version' => 3, 'id' => $result['id']);
    $result = civicrm_api($this->_entity, 'delete', $deleteParams);
    $this->documentMe($deleteParams, $result, __FUNCTION__, __FILE__);
    $this->assertAPISuccess($result, 'In line ' . __LINE__);
    $checkDeleted = civicrm_api($this->_entity, 'get', array(
      'version' => 3,
      ));
    $this->assertEquals(0, $checkDeleted['count'], 'In line ' . __LINE__);
  }

  public function testGetFieldsContributionPage() {
    $result = civicrm_api($this->_entity, 'getfields', array('version' => 3, 'action' => 'create'));
    $this->assertAPISuccess($result, 'In line ' . __LINE__);
    $this->assertEquals(12, $result['values']['start_date']['type']);
  }
  public static function tearDownAfterClass(){
    $tablesToTruncate = array(
      'civicrm_contribution_type',
    );
    $unitTest = new CiviUnitTestCase();
    $unitTest->quickCleanup($tablesToTruncate);
  }
}

