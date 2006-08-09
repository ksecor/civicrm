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
 | at http://www.openngo.org/faqs/licensing.html                      |
 +--------------------------------------------------------------------+
*/


/**
 * Amherst Appliction
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Quest/Form/App.php';
require_once 'CRM/Core/OptionGroup.php';

/**
 * This class generates form components for the Princeton application
 * 
 */
class CRM_Quest_Form_MatchApp_Partner_Princeton_PrEnggEssay extends CRM_Quest_Form_App
{
  
   
    public function preProcess()
    {
        
        parent::preProcess();
        
        require_once 'CRM/Quest/BAO/Essay.php';
        $this->_essays = CRM_Quest_BAO_Essay::getFields( 'cm_partner_princeton_engg_essay', $this->_contactID, $this->_contactID );
    }
    

    function setDefaultValues( ) 
    {
        $defaults = array( );
        
        $defaults['essay'] = array( );
        CRM_Quest_BAO_Essay::setDefaults( $this->_essays, $defaults['essay'] );
        return $defaults;
    }




     /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $this->_essays = CRM_Quest_BAO_Essay::getFields( 'cm_partner_princeton_engg_essay', $this->_contactID, $this->_contactID );
        CRM_Quest_BAO_Essay::buildForm( $this, $this->_essays );
        
        $this->addFormRule(array('CRM_Quest_Form_MatchApp_Partner_Princeton_PrEnggEssay', 'formRule'), $this->_contactID);

        parent::buildQuickForm( );
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
    public function formRule(&$params, $contactID) {
        // Engineering essay is only required if princeton_degree is BSE (value = 2)
        $errors = array( );
        $princeton_degree = CRM_Core_DAO::getFieldValue( 'CRM_Quest_Partner_DAO_Princeton',
                                          null,
                                          'princeton_degree',
                                          $contactID );
        if ( $params['essay']['intrested_in_study'] == '' &&  $princeton_degree == 2 ) {
            $errors['essay[intrested_in_study]'] = "This essay is required for applicants interested in the BSE Degree.";
        }
        return empty($errors) ? true : $errors;

    }
    
    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle()
    {
         return ts('Engineering Essay');
    }


    /** 
     * process the form after the input has been submitted and validated 
     * 
     * @access public 
     * @return void 
     */ 
    public function postProcess() {
     
        if ( $this->_action &  CRM_Core_Action::VIEW ) {
            return;
        }
        $params = $this->controller->exportValues( $this->_name );   
        CRM_Quest_BAO_Essay::create( $this->_essays, $params['essay'],
                                     $this->_contactID, $this->_contactID ); 
        
        parent::postProcess( );
    } 

}
?>