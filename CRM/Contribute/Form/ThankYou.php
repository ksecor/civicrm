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

require_once 'CRM/Core/Form.php';

/**
 * form to process actions on the group aspect of Custom Data
 */
class CRM_Contribute_Form_ThankYou extends CRM_Core_Form {

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        $this->_contributeMode = $this->get( 'contributeMode' );
        $this->assign( 'contributeMode', $this->_contributeMode );

        $this->_params = $this->get( 'transactionParams' );

        // also retrieve the contribution object
        // get all the values from the dao object 
        $params = array('id' => $this->get( 'id' ) );  
        $this->_values = array( );                   
        CRM_Core_DAO::commonRetrieve( 'CRM_Contribute_DAO_ContributionPage', $params, $this->_values ); 

        $this->assign( 'thankyou_title', $this->_values['thankyou_title'] );
        $this->assign( 'thankyou_text' , $this->_values['thankyou_text']  );
    }

    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm()
    {
        CRM_Contribute_Form_Confirm::assignToTemplate( $this, $this->_params );

        $this->assign( 'trxn_id', $this->_params['trxn_id'] );
        
        $this->addButtons(array(
                                array ( 'type'      => 'cancel',
                                        'name'      => ts('Done'),
                                        'isDefault' => true ),
                                )
                          );
    }

    /**
     * This function sets the default values for the form. Note that in edit/view mode
     * the default values are retrieved from the database
     *
     * @access public
     * @return void
     */
    function setDefaultValues()
    {
        $defaults = array();
        return $defaults;
    }

    /**
     * Process the form
     *
     * @return void
     * @access public
     */
    public function postProcess()
    {
    }
}

?>
