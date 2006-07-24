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
        $this->_grouping = 'cm_teacher_eval';
        $this->_essays = CRM_Quest_BAO_Essay::getFields( 'cm_teacher_eval', $this->_contactID, $this->_contactID );
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
        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_DAO_TeacherEvaluation');
        for($i=1;$i<=3;$i++) {
        $this->add('text', 'word_'.$i, ts( 'What three words would you use to describe this applicant?' ), $attributes['word_'.$i], true );
        }
       
        $this->addCheckBox( 'academic_success',
                            ts( 'Please indicate which of the following factors have most influenced this students academic success, and provide a brief explanation.' ),
                            CRM_Core_OptionGroup::values( 'academic_success', true ),
                            false, null,true );
        //  $this->addElement('textarea','academic_success_explain');


         $extra1 = array('onchange' => "return showHideByValue('obstacle', '1', 'obstacle_explain','table-row', 'radio', false);");
         $this->addYesNo( 'obstacle',
                          ts( 'Has this applicant faced any special obstacles that make his/her other accomplishments all the more remarkable?' ),null,true ,$extra1);

         $extra2 = array('onchange' => "return showHideByValue('interfere', '1', 'interfere_explain','table-row', 'radio', false);");
         $this->addYesNo( 'interfere',
                          ts( 'Are there any factors that might interfere wit the candidate academic performance?' ),null,true ,$extra2);


        
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

            CRM_Quest_BAO_Essay::create( $this->_essays, $params['essay'],
                                         0, 0 );
       }

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
