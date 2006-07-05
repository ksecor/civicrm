<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

/**
 *
 */
class CRM_Mailing_Form_Schedule extends CRM_Core_Form {

    /**
     * Build the form for the last step of the mailing wizard
     *
     * @param
     * @return void
     * @access public
     */
    public function buildQuickform() {
        $this->addElement('date', 'start_date', ts('Start Date'),
            CRM_Core_SelectValues::date('mailing'));
        $this->addElement('checkbox', 'now', ts('Send Immediately'));
        
        $this->addFormRule(array('CRM_Mailing_Form_Schedule', 'formRule'));
        
        $this->addButtons(  array(
                                array(  'type'  => 'back',
                                        'name'  => ts('<< Previous')),
                                array(  'type'  => 'next',
                                        'name'  => ts('Done'),
                                        'isDefault' => true),
                                array(  'type'  => 'Cancel',
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
    public static function &formRule(&$params) {
        if ($params['now']) {
            return true;
        }
        if (! CRM_Utils_Rule::qfDate($params['start_date'])) {
            return array('start_date' => ts('Start date is not valid.'));
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
    public function postProcess() {
        $params = array();
        foreach (array( 
                    'template', 'mailing_name',
                    'groups', 'mailings', 'header_id', 'footer_id',
                    'reply_id', 'unsubscribe_id', 'optout_id',
                    'textFile', 'htmlFile', 'subject',
                    'from_name', 'from_email', 'forward_reply', 'track_urls',
                    'track_opens', 'auto_responder'
                ) as $parameter) 
        {
            $params[$parameter] = $this->get($parameter);
        }
        foreach(array('now', 'start_date') as $parameter) {
            $params[$parameter] = $this->controller->exportValue($this->_name,
            $parameter);
        }
        
        $session =& CRM_Core_Session::singleton();
        $params['domain_id'] = $session->get('domainID');
        $params['contact_id'] = $session->get('userID');
        
        /* Build the mailing object */
        require_once 'CRM/Mailing/BAO/Mailing.php';
        CRM_Mailing_BAO_Mailing::create($params);
    }

    /**
     * Display Name of the form
     *
     * @access public
     * @return string
     */
    public function getTitle( ) {
        return ts( 'Schedule / Send Mailing' );
    }

}

?>
