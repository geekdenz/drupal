<?php
// $Id$

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2012                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

require_once 'CiviTest/CiviUnitTestCase.php';

class api_v3_MembershipPaymentTest extends CiviUnitTestCase {
  protected $_apiversion = 3; 
  protected $_contactID;
  protected $_contributionTypeID;
  protected $_membershipTypeID;
  protected $_membershipStatusID;
  protected $_contribution = array();
  function setUp() {
    parent::setUp();

    $this->_contactID = $this->organizationCreate(NULL);
    $this->_contributionTypeID = $this->contributionTypeCreate();
    $this->_membershipTypeID = $this->membershipTypeCreate($this->_contactID,$this->_contributionTypeID);
    $this->_membershipStatusID = $this->membershipStatusCreate('test status');
    $activityTypes = CRM_Core_PseudoConstant::activityType(TRUE, TRUE, TRUE, 'name');
        $params = array(
      'contact_id' => $this->_contactID,
      'currency' => 'USD',
      'contribution_type_id' => $this->_contributionTypeID,
      'contribution_status_id' => 1,
      'contribution_page_id' => NULL,
      'payment_instrument_id' => 1,
      'source' => 'STUDENT',
      'receive_date' => '20080522000000',
      'receipt_date' => '20080522000000',
      'id' => NULL,
      'total_amount' => 200.00,
      'trxn_id' => '22ereerwww322323',
      'invoice_id' => '22ed39c9e9ee6ef6031621ce0eafe6da70',
      'thankyou_date' => '20080522',
      'version' => 3,
    );
    $this->_contribution = civicrm_api('contribution','create', $params);
  }

  function tearDown() {
    $this->quickCleanup(
      array(
        'civicrm_contact',
        'civicrm_contribution',
        'civicrm_membership',
        'civicrm_membership_payment',
        'civicrm_membership_status',
        'civicrm_membership_type',

      )
    );
    $this->contributionTypeDelete();
  }

  ///////////////// civicrm_membership_payment_create methods

  /**
   * Test civicrm_membership_payment_create with empty params.
   */
  public function testCreateEmptyParams() {
    $params = array('version' => $this->_apiversion);
    $CreateEmptyParams = civicrm_api('membership_payment', 'create', $params);
    $this->assertEquals($CreateEmptyParams['error_message'], 'Mandatory key(s) missing from params array: membership_id, contribution_id');
  }

  /**
   * Test civicrm_membership_payment_create - success expected.
   */
  public function testCreate() {
    $contactId = $this->individualCreate(NULL);



    $params = array(
      'contact_id' => $contactId,
      'membership_type_id' => $this->_membershipTypeID,
      'join_date' => '2006-01-21',
      'start_date' => '2006-01-21',
      'end_date' => '2006-12-21',
      'source' => 'Payment',
      'is_override' => 1,
      'status_id' => $this->_membershipStatusID,
      'version' => API_LATEST_VERSION,
    );

    $membership = civicrm_api('membership', 'create', $params);
    $this->assertAPISuccess($membership, "membership created in line " . __LINE__);

    $params = array(
      'contribution_id' => $this->_contribution['id'],
      'membership_id' => $membership['id'],
      'version' => $this->_apiversion,
    );
    $result = civicrm_api('membership_payment', 'create', $params);
    $this->documentMe($params, $result, __FUNCTION__, __FILE__);
    $this->assertEquals($result['values'][$result['id']]['membership_id'], $membership['id'], 'Check Membership Id in line ' . __LINE__);
    $this->assertEquals($result['values'][$result['id']]['contribution_id'], $this->_contribution['id'], 'Check Contribution Id in line ' . __LINE__);

  }


  ///////////////// civicrm_membershipPayment_get methods

  /**
   * Test civicrm_membershipPayment_get with wrong params type.
   */
  public function testGetWrongParamsType() {
    $params = 'eeee';
    $GetWrongParamsType = civicrm_api('membership_payment', 'get', $params);
    $this->assertEquals($GetWrongParamsType['error_message'], 'Input variable `params` is not an array');
  }

  /**
   * Test civicrm_membershipPayment_get with empty params.
   */
  public function testGetEmptyParams() {
    $params = array();
    $GetEmptyParams = civicrm_api('membership_payment', 'get', $params);
    $this->assertEquals($GetEmptyParams['error_message'], 'Mandatory key(s) missing from params array: version');
  }

  /**
   * Test civicrm_membershipPayment_get - success expected.
   */
  public function testGet() {
    $contactId = $this->individualCreate(NULL);
    $params = array(
      'contact_id' => $contactId,
      'membership_type_id' => $this->_membershipTypeID,
      'source' => 'Payment',
      'is_override' => 1,
      'status_id' => $this->_membershipStatusID,
      'version' => $this->_apiversion,
    );
    $ids = array();
    $membership = CRM_Member_BAO_Membership::create($params, $ids);

    $params = array(
      'contribution_id' => $this->_contribution['id'],
      'membership_id' => $membership->id,
      'version' => $this->_apiversion,
    );
    civicrm_api('membership_payment', 'create', $params);

    $result = civicrm_api('membership_payment', 'get', $params);
    $this->documentMe($params, $result, __FUNCTION__, __FILE__);
    $this->assertEquals($result['values'][$result['id']]['membership_id'], $membership->id, 'Check Membership Id');
    $this->assertEquals($result['values'][$result['id']]['contribution_id'], $this->_contribution['id'], 'Check Contribution Id');

  }
}

