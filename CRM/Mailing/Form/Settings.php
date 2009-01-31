<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

/**
 * This file is used to build the form configuring mailing details
 */
class CRM_Mailing_Form_Settings extends CRM_Core_Form 
{
    /**
     * This function sets the default values for the form.
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {
        $mailingID = CRM_Utils_Request::retrieve('mid', 'Integer', $this, false, null );
        $count     = $this->get('count');
        $this->assign('count',$count);
        $defaults = array( );

        $daoComponent =& new CRM_Mailing_DAO_Component();
        $components = array('Reply', 'OptOut', 'Unsubscribe', 'Resubscribe');
        
        foreach ($components as $value) {
            $findDefaultComponent =
                "SELECT id
                FROM    civicrm_mailing_component
                WHERE   component_type = '$value'
                ORDER BY is_default desc";
            
            $daoComponent->query($findDefaultComponent);
            
            if ( $daoComponent->fetch( ) ) {
                $$value = $daoComponent->id;
            }
        }
        
        $defaults['reply_id']       = $Reply;
        $defaults['optout_id']      = $OptOut;
        $defaults['unsubscribe_id'] = $Unsubscribe;
        $defaults['resubscribe_id'] = $Resubscribe;

        if ( $mailingID ) {
            require_once "CRM/Mailing/DAO/Mailing.php";
            $dao =&new  CRM_Mailing_DAO_Mailing();
            $dao->id = $mailingID; 
            $dao->find(true);
            $dao->storeValues($dao, $defaults);
        }
        return $defaults;
    }

 
    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        require_once 'CRM/Mailing/PseudoConstant.php';

        $this->add('checkbox', 'forward_replies', ts('Forward Replies?'));
        $defaults['forward_replies'] = true;
        
        $this->add('checkbox', 'url_tracking', ts('Track Click-throughs?'));
        $defaults['url_tracking'] = true;
        
        $this->add('checkbox', 'open_tracking', ts('Track Opens?'));
        $defaults['open_tracking'] = true;
        
        $this->add('checkbox', 'auto_responder', ts('Auto-respond to Replies?'));
        $defaults['auto_responder'] = false;
        
        $this->add( 'select', 'reply_id', ts( 'Auto-responder' ), 
                    CRM_Mailing_PseudoConstant::component( 'Reply' ), true );
        
        $this->add( 'select', 'unsubscribe_id', ts( 'Unsubscribe Message' ), 
                    CRM_Mailing_PseudoConstant::component( 'Unsubscribe' ), true );
        
        $this->add( 'select', 'resubscribe_id', ts( 'Resubscribe Message' ), 
                    CRM_Mailing_PseudoConstant::component( 'Resubscribe' ), true );
        
        $this->add( 'select', 'optout_id', ts( 'Opt-out Message' ), 
                    CRM_Mailing_PseudoConstant::component( 'OptOut' ), true );
        
        $this->addButtons( array(
                                 array ( 'type'      => 'back',
                                         'name'      => ts('<< Previous') ),
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Next >>'),
                                         'spacing' => '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;',
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 array ( 'type'      => 'submit',
                                         'name'      => ts('Save & Continue Later') )
                                 )
                           );
        $this->setDefaults($defaults);
    }
    
    public function postProcess() 
    {
        $params = $ids       = array( );
        
        $uploadParams        = array('reply_id', 'unsubscribe_id', 'optout_id', 'resubscribe_id');
        $uploadParamsBoolean = array('forward_replies', 'url_tracking', 'open_tracking', 'auto_responder');
       
        $qf_Settings_submit = $this->controller->exportValue($this->_name, '_qf_Settings_submit');
        
        foreach ( $uploadParams as $key ) {
            $params[$key] = $this->controller->exportvalue($this->_name, $key);
            $this->set($key, $this->controller->exportvalue($this->_name, $key));
        }
        
        foreach ( $uploadParamsBoolean as $key ) {
            if ( $this->controller->exportvalue($this->_name, $key) ) {
                $params[$key] = true;
            } else {
                $params[$key] = false;
            }
            $this->set($key, $this->controller->exportvalue($this->_name, $key));
        }
        
        $ids['mailing_id']    = $this->get('mailing_id');
        
        // update mailing
        require_once 'CRM/Mailing/BAO/Mailing.php';
        CRM_Mailing_BAO_Mailing::create($params, $ids);

        if ( $qf_Settings_submit ) {
            //when user perform mailing from search context 
            //redirect it to search result CRM-3711.
            $ssID    = $this->get( 'ssID' );
            $context = $this->get( 'context' );
            if ( $ssID && $context == 'search' ) {
                if ( $this->_action == CRM_Core_Action::BASIC ) {
                    $fragment = 'search';
                } else if ( $this->_action == CRM_Core_Action::PROFILE ) {
                    $fragment = 'search/builder';
                } else if ( $this->_action == CRM_Core_Action::ADVANCED ) {
                    $fragment = 'search/advanced';
                } else {
                    $fragment = 'search/custom';
                }
                
                $draftURL = CRM_Utils_System::url( 'civicrm/mailing/browse/unscheduled', 'scheduled=false&reset=1' );
                $status = ts("Your mailing has been saved. You can continue later by clicking the 'Continue' action to resume working on it.<br /> From <a href='%1'>Draft and Unscheduled Mailings</a>.", array( 1 => $draftURL ) );
                CRM_Core_Session::setStatus( $status );
                
                //replace user context to search.
                $url = CRM_Utils_System::url( 'civicrm/contact/' . $fragment, "force=1&reset=1&ssID={$ssID}" );
                CRM_Utils_System::redirect( $url );
            } else { 
                $status = ts("Your mailing has been saved. Click the 'Continue' action to resume working on it.");
                CRM_Core_Session::setStatus( $status );
                $url = CRM_Utils_System::url( 'civicrm/mailing/browse/unscheduled', 'scheduled=false&reset=1' );
                CRM_Utils_System::redirect($url);
            }
        }
    }
    
    /**
     * Display Name of the form
     *
     * @access public
     * @return string
     */
    public function getTitle( ) 
    {
        return ts( 'Track and Respond' );
    }
}


