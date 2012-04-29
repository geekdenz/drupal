<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2011                                |
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
 * @copyright CiviCRM LLC (c) 2004-2011
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';
require_once 'CRM/Friend/BAO/Friend.php';

/**
 * This class generates form components for Tell A Friend Form For End User
 * 
 */
class CRM_Friend_Form extends CRM_Core_Form
{

     /** 
     * Constants for number of friend contacts
     */ 
    const NUM_OPTION = 3;
    /**
     * the id of the entity that we are proceessing
     *
     * @var int
     * @protected
     */
    protected $_entityId;

    /**
     * the table name of the entity that we are proceessing
     *
     * @var string
     * @protected
     */
    protected $_entityTable;

    protected $_campaignId;
    
    /**
     * the contact ID
     *
     * @var int
     * @protected
     */
    protected $_contactID;

    public function preProcess( )  
    {  
        $this->_action   = CRM_Utils_Request::retrieve( 'action', 'String'  , $this );
        $this->_entityId = CRM_Utils_Request::retrieve( 'eid'   , 'Positive', $this, true );       
        
        $pcomponent = CRM_Utils_Request::retrieve( 'pcomponent', 'String', $this, true );
        
        if ( in_array( $pcomponent, array( 'contribute', 'event' ) ) ) {
            $values = array( );
            $params = array( 'id' => $this->_entityId );
            CRM_Core_DAO::commonRetrieve( 'CRM_Contribute_DAO_ContributionPage', 
                                          $params, $values, array( 'title', 'campaign_id' ) );
            $this->_title      = CRM_Utils_Array::value( 'title',       $values );
            $this->_campaignId = CRM_Utils_Array::value( 'campaign_id', $values );
            $this->_entityTable = 'civicrm_contribution_page';
            if ( $pcomponent == 'event' ) $this->_entityTable = 'civicrm_event'; 
        } else if ( $pcomponent == 'pcp' ) {
            $this->_pcpBlockId = CRM_Utils_Request::retrieve( 'blockId', 'Positive', $this, true ); 
            
            $values = array( );
            $params = array( 'id' => $this->_pcpBlockId );
            CRM_Core_DAO::commonRetrieve( 'CRM_PCP_DAO_PCPBlock', 
                                          $params, $values, array( 'is_tellfriend_enabled', 'tellfriend_limit' ) );
            
            if ( ! CRM_Utils_Array::value( 'is_tellfriend_enabled', $values ) ) { 
                CRM_Core_Error::fatal( ts( 'Tell Friend is disable for this Personal Campaign Page' ) );
            }
            
            $this->_mailLimit   = $values['tellfriend_limit'];
            $this->_entityTable = 'civicrm_pcp';
            
            $sql = '
   SELECT  pcp.title, 
           contrib.campaign_id
  FROM  civicrm_pcp pcp
    INNER JOIN  civicrm_contribution_page contrib ON ( pcp.page_id = contrib.id AND pcp.page_type = "contribute" )  
  WHERE  pcp.id = %1';
            $pcp = CRM_Core_DAO::executeQuery( $sql, array( 1 => array( $this->_entityId, 'Positive' ) ) );
            while ( $pcp->fetch( ) ) {
                $this->_title      = $pcp->title;
                $this->_campaignId = $pcp->campaign_id;
                $pcp->free( );
            }
            
            $this->assign('pcpTitle', $this->_title);
        } else {
            CRM_Core_Error::fatal( ts( 'page argument missing or invalid' ) );
        }
        $this->assign('context', $pcomponent);

        $session = CRM_Core_Session::singleton( );
        $this->_contactID = $session->get( 'userID' );
        if ( ! $this->_contactID ) {
            $this->_contactID = $session->get( 'transaction.userID' );
        }

        if ( ! $this->_contactID ) {
            CRM_Core_Error::fatal( ts( 'Could not get the contact ID' ) );
        }

        // we do not want to display recently viewed items, so turn off
        $this->assign       ( 'displayRecent' , false );
    }

