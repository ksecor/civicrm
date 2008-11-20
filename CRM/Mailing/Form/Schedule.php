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
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

/**
 *
 */
class CRM_Mailing_Form_Schedule extends CRM_Core_Form 
{

    /**
     * This function sets the default values for the form.
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {
        $count = $this->get('count');
        $this->assign('count',$count);
    }
    
    /**
     * Build the form for the last step of the mailing wizard
     *
     * @param
     * @return void
     * @access public
     */
    public function buildQuickform() 
    {
        $this->addElement('date', 'start_date', ts('Schedule Mailing'),
            CRM_Core_SelectValues::date('mailing'));
        $this->addElement('checkbox', 'now', ts('Send Immediately'));
        
        $this->addFormRule(array('CRM_Mailing_Form_Schedule', 'formRule'), $this );
        
        $this->addButtons( array(
                                 array(  'type'  => 'back',
                                         'name'  => ts('<< Previous')),
                                 array(  'type'  => 'next',
                                         'name'  => ts('Done'),
                                         'isDefault' => true),
                                 array(  'type'  => 'cancel',
                                         'name'  => ts('Cancel')),
                                 array ( 'type'  => 'submit',
                                         'name'  => ts('Save & Continue Later')
                                         ),
                                 )
                           );
    }
    
    /**
     * Form rule to validate the date selector and/or if we should deliver
     * immediately.
     *
     * Warning: if you make changes here, be sure to also make them in
     * Retry.php
     * 
     * @param array $params     The form values
     * @return boolean          True if either we deliver immediately, or the
     *                          date is properly set.
     * @static
     */
    public static function formRule(&$params, &$files, &$self) 
    {
        if ( $params['_qf_Schedule_submit'] ) {
            //when user perform mailing from search context 
            //redirect it to search result CRM-3711.
            $ssID    = $self->get( 'ssID' );
            $context = $self->get( 'context' );
            if ( $ssID && $context == 'search' ) {
                if ( $self->_action == CRM_Core_Action::BASIC ) {
                    $fragment = 'search';
                } else if ( $self->_action == CRM_Core_Action::PROFILE ) {
                    $fragment = 'search/builder';
                } else if ( $self->_action == CRM_Core_Action::ADVANCED ) {
                    $fragment = 'search/advanced';
                } else {
                    $fragment = 'search/custom';
                }
                
                $draftURL = CRM_Utils_System::url( 'civicrm/mailing/browse/unscheduled', 'scheduled=false&reset=1' );
                $status = ts("Your mailing has been saved. You can continue later by clicking the 'Continue' action to resume working on it.<br /> From <a href='%1'>Draft and Unscheduled Mailings</a>.", array( 1 => $draftURL ) );
                CRM_Core_Session::setStatus( $status );
                
                //replace user context to search.
                $url = CRM_Utils_System::url( "civicrm/contact/" . $fragment, "force=1&reset=1&ssID={$ssID}" );
                CRM_Utils_System::redirect( $url );
            } else {
                CRM_Core_Session::setStatus( ts("Your mailing has been saved. Click the 'Continue' action to resume working on it.") );
                $url = CRM_Utils_System::url( 'civicrm/mailing/browse/unscheduled', 'scheduled=false&reset=1' );
                CRM_Utils_System::redirect($url);
            }
        }
        if ( isset($params['now']) || $params['_qf_Schedule_back'] == '<< Previous' ) {
            return true;
        }
        if (! CRM_Utils_Rule::qfDate($params['start_date'])) {
            return array('start_date' => ts('Scheduled date is not valid.'));
        }
        if (CRM_Utils_Date::format($params['start_date']) < date('YmdHi00')) {
            return array('start_date' => 
                ts('Start date cannot be earlier than the current time.'));
        }
        return true;
    }

    /**
     * Process the posted form values.  Create and schedule a mailing.
     *
     * @param
     * @return void
     * @access public
     */
    public function postProcess() 
    {
        $params = array();
        $params['mailing_id'] = $ids['mailing_id'] = $this->get('mailing_id');
        
        foreach(array('now', 'start_date') as $parameter) {
            $params[$parameter] = $this->controller->exportValue($this->_name,
                                                                 $parameter);
        }

        require_once 'CRM/Mailing/BAO/Mailing.php';
        $mailing =& new CRM_Mailing_BAO_Mailing();
        $mailing->id = $ids['mailing_id'];

        if ($mailing->find(true)) {
            
            $job =& new CRM_Mailing_BAO_Job();
            $job->mailing_id = $mailing->id;
            
            if ( ! $mailing->is_template) {
                $job->status = 'Scheduled';
                if ($params['now']) {
                    $job->scheduled_date = date('YmdHis');
                } else {
                    $job->scheduled_date = CRM_Utils_Date::format($params['start_date']);
                }
                $job->save();
            } 

            // also set the scheduled_id 
            $session =& CRM_Core_Session::singleton( );
            $mailing->scheduled_id = $session->get( 'userID' );
            $mailing->save( );
            
        }
        
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
            $url = CRM_Utils_System::url( 'civicrm/contact/' . $fragment, "force=1&reset=1&ssID={$ssID}" );
            CRM_Utils_System::redirect( $url );
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
        return ts( 'Schedule or Send' );
    }

}


