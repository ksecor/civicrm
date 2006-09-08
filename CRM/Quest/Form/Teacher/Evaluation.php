<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
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
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Quest/Form/Recommender.php';
require_once 'CRM/Core/OptionGroup.php';


/**
 * This class generates form components for relationship
 * 
 */
class CRM_Quest_Form_Teacher_Evaluation extends CRM_Quest_Form_Recommender
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
        $this->_grouping = 'cm_teacher_eval';
        $this->_essays = CRM_Quest_BAO_Essay::getFields( 'cm_teacher_eval', $this->_recommenderID, $this->_studentContactID );
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
        $defaults['essay'] = array( );
        
        require_once "CRM/Quest/BAO/Essay.php";
        CRM_Quest_BAO_Essay::setDefaults( $this->_essays, $defaults['essay'] );

        $dao =& new CRM_Quest_DAO_TeacherEvaluation();
        $dao->target_contact_id = $this->_studentContactID;
        $dao->source_contact_id = $this->_recommenderID;
        if ( $dao->find(true) ) {
            CRM_Core_DAO::storeValues( $dao , $defaults );
        }
        if ($defaults['success_factor']) {
            $value = explode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR , $defaults['success_factor']);
        }

        $defaults['success_factor'] = array();
        if ( is_array( $value ) ) {
            foreach( $value as $v ) {
                $defaults['success_factor'][$v] = 1;
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
        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_DAO_TeacherEvaluation');
        
        for($i=1;$i<=3;$i++) {
            $this->add('text', 'word_'.$i, ts( 'What three words would you use to describe this applicant?' ), $attributes['word_'.$i], true );
        }
       
        $this->addCheckBox( 'success_factor', ts( 'Please indicate which of the following factors have most influenced this students academic success, and provide a brief explanation. (150 words max)' ), CRM_Core_OptionGroup::values( 'success_factor', true ),
                            false, null,true );

         $extra1 = array('onclick' => "return showHideByValue('is_obstacles', '1', 'obstacle_explain','table-row', 'radio', false);");
         $this->addYesNo( 'is_obstacles', ts( 'Has this applicant faced any special obstacles that make his/her other accomplishments all the more remarkable?' ),null,true ,$extra1);

         $extra2 = array('onclick' => "return showHideByValue('is_interfere', '1', 'interfere_explain','table-row', 'radio', false);");
         $this->addYesNo( 'is_interfere', ts( 'Are there any factors that might interfere with the candidate\'s academic performance?' ),null,true ,$extra2);
        
        require_once "CRM/Quest/BAO/Essay.php";
        CRM_Quest_BAO_Essay::buildForm( $this, $this->_essays );
        
        $this->addFormRule(array('CRM_Quest_Form_Teacher_Evaluation', 'formRule'));
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
    public function formRule(&$params) {
        $errors = array( );

        $radEssay = array('is_obstacles' => array( 'name'  => 'obstacle_explain',
                                                   'title' => 'obstacles'),
                          'is_interfere' => array( 'name'  => 'interfere_explain',
                                                   'title' => 'interfere factors'));
        foreach ( $radEssay as $rad => $essay ) {
            if ( $params[$rad] ) {
                if ( !$params['essay'][$essay['name']] ) {
                    $errors['essay['.$essay['name'].']'] = 'Briefly explain these '.$essay['title'];
                }
            }
        }
        return empty($errors) ? true : $errors;
    }
    
    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return void
     */
    public function postProcess() 
    {
        if ( $this->_action &  CRM_Core_Action::VIEW ) {
            return;
        }

        $params = $this->controller->exportValues( $this->_name );
        CRM_Quest_BAO_Essay::create( $this->_essays, $params['essay'], $this->_recommenderID, $this->_studentContactID );

        if ( $params['success_factor'] ) {
            $params['success_factor'] = implode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR,
                                                array_keys($params['success_factor']));
        }

        $dao =& new CRM_Quest_DAO_TeacherEvaluation();
        $params['target_contact_id'] = $dao->target_contact_id = $this->_studentContactID;
        $params['source_contact_id'] = $dao->source_contact_id = $this->_recommenderID;
        $ids = array();
        if ( $dao->find(true) ) {
            $ids["id"] = $dao->id;
        }
        
        require_once "CRM/Quest/BAO/TeacherEvaluation.php";
        CRM_Quest_BAO_TeacherEvaluation::create($params ,$ids );

        parent::postProcess( );
    } //end of function

    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle()
    {
        return ts('Evaluation');
    }
}

?>
