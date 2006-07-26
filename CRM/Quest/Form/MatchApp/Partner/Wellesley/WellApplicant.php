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
 * Columbia Applicant
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
 * This class generates form components for the columbia applicant
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
                            CRM_Core_OptionGroup::values( 'interdepartmental_major', true ),
                            false, null );


        $this->addCheckBox( 'preprofessional_interest', ts('Preprofessional Interest'),
                            CRM_Core_OptionGroup::values( 'preprofessional_interest', true ),
                            true, null );

       
       
        
        parent::buildQuickForm( );
                
    }//end of function

    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle()
    {
         return ts('Wellesley College');
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

//         require_once 'CRM/Quest/Partner/DAO/Amherst.php';
//         $dao =& new CRM_Quest_Partner_DAO_Amherst( );
//         $dao->contact_id = $this->_contactID;
//         $dao->find( true );

//         foreach ( $this->_fields as $name => $titles ) {
//             $cond = "is_{$name}";
//             $dao->$cond = CRM_Utils_Array::value( $cond, $params, false );
//             $dao->$name = $params[$name];
//         }

//         $dao->save( );

        parent::postProcess( );
    } 
   
}

?>