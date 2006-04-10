<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
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

/**
 * This class generates form components for relationship
 * 
 */
class CRM_Quest_Form_App_Academic extends CRM_Quest_Form_App
{
    static $_honorIds;
    
    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        parent::preProcess( );

        // need tp get honor ID's
        $this->_honorIds = array();

        require_once 'CRM/Quest/DAO/Honor.php';
        $dao = & new CRM_Quest_DAO_Honor();
        $dao->contact_id = $this->_contactID;
        $dao->find();
        $count = 0;
        while ( $dao->fetch() ) {
            $count++;
            $this->_honorIds[$count] = $dao->id;
        }
        $this->set( 'honorIds', $this->_honorIds);
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
        require_once 'CRM/Quest/DAO/Student.php';
        $defaults       = array( );
 
        $session =& CRM_Core_Session::singleton( );

        $dao = & new CRM_Quest_DAO_Student();
        $dao->id = $this->_studentID;
        if ($dao->find(true)) {
            CRM_Core_DAO::storeValues( $dao , $defaults );
        }
        
        //set defaults for honor
        require_once 'CRM/Utils/Date.php';
        require_once 'CRM/Quest/DAO/Honor.php';
        $dao = & new CRM_Quest_DAO_Honor();
        $dao->contact_id = $this->_contactID;
        $dao->find();

        $count = 0;
        while ( $dao->fetch() ) {
            $count++;
            $defaults["description_$count"] = $dao->description;
            $defaults["award_date_$count" ] = CRM_Utils_Date::unformat( $dao->award_date,'-' );
        }

        // Assign show and hide blocks lists to the template for optional Academic Honors blocks
        $this->_showHide =& new CRM_Core_ShowHideBlocks( );
        for ( $i = 2; $i <= 6; $i++ ) {
            if ( CRM_Utils_Array::value( "description_$i", $defaults )) {
                $this->_showHide->addShow( "honor_$i" );
            } else {
                $this->_showHide->addHide( "honor_$i" );
            }
        }
        $this->_showHide->addToTemplate( );
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
        
        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_DAO_Student' );
        
        // name of school
        $this->addSelect('gpa',
                          ts( 'What is your GPA?' ),
                         null,true);
        // $this->addRule('gpa_id' , ts("Please enter GPA"),'required');

        $this->addYesNo( 'is_class_ranking',
                         ts( 'Does your school give class rankings?' ),null,true,array ('onclick' => "return showHideByValue('is_class_ranking', '1', 'class_rank', '', 'radio', false);") );

        $this->addElement('text', 'class_rank',
                          ts( 'If yes, what is your class rank?' ),
                          $attributes['class_rank']  );
        $this->addRule( "class_rank", ts('Number not valid.'), 'integer' );
        
        $this->addElement('text', 'class_num_students',
                          null,
                          $attributes['class_num_students']  );
        $this->addRule( "class_num_students", ts('Number not valid.'), 'integer' );

        $this->addSelect( 'class_rank_percent', ts( 'Percent class rank' ) );

        $this->addElement('textarea', 'gpa_explanation',
                          ts( 'If there are any extenuating circumstances, or details regarding your academic performance that you would like to add or clarify, please do so here' ),
                          $attributes['gpa_explanation'] );

        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_DAO_Honor');
        require_once 'CRM/Core/ShowHideBlocks.php';
        // add up to 6 Honors
        $honor = array( );
        for ( $i = 1; $i <= 6; $i++ ) {
            $this->addElement('text', 'description_' . $i,
                              ts( 'Honors' ),
                              $attributes['description'] );

            $this->addElement('date', 'award_date_' . $i,
                              null,
                              CRM_Core_SelectValues::date( 'custom', 5, 1, "M\001Y" ) );
            $honor[$i] = CRM_Core_ShowHideBlocks::links( $this,"honor_$i",
                                                              ts('add another honor'),
                                                              ts('hide this honor'),
                                                              false );
        }
        // Assign showHide links to tpl
        $this->assign( 'honor', $honor );

        parent::buildQuickForm( );
    }

    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return void
     */
    public function postProcess() 
    {
        if ($this->_action !=  CRM_Core_Action::VIEW ) {
            $params = $this->controller->exportValues( $this->_name );
            
            $ids = array( 'id'         => $this->_studentID,
                          'contact_id' => $this->_contactID );
            
            require_once 'CRM/Quest/BAO/Student.php';
            $student = CRM_Quest_BAO_Student::create( $params, $ids);
            
            // to add honour records 
            require_once 'CRM/Utils/Date.php';
            $honors = array();
            
            for ( $i = 1; $i <= 6; $i++ ) {
                if ( ! empty( $params[ "description_$i" ] ) ) {
                    $honors[$i]['description'] = $params[ "description_$i" ];
                    if ( ! CRM_Utils_System::isNull( $params[ "award_date_$i" ]) ) {
                        $honors[$i]['award_date'] = CRM_Utils_Date::format( $params[ "award_date_$i" ] );
                    } else { 
                        $honors[$i]['award_date'] = null;
                    }
                }
            }
            
            require_once 'CRM/Quest/BAO/Honor.php';
          
            // $this->_honorIds = $this->get( 'honorIds');
            
            // delete honor 
            require_once 'CRM/Quest/DAO/Honor.php';
            if ( is_array($this->_honorIds ) ) {
                foreach ( $this->_honorIds as $honorID ) {
                    $dao     = & new CRM_Quest_DAO_Honor();
                    $dao->id = $honorID;
                    $dao->delete();
                }
            }

            $this->_honorIds = null;

            foreach ( $honors as $key => $honor ) {
                $ids = array();
                $honor['contact_id']     = $this->_contactID;
                $newHonor                = CRM_Quest_BAO_Honor::create( $honor,$ids );
                $this->_honorIds[$key]   = $newHonor->id;
            }
            $this->set( 'honorIds', $this->_honorIds);
        }
        parent::postProcess( );
    }//end of function

    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle()
    {
        return ts('Academic Information');
    }
    
}

?>
