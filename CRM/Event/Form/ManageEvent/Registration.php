<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Event/Form/ManageEvent.php';
require_once 'CRM/Event/BAO/Event.php';

/**
 * This class generates form components for processing Event  
 * 
 */
class CRM_Event_Form_ManageEvent_Registration extends CRM_Event_Form_ManageEvent
{
    /**
     * what blocks should we show and hide.
     *
     * @var CRM_Core_ShowHideBlocks
     */
    protected $_showHide;

    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    function preProcess( ) {
        parent::preProcess( );
    }

    /**
     * This function sets the default values for the form. 
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {
        $eventId = $this->_id;

        $defaults = parent::setDefaultValues( );

        $this->setShowHide( $defaults );
        if ( isset( $eventId ) ) {
            $params = array( 'id' => $eventId );
            CRM_Event_BAO_Event::retrieve( $params, $defaults );
            
            require_once 'CRM/Core/BAO/UFJoin.php';
            $ufJoinParams = array( 'entity_table' => 'civicrm_event',
                                   'entity_id'    => $eventId );

            list( $defaults['custom_pre_id'],
                  $defaults['custom_post_id'] ) = 
                CRM_Core_BAO_UFJoin::getUFGroupIds( $ufJoinParams ); 
        } else {
            $defaults['is_email_confirm'] = 0;
        }

        // Provide defaults for Confirm and Thank you titles if we're in New Event Wizard
        if ( ! $this->_single ) {
            $defaults['confirm_title'] = 'Confirm Your Registration Information';
            $defaults['thankyou_title'] = 'Thank You for Registering';
        }
        
        return $defaults;
    }   
    
    /**
     * Fix what blocks to show/hide based on the default values set
     *
     * @param array   $defaults the array of default values
     * @param boolean $force    should we set show hide based on input defaults
     *
     * @return void
     */
    function setShowHide( &$defaults) 
    {
        require_once 'CRM/Core/ShowHideBlocks.php';
        $this->_showHide =& new CRM_Core_ShowHideBlocks( array('registration' => 1 ),
                                                         '') ;
        if ( empty($defaults)) {
            $this->_showHide->addShow( 'registration_screen_show' );
            $this->_showHide->addShow( 'confirm_show' );
            $this->_showHide->addShow( 'mail_show' );
            $this->_showHide->addShow( 'thankyou_show' );
            $this->_showHide->addHide( 'registration' );
            $this->_showHide->addHide( 'registration_screen' );
            $this->_showHide->addHide( 'confirm' );
            $this->_showHide->addHide( 'mail' );
            $this->_showHide->addHide( 'thankyou' );
        } else {
            $this->_showHide->addShow( 'registration' );
            $this->_showHide->addShow( 'confirm' );
            $this->_showHide->addShow( 'mail' );
            $this->_showHide->addShow( 'thankyou' );
            $this->_showHide->addHide( 'registration_screen_show' );
            $this->_showHide->addHide( 'confirm_show' );            
            $this->_showHide->addHide( 'mail_show' );
            $this->_showHide->addHide( 'thankyou_show' );
        }
        $this->_showHide->addToTemplate( );
    }

    /** 
     * Function to build the form 
     * 
     * @return None 
     * @access public 
     */ 
    public function buildQuickForm( )  
    { 
        $this->applyFilter('__ALL__', 'trim');

        $this->addElement('checkbox', 'is_online_registration', ts('Allow Online Registration?'),null,array('onclick' =>"return showHideByValue('is_online_registration','','register_show','block','radio',false);")); 
        
        $this->add('text','registration_link_text',ts('Registration Link Text'));

        $this->add( 'date',
                    'registration_start_date',
                    ts( 'Registration Start Date'  ),
                    CRM_Core_SelectValues::date('datetime') );
        $this->addRule('registration_start_date', ts('Please select a valid start date.'), 'qfDate');

        $this->add( 'date',
                    'registration_end_date',
                    ts( 'Registration End Date'  ),
                    CRM_Core_SelectValues::date('datetime') );
        $this->addRule('registration_end_date', ts('Please select a valid end date.'), 'qfDate');
     
        $this->addElement('checkbox', 'is_multiple_registrations', ts('Register Multiple Participants?')); 
        self::buildRegistrationBlock( $this );
        self::buildConfirmationBlock( $this );
        self::buildMailBlock( $this );
        self::buildThankYouBlock( $this );

        parent::buildQuickForm();
    }
    
    /**
     * Function to build Registration Block  
     * 
     * @param int $pageId 
     * @static
     */
    function buildRegistrationBlock(&$form ) 
    {
        $attributes = CRM_Core_DAO::getAttribute('CRM_Event_DAO_Event');
        $form->add('textarea','intro_text',ts('Introductory Text'), $attributes['intro_text']);
        $form->add('textarea','footer_text',ts('Footer Text'), $attributes['footer_text']);

        require_once "CRM/Core/BAO/UFGroup.php";
        $types    = array( 'Contact', 'Individual','Organization', 'Household','Participant' );
        $profiles = CRM_Core_BAO_UFGroup::getProfiles( $types ); 

        $form->add('select', 'custom_pre_id', ts('Include Profile') . '<br />' . ts('(top of page)'),array(''=>'- select -') +  $profiles );
        $form->add('select', 'custom_post_id', ts('Include Profile') . '<br />' . ts('(bottom of page)'),array(''=>'- select -')+  $profiles );
    }