    /**
     * This function sets the default values for the form. 
     * 
     * @access public
     * @return None
     */
    public function setDefaultValues( ) 
    {
        $defaults = array( );      
        
        $defaults['entity_id']    = $this->_entityId;
        $defaults['entity_table'] = $this->_entityTable;   
         
        CRM_Friend_BAO_Friend::getValues($defaults);
        CRM_Utils_System::setTitle(CRM_Utils_Array::value( 'title', $defaults ) );

        $this->assign( 'title',   CRM_Utils_Array::value( 'title', $defaults ) );
        $this->assign( 'intro',   CRM_Utils_Array::value( 'intro', $defaults ) );
        $this->assign( 'message', CRM_Utils_Array::value( 'suggested_message', $defaults ) );
        $this->assign( 'entityID',  $this->_entityId );
        
        require_once "CRM/Contact/BAO/Contact.php";
        list( $fromName, $fromEmail ) = CRM_Contact_BAO_Contact::getContactDetails( $this->_contactID );

        $defaults['from_name' ] = $fromName;
        $defaults['from_email'] = $fromEmail;
       
        return $defaults;
    }

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        // Details of User        
        $name  =& $this->add( 'text',
                              'from_name',
                              ts('From'),
                              CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'first_name') );
        $name->freeze( );

        $email =& $this->add( 'text',
                              'from_email',
                              ts('Your Email'),
                              CRM_Core_DAO::getAttribute('CRM_Core_DAO_Email','email'),
                              true );
        $email->freeze( );

        $this->add('textarea', 'suggested_message', ts('Your Message'), CRM_Core_DAO::getAttribute('CRM_Friend_DAO_Friend', 'suggested_message'), true);
        
        $friend = array();
        $mailLimit = self::NUM_OPTION;
        if ( $this->_entityTable == 'civicrm_pcp' ) {
            $mailLimit = $this->_mailLimit;
        }
        $this->assign( 'mailLimit', $mailLimit + 1);
        for ( $i = 1; $i <= $mailLimit; $i++ ) {           
            $this->add('text', "friend[$i][first_name]", ts("Friend's First Name"));           
            $this->add('text', "friend[$i][last_name]", ts("Friend's Last Name")); 
            $this->add('text', "friend[$i][email]", ts("Friend's Email"));
            $this->addRule( "friend[$i][email]", ts('The format of this email address is not valid.'), 'email' );
        }
       
        $this->addButtons(array( 
                                array ( 'type'      => 'submit',
                                        'name'      => ts('Send Your Message'), 
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                        'isDefault' => true   ), 
                                array ( 'type'      => 'cancel', 
                                        'name'      => ts('Cancel') ), 
                                ) 
                          );
        
        $this->addFormRule(array('CRM_Friend_Form', 'formRule'));
    }
    
    
    /**
     * Function for validation
     *
     * @param array $params (ref.) an assoc array of name/value pairs
     *
     * @return mixed true or array of errors
     * @access public
     * @static
     */
    static function formRule( $fields ) 
    {

        $errors = array( ); 
        
        $valid = false;
        foreach ( $fields['friend'] as $key => $val ) {
            if ( trim( $val['first_name'] ) || trim( $val['last_name'] ) || trim( $val['email'] ) ) {
                $valid = true;
                
                if ( ! trim( $val['first_name'] ) ) {
                    $errors["friend[{$key}][first_name]"] = ts( 'Please enter your friend\'s first name.' );
                }

                if ( ! trim( $val['last_name'] ) ) {
                    $errors["friend[{$key}][last_name]"] = ts( 'Please enter your friend\'s last name.' );
                }

                if ( ! trim( $val['email'] ) ) {
                    $errors["friend[{$key}][email]"] = ts( 'Please enter your friend\'s email address.' );
                }
            } 
        }
        
        if ( ! $valid ) {
            $errors['friend[1][first_name]'] = ts( "Please enter at least one friend's information, or click Cancel if you don't want to send emails at this time." );
        }
        
        return empty($errors) ? true : $errors;
    }
       
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        // get the submitted form values.  
        $formValues = $this->controller->exportValues( $this->_name );
        
        $formValues['entity_id'        ]  = $this->_entityId;
        $formValues['entity_table'     ]  = $this->_entityTable;
        $formValues['source_contact_id']  = $this->_contactID;      
        $formValues['is_test'          ]  = $this->_action ? 1 : 0 ;
        $formValues['title'            ]  = $this->_title;
        $formValues['campaign_id'      ]  = $this->_campaignId;

        CRM_Friend_BAO_Friend::create( $formValues );

        $this->assign( 'status', 'thankyou' );
        $defaults = array( );      
        
        $defaults['entity_id']    = $this->_entityId;
        $defaults['entity_table'] = $this->_entityTable;            
        
        CRM_Friend_BAO_Friend::getValues($defaults);
        if ( $this->_entityTable == 'civicrm_pcp' ) {
            $defaults['thankyou_text'] = $defaults['thankyou_title'] = ts( 'Thanks for your Support' );
            $defaults['thankyou_text'] = ts( 'Thanks for supporting this campaign by spreading the word to your friends.' );
        } else if ( $this->_entityTable == 'civicrm_contribution_page' ) {
            // If this is tell a friend after contributing, give donor link to create their own fundraising page
            require_once 'CRM/PCP/BAO/PCP.php';
            if ( $linkText = CRM_PCP_BAO_PCP::getPcpBlockStatus( $defaults['entity_id'], $defaults['entity_table'] ) ) {
                
                $linkTextUrl = CRM_Utils_System::url( 'civicrm/contribute/campaign',
                                                     "action=add&reset=1&pageId={$defaults['entity_id']}&component=contribute",
                                                     false, null, true,
                                                     true );
                $this->assign( 'linkTextUrl', $linkTextUrl );
                $this->assign( 'linkText', $linkText );
            }           
        }
            
        CRM_Utils_System::setTitle($defaults['thankyou_title']);
        $this->assign( 'thankYouText'  , $defaults['thankyou_text'] );
   }
}

