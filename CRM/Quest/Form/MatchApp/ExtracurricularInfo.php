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
 * Personal Information Form Page
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Quest/Form/App.php';
require_once 'CRM/Core/OptionGroup.php';
require_once 'CRM/Quest/BAO/Essay.php';

/**
 * This class generates form components for relationship
 * 
 */
class CRM_Quest_Form_MatchApp_ExtracurricularInfo extends CRM_Quest_Form_App
{

    protected $_essays;

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        parent::preProcess();

        $this->_grouping = 'cm_extracurricular_info';
        $this->_essays = CRM_Quest_BAO_Essay::getFields( $this->_grouping, $this->_contactID, $this->_contactID );
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

        require_once 'CRM/Quest/BAO/Extracurricular.php';
        CRM_Quest_BAO_Extracurricular::setDefaults( $this->_contactID,
                                                    'Extracurricular',
                                                    $defaults );
        
        $studentFields   = array( 'varsity_sports' => 'varsity_sports_list', 'arts' => 'arts_list' );
        //$studentCheckBox = array( 'varsity_sports', 'arts' );
        $dao = & new CRM_Quest_DAO_Student();
        $dao->contact_id = $this->_contactID;
        if ( $dao->find( true ) ) {
            foreach ( $studentFields as $chkBox => $stu ) {
                if ( $dao->$stu ) {
                    $defaults[$stu] = $dao->$stu;
                    $defaults[$chkBox] = 1;
                }
            }
        }    
        CRM_Quest_BAO_Essay::setDefaults( $this->_essays, $defaults["essay"] );

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
        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_DAO_Person');

        require_once 'CRM/Quest/BAO/Extracurricular.php';
        CRM_Quest_BAO_Extracurricular::buildForm( $this, 'Extracurricular' );
        
        CRM_Quest_BAO_Essay::buildForm( $this, $this->_essays );
        
        // $this->addElement( 'textarea', "hobbies",
//                            ts('We encourage you to reply to this question in sentence form, rather than as a list, if you feel this would allow you to better express your interests.') ,"cols=60 rows=5");


        $extra1 = array ('onclick' => "return showHideByValue('varsity_sports', '1', 'varsity_sports_list', '', 'radio', false);");
        $this->addElement('checkbox', 'varsity_sports',ts( 'Varsity Sports (list):' ) , null, $extra1);
        $this->addElement('text', 'varsity_sports_list' );

        $extra2 = array ('onclick' => "return showHideByValue('arts', '1', 'arts_list', '', 'radio', false);");
        $this->addElement( 'checkbox','arts',ts('Arts (music, dance/theatre, visual, etc) (list):'), null, $extra2);
        $this->addElement('text', 'arts_list' );
 
        $this->addFormRule( array( 'CRM_Quest_Form_MatchApp_ExtracurricularInfo',
                                   'formRule' ),
                            'Extracurricular' );

        parent::buildQuickForm();
    }
    //end of function
    
    /**
     * Function for validation
     *
     * @param array $params (ref.) an assoc array of name/value pairs
     *
     * @return mixed true or array of errors
     * @access public
     * @static
     */
    public function formRule( &$params, $options )
    {
        return CRM_Quest_BAO_Extracurricular::formRule( $params, "Extracurricular" );
    } 

    /** 
     * process the form after the input has been submitted and validated 
     * 
     * @access public 
     * @return void 
     */ 
    public function postProcess()  
    {
        if ( ! ( $this->_action &  CRM_Core_Action::VIEW ) ) {
            require_once 'CRM/Quest/BAO/Extracurricular.php';
            $params = $this->controller->exportValues( $this->_name );
            
            CRM_Quest_BAO_Extracurricular::process( $this->_contactID, 'Extracurricular', $params );
            
            CRM_Quest_BAO_Essay::create( $this->_essays, $params["essay"], $this->_contactID, $this->_contactID );

            $ids = array( 'id'         => $this->_studentID,
                          'contact_id' => $this->_contactID );

            require_once "CRM/Quest/BAO/Student.php";
            CRM_Quest_BAO_Student::create( $params, $ids );
        }
        
    }
    
    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle()
    {
        return $this->_title ? $this->_title : ts('Extracurricular Information');
    }

    public function getRootTitle( ) {
       return "Extra Curricular Information: ";
    }

}

?>
