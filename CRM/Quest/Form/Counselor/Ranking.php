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

require_once 'CRM/Quest/Form/Recommender.php';
require_once 'CRM/Core/OptionGroup.php';


/**
 * This class generates form components for relationship
 * 
 */
class CRM_Quest_Form_Counselor_Ranking extends CRM_Quest_Form_Recommender
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
       
        require_once "CRM/Quest/BAO/Essay.php";
        $this->_essays = CRM_Quest_BAO_Essay::getFields( 'cm_counselor_ranking',$this->_recommenderID,
                                         $this->_studentContactID);
      
        
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
        require_once 'CRM/Quest/DAO/StudentRanking.php';
        $dao =&new CRM_Quest_DAO_StudentRanking();
        $dao->target_contact_id = $this->_studentContactID;
        $dao->source_contact_id = $this->_recommenderID;
        $ids = array();
        if ( $dao->find(true) ) {
            CRM_Core_DAO::storeValues( $dao, $defaults);
        }
        if ( $defaults['counselor_basis'] ) {
            $value = explode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR , $defaults['counselor_basis'] );
            $defaults['counselor_basis'] = array();
            if ( is_array( $value ) ) {
                foreach( $value as $v ) {
                    $defaults['counselor_basis'][$v] = 1;
                }
            }
        }
        $defaults['essay'] = array( );
        require_once "CRM/Quest/BAO/Essay.php";
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
        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_DAO_StudentRanking');

        $this->add( 'text',
                    'counselor_years',
                    ts( 'How long have you known this student?' ),
                    $attributes['counselor_years'],
                    true );

        $this->add( 'text',
                    'counselor_capacity',
                    ts( 'In what capacity?' ),
                    $attributes['counselor_capacity'],
                    true );

        $this->add( 'text',
                    'counselor_meet',
                    ts( 'How often do you meet with this student?' ),
                    $attributes['counselor_meet'],
                    true );

        $this->addCheckBox( 'counselor_basis',
                            ts( 'This evaluation is based on (check all that apply):' ),
                            CRM_Core_OptionGroup::values( 'counselor_basis', true ),
                            true, '<br/>',true
                            );
       
        $radioBoxes = array(
                            'Leadership potential'             => 'leadership_id',
                            'Intellectual Curiosity'           => 'intellectual_id',
                            'Atttitude toward challenges'      => 'challenge_id',
                            'Emotional Maturity'               => 'maturity_id',
                            'Work ethic'                       => 'work_ethic_id',
                            'Originality of thought'           => 'originality_id',
                            'Sense of humor'                   => 'humor_id',
                            'Energy'                           => 'energy_id',
                            'Respect for differences'          => 'respect_differences_id',
                            'Respect accorded by faculty'      => 'respect_faculty_id',
                            'Respect accorded by peers'        => 'respect_peers_id',
                            'Academically'                     => 'compare_academic_id',
                            'Extracurricular accomplishment'   => 'compare_extracurricular_id',
                            'Personal qualities and character' => 'compare_personal_id',
                            'Overall'                          => 'compare_overall_id',
                            );
        
        $optionValues = CRM_Core_OptionGroup::values( 'recommender_ranking' );
        
        // delete all the labels since the template takes care of them
        foreach ( $optionValues as $key => $val ) {
            $optionValues[$key] = null;
        }

        foreach( $radioBoxes as $label => $name ) {
            $this->addRadio( $name, $label, $optionValues, null, '</td><td>' );
        }

        $this->addSelect( 'recommend_student',
                          ts( 'I recommend this student' ),
                          null,
                          true);

        require_once "CRM/Quest/BAO/Essay.php";
        CRM_Quest_BAO_Essay::buildForm( $this, $this->_essays );
        
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
        if ( ! ( $this->_action &  CRM_Core_Action::VIEW ) ) {
            $params = $this->controller->exportValues( $this->_name );
            $params["target_contact_id"] =  $this->_studentContactID;
            $params["source_contact_id"] =  $this->_recommenderID;
            if ( $params['counselor_basis'] ) {
                $params['counselor_basis']       = implode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR,array_keys($params['counselor_basis']));
            }
            
            require_once 'CRM/Quest/DAO/StudentRanking.php';
            $dao =&new CRM_Quest_DAO_StudentRanking();
            $dao->target_contact_id = $this->_studentContactID;
            $dao->source_contact_id = $this->_recommenderID;
            $ids = array();
            if ( $dao->find(true) ) {
                $ids["id"] = $dao->id;
            }

            require_once "CRM/Quest/BAO/StudentRanking.php";
            CRM_Quest_BAO_StudentRanking::create($params ,$ids );
            
            CRM_Quest_BAO_Essay::create( $this->_essays, $params['essay'],$this->_recommenderID,
                                         $this->_studentContactID);


            

       }

        // parent::postProcess( );
     
    } //end of function

    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle()
    {
        return ts('Student Ranking');
    }
}

?>
