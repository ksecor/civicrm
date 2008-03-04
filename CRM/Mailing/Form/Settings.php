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
        $mailingID = $this->get("mid");
        $count     = $this->get('count');
        $this->assign('count',$count);
        
        $defaults = array( );
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
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );
        $this->setDefaults($defaults);
    }
    
    public function postProcess() 
    {
        $params = $ids       = array( );
        
        $uploadParams        = array('reply_id', 'unsubscribe_id', 'optout_id', 'resubscribe_id');
        $uploadParamsBoolean = array('forward_replies', 'url_tracking', 'open_tracking', 'auto_responder');
        
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


