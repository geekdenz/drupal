<?php
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

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2012
 * $Id$
 *
 */

/**
 * form helper class for address section 
 */
class CRM_Contact_Form_Inline_Address extends CRM_Core_Form {

  /**
   * contact id of the contact that is been viewed
   */
  public $_contactId;

  /**
   * location block no 
   */
  private $_locBlockNo;

  /**
   * Do we want to parse street address.
   */
  public $_parseStreetAddress;

  /**
   * store address values
   */
  public $_values;

  /**
   * form action
   */
  public $_action;

  /**
   * Since we are using same class / code to generate multiple instances 
   * of address block, we need to generate unique form name for each, 
   * hence calling parent contructor
   */
  function __construct( ) {
    $locBlockNo = CRM_Utils_Request::retrieve('locno', 'Positive', CRM_Core_DAO::$_nullObject, TRUE, NULL, $_REQUEST);
    $name = "Address_{$locBlockNo}";

    parent::__construct( null, CRM_Core_Action::NONE, 'post', $name ); 
  }

  /**
   * call preprocess
   */
  public function preProcess() {
    //get all the existing email addresses
    $this->_contactId = CRM_Utils_Request::retrieve('cid', 'Positive', $this, TRUE, NULL, $_REQUEST);
    $this->assign('contactId', $this->_contactId);
    $this->_locBlockNo = CRM_Utils_Request::retrieve('locno', 'Positive', $this, TRUE, NULL, $_REQUEST);
    $this->assign('blockId', $this->_locBlockNo);
   
    $addressSequence = CRM_Core_BAO_Address::addressSequence();
    $this->assign('addressSequence', $addressSequence);

    $this->_values = array();    
    $addressId = CRM_Utils_Request::retrieve('aid', 'Positive', $this, FALSE, NULL, $_REQUEST);

    $this->_action = CRM_Core_Action::ADD;
    if ( $addressId ) {
      $params = array( 'id' => $addressId );
      $address = CRM_Core_BAO_Address::getValues( $params, FALSE, 'id' );
      $this->_values['address'][$this->_locBlockNo] = array_pop($address);
      $this->_action = CRM_Core_Action::UPDATE;
    }
    else {
      $addressId = 0;
    }

    $this->assign('action', $this->_action);
    $this->assign('addressId', $addressId);
    
    // parse street address, CRM-5450
    $this->_parseStreetAddress = $this->get('parseStreetAddress');
    if (!isset($this->_parseStreetAddress)) {
      $addressOptions = CRM_Core_BAO_Setting::valueOptions(CRM_Core_BAO_Setting::SYSTEM_PREFERENCES_NAME,
        'address_options'
      );
      $this->_parseStreetAddress = FALSE;
      if (CRM_Utils_Array::value('street_address', $addressOptions) &&
        CRM_Utils_Array::value('street_address_parsing', $addressOptions)
      ) {
        $this->_parseStreetAddress = TRUE;
      }
      $this->set('parseStreetAddress', $this->_parseStreetAddress);
    }
    $this->assign('parseStreetAddress', $this->_parseStreetAddress);
  }

  /**
   * build the form elements for an email object
   *
   * @return void
   * @access public
   */
  public function buildQuickForm() {
    CRM_Contact_Form_Edit_Address::buildQuickForm( $this, $this->_locBlockNo, TRUE, TRUE );

    $buttons = array(
      array(
        'type' => 'upload',
        'name' => ts('Save'),
        'isDefault' => TRUE,
      ),
      array(
        'type' => 'cancel',
        'name' => ts('Cancel'),
      ),
    );

    $this->addButtons($buttons);
  }

  /**
   * Override default cancel action
   */
  function cancelAction() {
    $response = array('status' => 'cancel');
    echo json_encode($response);
    CRM_Utils_System::civiExit();
  }

  /**
   * set defaults for the form
   *
   * @return void
   * @access public
   */
  public function setDefaultValues() {
    $defaults = $this->_values;

    $config = CRM_Core_Config::singleton();
    //set address block defaults
    if ( CRM_Utils_Array::value( 'address', $defaults ) ) {
      CRM_Contact_Form_Edit_Address::setDefaultValues( $defaults, $this );
    } 
    else {
      // get the default location type
      $locationType = CRM_Core_BAO_LocationType::getDefault();

      if ( $this->_locBlockNo == 1 ) {
        $address['is_primary'] = TRUE;
        $address['location_type_id'] = $locationType->id;
      }
      
      $address['country_id'] = $config->defaultContactCountry;
      $defaults['address'][$this->_locBlockNo] = $address;
    } 

    $values = $defaults['address'][$this->_locBlockNo];
    
    CRM_Contact_Form_Edit_Address::fixStateSelect($this,
      "address[$this->_locBlockNo][country_id]",
      "address[$this->_locBlockNo][state_province_id]",
      "address[$this->_locBlockNo][county_id]",
      CRM_Utils_Array::value('country_id',
        $values, $config->defaultContactCountry
      ),
      CRM_Utils_Array::value('state_province_id', $values)
    );

    return $defaults;
  }

  /**
   * process the form
   *
   * @return void
   * @access public
   */
  public function postProcess() {
    $params = $this->exportValues();

    // need to process / save address 
    $params['contact_id'] = $this->_contactId;
    $params['updateBlankLocInfo'] = TRUE;

    // process shared contact address.
    CRM_Contact_BAO_Contact_Utils::processSharedAddress($params['address']);

    if ($this->_parseStreetAddress) {
      CRM_Contact_Form_Contact::parseAddress($params);
    }

    // save address changes
    $address = CRM_Core_BAO_Address::create( $params, TRUE );

    // make entry in log table
    CRM_Core_BAO_Log::register( $this->_contactId,
      'civicrm_contact',
      $this->_contactId
    );
   
    $response = array(
      'status'    => 'save',
      'addressId' => $address[0]->id
    );
    echo json_encode($response);
    CRM_Utils_System::civiExit();
  }
}

