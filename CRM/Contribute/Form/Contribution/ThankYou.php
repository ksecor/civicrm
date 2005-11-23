<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
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
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Contribute/Form/Contribution.php';

/**
 * form for thank-you / success page - 3rd step of online contribution process
 */
class CRM_Contribute_Form_Contribution_ThankYou extends CRM_Contribute_Form_Contribution {

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        parent::preProcess( );

        $this->_params = $this->get( 'params' );

        $this->assign( 'thankyou_title', $this->_values['thankyou_title'] );
        $this->assign( 'thankyou_text' , $this->_values['thankyou_text']  );
        $this->assign( 'thankyou_footer' , $this->_values['thankyou_footer']  );

        CRM_Utils_System::setTitle($this->_values['thankyou_title']);
    }

    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm()
    {
        $this->assignToTemplate( );

        CRM_Core_Error::debug( 'p', $this->_params );
        $this->assign( 'trxn_id', $this->_params['trxn_id'] );       
        $this->assign( 'receipt_date', $this->_params['receipt_date'] );       
    }

}

?>
