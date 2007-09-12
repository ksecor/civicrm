<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.9                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
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
        
        $this->addFormRule(array('CRM_Mailing_Form_Schedule', 'formRule'));
        
        $this->addButtons(  array(
                                array(  'type'  => 'back',
                                        'name'  => ts('<< Previous')),
                                array(  'type'  => 'next',
                                        'name'  => ts('Done'),
                                        'isDefault' => true),
                                array(  'type'  => 'cancel',
                                        'name'  => ts('Cancel')),
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
    public static function formRule(&$params) 
    {
        if ( isset($params['now']) ) {
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
        
        $session =& CRM_Core_Session::singleton();
        $params['domain_id' ] = $session->get('domainID');
        $params['contact_id'] = $session->get('userID');
        
        /* Build the mailing object */
        require_once 'CRM/Mailing/BAO/Mailing.php';
        CRM_Mailing_BAO_Mailing::create( $params, $ids );
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

?>
