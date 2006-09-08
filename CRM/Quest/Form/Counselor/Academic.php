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
class CRM_Quest_Form_Counselor_Academic extends CRM_Quest_Form_Recommender
{
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
        require_once 'CRM/Quest/DAO/Academic.php';
        $dao =&new CRM_Quest_DAO_Academic();
        $dao->target_contact_id = $this->_studentContactID;
        $dao->source_contact_id = $this->_recommenderID;
        $ids = array();
        if ( $dao->find(true) ) {
            CRM_Core_DAO::storeValues( $dao, $defaults);
        }
        $defaults['rank_date_low']  = CRM_Utils_Date::unformat( $defaults['rank_date_low'] , '-' );
        $defaults['rank_date_high'] = CRM_Utils_Date::unformat( $defaults['rank_date_high'] , '-' );
        
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
        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_DAO_Academic');

        for ( $i = 1; $i <= 6; $i++ ) {
            $this->add( 'text',
                        "ap_class_$i", null, $attributes['ap_class_1'] );
        }

        $this->add( 'text',
                    'gpa_unweighted',
                    ts( 'The student has a cumulative <strong>unweighted</strong> GPA of:' ),
                    $attributes['gpa_unweighted'],
                    true);
        $this->addRule( 'gpa_unweighted', ts( 'GPA should be a number between 0 and 4 (0.00 - 4.00)' ), 'money' );
                        
        $this->add( 'text',
                    'gpa_weighted',
                    ts( 'The student has a cumulative <strong>weighted</strong> GPA of:' ),
                    $attributes['gpa_weighted'],
                    true);
        $this->addRule( 'gpa_weighted', ts( 'GPA should be a number between 0 and 5 (0.00 - 5.00)' ), 'money' );
        
        $this->addSelect( 'gpa_includes',
                          ts( 'The student\'s GPA includes' ) );
                                                                    
        $this->add( 'text',
                    'gpa_weighted_max',
                    ts( 'The highest weighted GPA in the class is:' ),
                    $attributes['gpa_weighted_max'],
                    true);
        $this->addRule( 'gpa_weighted_max', ts( 'GPA should be a number between 0 and 5 (0.00 - 5.00)' ), 'money' );

        foreach ( array( 'a', 'b', 'c', 'd' ) as $alphabet ) {
            $this->add( 'text',
                        "numeric_grade_{$alphabet}",
                        null,
                        $attributes['numeric_grade_a']);
            $this->addRule( "numeric_grade_{$alphabet}", ts( 'GPA should be a number between 0 and 5 (0.00 - 5.00)' ), 'money' );
        }

        $this->addElement('text', 'unweighted_rank',
                          ts( 'The cumulative <strong>unweighted</strong> rank of the student is' ),
                          $attributes['unweighted_rank']);
        $this->addRule( "unweighted_rank", ts('Number not valid.'), 'integer' );
        
        $this->addElement('text', 'class_num_students',
                          ts( 'out of'),
                          $attributes['class_num_students']);
        $this->addRule( "class_num_students", ts('Number not valid.'), 'integer' );
        
        $this->addElement('date', 'rank_date_low', null,
                          CRM_Core_SelectValues::date( 'custom', 5, 1, "M\001Y" ) );
        $this->addElement('date', 'rank_date_high', null,
                          CRM_Core_SelectValues::date( 'custom', 5, 1, "M\001Y" ) );

        $extra = array( 'onchange' => "return showHideByValue('term_type_id','4','term_type_other','block','select',false);");
        $this->addSelectOther('term_type',
                              ts('This cumulative ranking is based on what type of term?'),
                              array('' => '- select -' ) + CRM_Core_OptionGroup::values( 'term_type' ),
                              null,
                              true,
                              $extra);
        
        $this->addElement('text', 'share_ranking',
                          ts('How many students share this cumulative ranking?'),
                          $attributes['share_ranking'] );
        $this->addRule( "share_ranking", ts('Number not valid.'), 'integer' );

        $this->addSelect( 'course_choice',
                          ts( 'Which of the following describes the student\'s choices of academic courses and in comparison to other college prep students at your school' ),
                          null,
                          true);

        $this->addElement('text',
                          'college_four_year',
                          ts( 'Of the student\'s graduating class, what percentage plan to attend a four-year college/university?' ),
                          $attributes['college_four_year']);
        $this->addRule( "college_four_year", ts('Number not valid.'), 'integer' ); 

        $this->addElement('text',
                          'college_two_year',
                          ts( 'Of the student\'s graduating class, what percentage plan to attend a two-year college?' ),
                          $attributes['college_two_year']);
        $this->addRule( "college_two_year", ts('Number not valid.'), 'integer' ); 

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
            $params['rank_date_low']  = CRM_Utils_Date::format($params['rank_date_low']);
            $params['rank_date_high'] = CRM_Utils_Date::format($params['rank_date_high']);
            
            require_once 'CRM/Quest/DAO/Academic.php';
            $dao =&new CRM_Quest_DAO_Academic();
            $dao->target_contact_id = $this->_studentContactID;
            $dao->source_contact_id = $this->_recommenderID;
            $ids = array();
            if ( $dao->find(true) ) {
                $ids["id"] = $dao->id;
            }

            require_once "CRM/Quest/BAO/Academic.php";
            CRM_Quest_BAO_Academic::create($params ,$ids );
            
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
        return ts('Academic Record');
    }
}

?>
