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
class CRM_Quest_Form_MatchApp_Partner_Princeton_PrinceApplicant   extends CRM_Quest_Form_App
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
    {} 
     
    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm( ) 
    {
     

        $this->addYesNo( 'is_diploma', ts( 'Are you an International Baccalaureate diploma candidate?' ) ,null,true);

           
        for ( $i = 1; $i <= 7; $i++ ) {
            $this->addElement('text', "subject_$i", ts('Subject'), null );
            
            $this->addElement('date', 'test_date_$i',
                              ts(' Month/Year)'),
                              CRM_Core_SelectValues::date('custom', 50, 0, "M\001Y" ));
            $this->addElement('radio', 'sl_hl', ts('SL  or HL'));
                              
            $this->addElement('text', "score_$i", ts('Score'), null );
        } 

        $this->addCheckBox( 'princeton_activities',ts('Please indicate the three activities in which, at this time, you would be most inclined to participate at Princeton.  '), CRM_Core_OptionGroup::values( 'princeton_activities', true ),
                            true,null );
        
        $this->addElement('text', "Please choose a four digit pin number.", null, null ); 

        $this->addRadio( 'princeton_degree', ts('Which degree would you most likely pursue at Princeton? (Your choice is not binding in any way.)'),CRM_Core_OptionGroup::values('princeton_degree') );

        $this->addCheckBox( 'ab_department',ts('A.B. Departments'), CRM_Core_OptionGroup::values( 'ab_department', true ),
                            false,null );

        $this->addCheckBox( 'bsc_department',ts(' B.S.E Departments'), CRM_Core_OptionGroup::values( 'bsc_department', true ),
                            false,null );

        $this->addCheckBox( 'certificate_programs',null, CRM_Core_OptionGroup::values( 'certificate_programs', true ),
                            false,null );

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
      
    } 

    /** 
     * process the form after the input has been submitted and validated 
     * 
     * @access public 
     * @return void 
     */ 
    public function postProcess()  
    {}
    
    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle()
    {
        return  ts('Princeton University');
    }

   

}

?>




