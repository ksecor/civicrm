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
 * Rice Essay
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
 * This class generates form components for the rice essay
 * 
 */
class CRM_Quest_Form_MatchApp_Partner_Rice_RiceApplicant extends CRM_Quest_Form_App
{

    protected $_essays;

    protected $_schools;

    protected $_allchecks;

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        parent::preProcess();

        require_once 'CRM/Quest/BAO/Essay.php';
        $this->_essays = CRM_Quest_BAO_Essay::getFields( 'cm_partner_rice_applicant', $this->_contactID, $this->_contactID );
        $this->_schools = array( 'architecture'     => 'Architecture', 
                                 'engineering'      => 'Engineering',
                                 'humanities'       => 'Humanities',
                                 'music'            => 'Music',
                                 'natural_sciences' => 'Natural Sciences',
                                 'social_sciences'  => 'Social Sciences',
                                 'other'            => 'Other' );
        $this->assign('schools', $this->_schools);

        $this->_allchecks = array( 'contacts' => 'Contacts' );
        $this->_allchecks = array_merge( $this->_schools, $this->_allchecks);
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

       require_once 'CRM/Quest/Partner/DAO/Rice.php';
       $dao =& new CRM_Quest_Partner_DAO_Rice( );
       $dao->contact_id = $this->_contactID;
       if ( $dao->find( true ) ) {
            CRM_Core_DAO::storeValues( $dao , $defaults );
       }

       $defaults['essay'] = array( );
       CRM_Quest_BAO_Essay::setDefaults( $this->_essays, $defaults['essay'] );
       
       foreach ( $this->_allchecks as $name => $title ) {
           if ($defaults[$name]) {
               $value = explode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR , $defaults[$name]);
           }
           if ( is_array( $value ) ) {
               $defaults[$name] = array();
               foreach( $value as $v ) {
                   $defaults[$name][$v] = 1;
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
        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_Partner_DAO_Rice');


        $extra = array('onclick' => "return showHideByValue('rice_academic_id', '1', 'experiences|academics','table-row', 'radio', false);");


        $this->addRadio( 'rice_academic_id', 'Select the academic school you are applying to', 
                         CRM_Core_OptionGroup::values( "rice_academic" ) ,$extra);
        $this->addRule( 'rice_academic_id', 'Please select the academic school you are applying to', 'required');
        
        foreach ( $this->_schools as $name => $title ) {
            $extra1 = array('onclick' => "return show_element(\"$name\");");
            if ( $name != "music" ) {
                $extra1 = null;
            }
            $this->addCheckBox( "$name", $title,
                                CRM_Core_OptionGroup::values( "rice_$name", true ),
                                false, null, null, $extra1 );
            $extra1 = null;
        }

        $extra2 = array('onclick' => "return show_element('contacts');");
        $this->addCheckBox( "contacts", 'What contacts have you had with Rice (check all that apply)?',
                            CRM_Core_OptionGroup::values( "rice_contacts", true ),
                            false, null, 'required' , $extra2 );
        
        $this->addYesNo( 'is_medicine', 'Are you interested in the Rice/Baylor College of Medicine Medical Scholars Program? (You must apply under Interim Decision to compete for this program.)', null, true );
        $this->addYesNo( 'is_rotc', 'Do you plan to apply for the Navy, Army, or Air Force ROTC Scholars Program?', null, true );
        $this->addYesNo( 'is_consent', 'Do you consent to the release of your academic and demographic information to outside groups and foundations that offer scholarships directly to Rice students?', null, true );

        $texts = array('music_other' => '', 
                       'alumni_name' => 'Name:', 
                       'student_name'=> 'Name:', 
                       'coach_name'  => 'Name:', 
                       'faculty_name'=> 'Name:',
                       'other_name'  => ''     );
        foreach ( $texts as $name => $label ) {
            $this->add('text', $name, ts( $label ), $attributes[$name], false);
        }
        $contact_names = array('7' => 'alumni_name', 
                               '8' => 'student_name', 
                               '9' => 'coach_name', 
                               '10'=> 'faculty_name', 
                               '11'=> 'other_name');
        $this->assign('contact_names', $contact_names);

        require_once 'CRM/Core/ShowHideBlocks.php';
        $showHide = new CRM_Core_ShowHideBlocks();
        foreach ( $contact_names as $id => $name ) {
            $showHide->addHide("name_".$id);
        }
        $showHide->addHide("music_name_4");
        $showHide->addToTemplate();

        CRM_Quest_BAO_Essay::buildForm( $this, $this->_essays, false );

        $this->addFormRule(array('CRM_Quest_Form_MatchApp_Partner_Rice_RiceApplicant', 'formRule'));
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
         return ts('Applicant Information');
    }

    public function getRootTitle( ) {
        return ts( 'Rice University' );
    }

   /* Function for validation
     *
     * @param array $params (ref.) an assoc array of name/value pairs
     *
     * @return mixed true or array of errors
     * @access public
     * @static
     */
    public function formRule(&$params) {
        $errors = array( );
        
        if (array_key_exists('rice_academic_id', $params) && ( $params['rice_academic_id'] == 1 ) ) {
            if (empty($params['essay']['experiences'])) {
                    $errors['essay[experiences]'] = ts("What aspirations, experiences, or relationships have motivated you to pursue the study of architecture? is required field");
            }
            if (empty($params['essay']['academics'])) {
                    $errors['essay[academics]'] = ts("Outside of academics, what do you enjoy most or find most challenging? is required field");
            }
        }
        
        $principleAreas = array('architecture','engineering', 'music', 'natural_sciences', 'social_sciences', 'other');
        if (!$params['architecture'] && !$params['engineering'] && !$params['music'] && 
            !$params['natural_sciences'] && !$params['social_sciences'] && !$params['other']) {
            $errors['architecture'] = "please select any principal area(s) of interest";
        }
        
        return empty($errors) ? true : $errors;
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

        foreach ( $this->_allchecks as $name => $title ) {
            $par = CRM_Utils_Array::value( $name, $params, array());
            $params[$name] = implode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR,
                                     array_keys($par));
        }
        require_once 'CRM/Quest/Partner/DAO/Rice.php';
        $dao =& new CRM_Quest_Partner_DAO_Rice( );
        $dao->contact_id = $this->_contactID;
        $dao->find( true );
        $dao->copyValues($params);
        $dao->save( );

        CRM_Quest_BAO_Essay::create( $this->_essays, $params['essay'],
                                     $this->_contactID, $this->_contactID ); 

        parent::postProcess( );
    } 
   
}

?>