    /**
     * Function to build Confirmation Block  
     * 
     * @param int $pageId 
     * @static
     */
    function buildConfirmationBlock(&$form) 
    {
        $attributes = CRM_Core_DAO::getAttribute('CRM_Event_DAO_Event');
        $form->add('text','confirm_title',ts('Title'), $attributes['confirm_title']);
        $form->add('textarea','confirm_text',ts('Introductory Text'), $attributes['confirm_text']);
        $form->add('textarea','confirm_footer_text',ts('Footer Text'), $attributes['confirm_footer_text']);     
    }

    /**
     * Function to build Email Block  
     * 
     * @param int $pageId 
     * @static
     */
    function buildMailBlock(&$form ) 
    {
        $attributes = CRM_Core_DAO::getAttribute('CRM_Event_DAO_Event');
        $form->addYesNo( 'is_email_confirm', ts( 'Send Confirmation Email?' ) , null, null, array('onclick' =>"return showHideByValue('is_email_confirm','','confirmEmail','block','radio',false);"));
        $form->add('textarea','confirm_email_text',ts('Text'), $attributes['confirm_email_text']);
        $form->add('text','cc_confirm',ts('CC Confirmation To'));
        $form->addRule( "cc_confirm", ts('Email is not valid.'), 'email' );  
        $form->add('text','bcc_confirm',ts('BCC Confirmation To'));  
        $form->addRule( "bcc_confirm", ts('Email is not valid.'), 'email' );          
        $form->add('text', 'confirm_from_name', ts('Confirm From Name') );
        $form->add('text', 'confirm_from_email', ts('Confirm From Email') );  
        $form->addRule( "confirm_from_email", ts('Email is not valid.'), 'email' );
    }

    function buildThankYouBlock(&$form) 
    {
        $attributes = CRM_Core_DAO::getAttribute('CRM_Event_DAO_Event');
        $form->add('text','thankyou_title',ts('Title'), $attributes['thankyou_title']);
        $form->add('textarea','thankyou_text',ts('Introductory Text'), $attributes['thankyou_text']);
        $form->add('textarea','thankyou_footer_text',ts('Footer Text'), $attributes['thankyou_footer_text']);
    }
    /**
     * Add local and global form rules
     *
     * @access protected
     * @return void
     */
    function addRules( ) 
    {
        $this->addFormRule( array( 'CRM_Event_Form_ManageEvent_Registration', 'formRule' ) );
    }
    
    /**
     * global validation rules for the form
     *
     * @param array $fields posted values of the form
     *
     * @return array list of errors to be posted back to the form
     * @static
     * @access public
     */
    static function formRule( &$values ) 
    {
        if ( $values['is_online_registration'] ) {
            if ( !$values['confirm_title'] ) {
                $errorMsg['confirm_title'] = ts('Please enter a Title for the registration Confirmation Page');
            }
            if ( !$values['thankyou_title'] ) {
                $errorMsg['thankyou_title'] = ts('Please enter a Title for the registration Thank-you Page');
            }
            if ( $values['is_email_confirm'] ) { 
                if ( !$values['confirm_from_name'] ) {
                    $errorMsg['confirm_from_name'] = ts('Please enter Confirmation Email FROM Name.');
                } 
                
                if ( !$values['confirm_from_email'] ) {
                    $errorMsg['confirm_from_email'] = ts('Please enter Confirmation Email FROM Email Address.');
                }
            }
        }
        
        if ( !empty($errorMsg) ) {
            return $errorMsg;
        }        
        
        return true;
    }
    
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {   
        $params = $ids = array();
        $params = $this->exportValues();
               
        $eventId = $this->_id;
        $params['event_id'] = $ids['event_id'] = $eventId;

        //format params
        $params['is_online_registration'] = CRM_Utils_Array::value('is_online_registration', $params, false);
        $params['is_multiple_registrations'] = CRM_Utils_Array::value('is_multiple_registrations', $params, false);

        // reset is_email confirm if not online reg
        if ( ! $params['is_online_registration'] ) {
            $params['is_email_confirm'] = false;
        }

        $params['registration_start_date'] = CRM_Utils_Date::format( $params['registration_start_date'] );
        $params['registration_end_date'] = CRM_Utils_Date::format( $params['registration_end_date'] );

        require_once 'CRM/Event/BAO/Event.php';
        CRM_Event_BAO_Event::add($params ,$ids);
       
            
        // also update the ProfileModule tables 
        $ufJoinParams = array( 'is_active'    => 1, 
                               'module'       => 'CiviEvent',
                               'entity_table' => 'civicrm_event', 
                               'entity_id'    => $eventId, 
                               'weight'       => 1, 
                               'uf_group_id'  => $params['custom_pre_id'] ); 
        
        require_once 'CRM/Core/BAO/UFJoin.php';
        CRM_Core_BAO_UFJoin::create( $ufJoinParams ); 

        $ufJoinParams['weight'     ] = 2; 
        $ufJoinParams['uf_group_id'] = $params['custom_post_id'];  
        CRM_Core_BAO_UFJoin::create( $ufJoinParams );         
         
    }//end of function
    
    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle( ) 
    {
        return ts('Online Registration');
    }

    
    
}

