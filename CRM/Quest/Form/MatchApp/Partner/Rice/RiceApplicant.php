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

/**
 * This class generates form components for the rice essay
 * 
 */
class CRM_Quest_Form_MatchApp_Partner_Rice_RiceApplicant extends CRM_Quest_Form_App
{

    protected $_essays;

    protected $_schools;

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        parent::preProcess();

        $this->_essays = CRM_Quest_BAO_Essay::getFields( 'cm_partner_rice_applicant', $this->_contactID, $this->_contactID );

        $this->_schools = array( 'architecture'     => 'Architecture', 
                                 'engineering'      => 'Engineering',
                                 'humanities'       => 'Humanities',
                                 'music'            => 'Music',
                                 'natural_sciences' => 'Natural Sciences',
                                 'social_sciences'  => 'Social Sciences',
                                 'other'            => 'Other' );
        $this->assign('schools', $this->_schools);
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
        }

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
        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_Partner_DAO_Rice');

        $this->addRadio( 'rice_academic', 'Select the academic school you are applying to', 
                         CRM_Core_OptionGroup::values( "rice_academic" ) );

        foreach ( $this->_schools as $name => $title ) {
            $this->addCheckBox( "rice_$name", $title,
                                CRM_Core_OptionGroup::values( "rice_$name", true ),
                                false, null );
        }

        $this->addCheckBox( "rice_contacts", 'What contacts have you had with Rice (check all that apply)?',
                            CRM_Core_OptionGroup::values( "rice_contacts", true ),
                            false, null );
        
        $this->addYesNo( 'is_medicine', 'Are you interested in the Rice/Baylor College of Medicine Medical Scholars Program? (You must apply under Interim Decision to compete for this program.)', null, true );
        $this->addYesNo( 'is_rotc', 'Do you plan to apply for the Navy, Army, or Air Force ROTC Scholars Program?', null, true );
        $this->addYesNo( 'is_consent', 'Do you consent to the release of your academic and demographic information to outside groups and foundations that offer scholarships directly to Rice students?', null, true );

        $texts = array('music_other' => '', 
                       'alumni_name' => 'Name:', 
                       'student_name'=> 'Name:', 
                       'coach_name'  => 'Name:', 
                       'faculty_name'=> 'Name:',
                       'other'       => ''     );
        foreach ( $texts as $name => $label ) {
            $this->add('text', $name, ts( $label ), $attributes[$name], false);
        }
        $addTextsToChks = array('7' => 'alumni_name', 
                                '8' => 'student_name', 
                                '9' => 'coach_name', 
                                '10'=> 'faculty_name', 
                                '11'=> 'other');
        $this->assign('addTexts', $addTextsToChks);

        CRM_Quest_BAO_Essay::buildForm( $this, $this->_essays );

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

        require_once 'CRM/Quest/Partner/DAO/Rice.php';
        $dao =& new CRM_Quest_Partner_DAO_Rice( );
        $dao->contact_id = $this->_contactID;
        $dao->find( true );
        $dao->learn = $params['learn'];
        $dao->save( );

        CRM_Quest_BAO_Essay::create( $this->_essays, $params['essay'],
                                     $this->_contactID, $this->_contactID ); 

        parent::postProcess( );
    } 
   
}

?>