<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                 |
 +--------------------------------------------------------------------+
*/


/**
 * Columbia Applicant
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Quest/Form/App.php';
require_once 'CRM/Core/OptionGroup.php';

/**
 * This class generates form components for the Wellesley applicant
 * 
 */
class CRM_Quest_Form_MatchApp_Partner_Wellesley_WellApplicant extends CRM_Quest_Form_App
{
    
    protected $_fields;

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        parent::preProcess();
        $this->_fields = array ("departmental_majors" ,"interdepartmental_major","preprofessional_interest" ) ;
       
    }
    
    /**
     * This function sets the default values for the form. Relationship that in edit/view action
     * the default values are retrieved from the database
     * 
     * @access public
     * @return void
     */
    function setDefaultValues( ) 
    {
        $defaults = array( );
        require_once 'CRM/Quest/Partner/DAO/Wellesley.php';
        $dao =& new CRM_Quest_Partner_DAO_Wellesley( );
        $dao->contact_id = $this->_contactID;
        if ( $dao->find( true ) ) {
            CRM_Core_DAO::storeValues( $dao, $defaults);
            foreach ( $this->_fields as $name ) {
                if ( $defaults[$name] ) {
                    $value = explode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR,$defaults[$name]);
                    if ( is_array( $value ) ) {
                        $defaults[$name] = array();
                        foreach( $value as $v ) {
                            $defaults[$name][$v] = 1;
                        }
                    }
                }
            }
        }
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
        $this->addElement( 'checkbox','undecided',ts('Undecided.'));
        
        $this->addCheckBox( 'departmental_majors',ts('Departmental Majors '),
                            CRM_Core_OptionGroup::values( 'departmental_majors', true ),
                            false,null );
        
        $this->addCheckBox( 'interdepartmental_major',ts('Interdepartmental Major'),
                            CRM_Core_OptionGroup::values('interdepartmental_major', true ),
                            false, null );
        
        $js_pi = array('onclick' => "return showHideByValue('preprofessional_interest[3]', '1', 'preprofessional_interest_other', '', 'radio', false);");
        $this->addCheckBox( 'preprofessional_interest', ts('Preprofessional Interest'),
                            CRM_Core_OptionGroup::values('preprofessional_interest', true ),
                            true, null, null, $js_pi );
        $this->addFormRule(array('CRM_Quest_Form_MatchApp_Partner_Wellesley_WellApplicant', 'formRule'));
        parent::buildQuickForm( );
                
    }//end of function
    
    /**
     * Function for validation
     *
     * @param array $params (ref.) an assoc array of name/value pairs
     *
     * @return mixed true or array of errors
     * @access public
     * @static
     */
    public function formRule(&$params) {
        $errors = array( );
                
        if ( count($params['departmental_majors']) > 2 ) {
            $errors['departmental_majors'] = 'Only 2 academic interests can be selected from Departmental Majors';
        }
        
        if ( count($params['interdepartmental_major']) > 2 ) {
            $errors['interdepartmental_major'] = 'Only 2 academic interests can be selected from Interdepartmental Majors';
        }
        
        if ( count($params['preprofessional_interest']) > 2 ) {
            $errors['preprofessional_interest'] = 'Only 2 academic interests can be selected from Preprofessional Interest';
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
         return ts('Applicant Information');
    }

    public function getRootTitle( ) {
        return ts( 'Wellesley College' );
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
        $params['undecided'] = CRM_Utils_Array::value( $params['undecided'] , $params, false);
        foreach ( $this->_fields as $name ) {
            $par = CRM_Utils_Array::value( $name, $params, array());
            $params[$name] = implode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR,array_keys($par));
        }
        require_once 'CRM/Quest/Partner/DAO/Wellesley.php';
        $dao =& new CRM_Quest_Partner_DAO_Wellesley( );
        $dao->contact_id = $this->_contactID;
        $dao->find( true );
        $dao->copyValues($params);
        $dao->save( );

        parent::postProcess( );
    } 
   
}

?>