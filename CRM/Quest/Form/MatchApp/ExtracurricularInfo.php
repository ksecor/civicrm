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

/**
 * This class generates form components for relationship
 * 
 */
class CRM_Quest_Form_MatchApp_ExtracurricularInfo extends CRM_Quest_Form_App
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
        
        for($i=1;$i<=7;$i++) {

            $this->addElement('text', "activity_$i", ts('Activity'), null );
            for ( $j = 1; $j <= 5; $j++ ) {
                $this->addElement('checkbox', "grade_level_{$i}_{$j}", null, null );
            }
            for ( $j = 1; $j <= 2; $j++ ) {
                $this->addElement('text', "time_spent_{$i}_{$j}", ts('Approximate time spent'), null );
            }
            $this->addElement('text', "positions_$i", ts('Positions held, honors won,or letters earned'), null );
        }
        $this->addElement( 'textarea', "meaningful_commitment",
                           ts('Describe which single activity/interest listed above represents your most meaningful commitment and why?') );
        $this->addElement( 'textarea', "past_activities",
                           ts('List and describe your activities, including jobs, during the past two summers:') );
       
        $this->addElement( 'textarea', "hobbies",
                           ts('We encourage you to reply to this question in sentence form, rather than as a list, if you feel this would allow you to better express your interests.') );

        $this->addElement('checkbox', 'varsity_sports',ts( 'Varsity Sports' ));
        $this->addElement('text', 'varsity_sports_list' );
        $this->addElement( 'checkbox','arts',ts('Arts') );
        $this->addElement('text', 'arts_list' );
 
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
    public function formRule(&$params)
    {
        
    } 

    /** 
     * process the form after the input has been submitted and validated 
     * 
     * @access public 
     * @return void 
     */ 
    public function postProcess()  
    {
     
    }
    
    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle()
    {
        return $this->_title ? $this->_title : ts('Extra Curricular Information');
    }

    public function getRootTitle( ) {
       return "Extra Curricular Information: ";
    }

}

?>
