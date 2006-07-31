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
class CRM_Quest_Form_Counselor_Evaluation extends CRM_Quest_Form_Recommender
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
        $this->_essays = CRM_Quest_BAO_Essay::getFields( 'cm_counselor_eval', $this->_recommenderID, $this->_studentContactID );
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

        $radFields = array('is_context'             => 'explain_context',
                           'is_problem_behavior'    => 'problem_explain',
                           'is_disciplinary_action' => 'discipline_explain');
        foreach ( $radFields as $rad => $field ) {
            if ( $defaults['essay'][$field] ) {
                $defaults[$rad] = 1;
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
        // broader context
        $extra1 = array('onchange' => "return showHideByValue('is_context', '1', 'explain_context_row','table-row', 'radio', false);");
        $this->addYesNo( 'is_context',
                         ts( 'Is there a broader context in which we should consider the candidate\'s performance and involvements?' ),null,true ,$extra1);
        
        $extra2 = array('onchange' => "return showHideByValue('is_problem_behavior', '1', 'explain_problem_row','table-row', 'radio', false);");
        $this->addYesNo( 'is_problem_behavior',
                         ts( 'Are there any observed problematic behaviors, e.g. discipline problems, violence, intolerance or anti-social behavior in interactions with peers or teachers, perhaps separable from academic performance, which should be explored further by a college considering this student?' ),null,true ,$extra2);

        $extra3 = array('onchange' => "return showHideByValue('is_disciplinary_action', '1', 'explain_discipline_row','table-row', 'radio', false);");
        $this->addYesNo( 'is_disciplinary_action',
                         ts( 'To the best of your knowledge, has the student ever violated an Honor Code or been suspended or subjected to any school-related or legal disciplinary action?' ),null,true ,$extra3);

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
        if ( $this->_action &  CRM_Core_Action::VIEW ) {
            return;
        }
        
        $params = $this->controller->exportValues( $this->_name );
        CRM_Quest_BAO_Essay::create( $this->_essays, $params['essay'], $this->_recommenderID, $this->_studentContactID );

        require_once 'CRM/Quest/DAO/CounselorEvaluation.php';
        $dao =& new CRM_Quest_DAO_CounselorEvaluation();
        $params['target_contact_id'] = $dao->target_contact_id = $this->_studentContactID;
        $params['source_contact_id'] = $dao->source_contact_id = $this->_recommenderID;
        $ids = array();
        if ( $dao->find(true) ) {
            $ids["id"] = $dao->id;
        }
        
        require_once "CRM/Quest/BAO/CounselorEvaluation.php";
        CRM_Quest_BAO_CounselorEvaluation::create($params ,$ids );
        
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
