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

    protected $_allChecks;

    protected $_testTypes;

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        parent::preProcess();
        $this->_allChecks = array('princeton_activities', 'ab_department', 'bse_department', 'certificate_programs');
        $this->_testTypes = CRM_Core_OptionGroup::values( 'princeton_test' ,true);
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
        
        require_once 'CRM/Quest/BAO/Test.php';
        $test =& new CRM_Quest_DAO_Test( );
        $test->contact_id = $this->_contactID;
        $test->test_id = $this->_testTypes['Princeton test'];
        $test->find();
        $count = 0;
        while ( $test->fetch( ) ) {
            $count++;
            $defaults['subject_'.$count]   = $test->subject;   
            $defaults['test_date_'.$count] = $test->test_date;   
            $defaults['slhl_'.$count]      = $test->sl_hl;   
            $defaults['score_'.$count]     = $test->score_composite;   
        }
        
        require_once 'CRM/Quest/Partner/DAO/Princeton.php';
        $dao =& new CRM_Quest_Partner_DAO_Princeton( );
        $dao->contact_id = $this->_contactID;
        if ( $dao->find( true ) ) {
            CRM_Core_DAO::storeValues( $dao , $defaults );
        }
        
        foreach ( $this->_allChecks as $name ) {
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
        $this->addYesNo( 'is_diploma', ts( 'Are you an International Baccalaureate diploma candidate?' ) ,null,true);
        
        for ( $i = 1; $i <= 7; $i++ ) {
            $this->addElement('text', "subject_{$i}", ts('Subject'), null );
            $this->addElement('date', "test_date_{$i}",
                              ts(' Month/Year)'),
                              CRM_Core_SelectValues::date('custom', 50, 0, "M\001Y" ));
            $slhl = array();
            $slhl[] =  $this->createElement( 'radio', null, null, ts( 'SL' ), 'sl', null );
            $slhl[] =  $this->createElement( 'radio', null, null, ts( 'HL' ), 'hl', null );
            $this->addGroup( $slhl, "slhl_{$i}", null );
            $this->addElement('text', "score_{$i}", ts('Score'), null );
        } 
        $extra =array('onclick' => "return showHideByValue('princeton_activities[11]', '1', 'activities_other', '', 'radio', false);");
        $this->addCheckBox( 'princeton_activities',ts('Please indicate the three activities in which, at this time, you would be most inclined to participate at Princeton.  '), CRM_Core_OptionGroup::values( 'princeton_activities', true ),
                            true,'<br/>',null,$extra);
        $this->addElement('text', 'activities_other',null);
        
        $this->addElement('text', 'pin_no',ts("Please choose a four digit pin number."), null, null ); 

        $this->addRadio( 'princeton_degree', ts('Which degree would you most likely pursue at Princeton? (Your choice is not binding in any way.)'),CRM_Core_OptionGroup::values('princeton_degree') );

        $this->addCheckBox( 'ab_department',ts('A.B. Departments'), CRM_Core_OptionGroup::values( 'ab_department', true ),
                            false,null );
        
        $this->addCheckBox( 'bse_department',ts(' B.S.E Departments'), CRM_Core_OptionGroup::values( 'bse_department', true ),
                            false,null );
        
        $this->addCheckBox( 'certificate_programs',null, CRM_Core_OptionGroup::values( 'certificate_programs', true ),
                            false,null );
        
        parent::buildQuickForm();
        
        require_once 'CRM/Core/ShowHideBlocks.php';
        $showHide = new CRM_Core_ShowHideBlocks();
        $showHide->addHide('activities_other');
        $showHide->addToTemplate();
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
    {
        if ( $this->_action &  CRM_Core_Action::VIEW ) {
            return;
        }
        
        $params = $this->controller->exportValues( $this->_name );

        foreach ( $this->_allChecks as $name ) {
            $par = CRM_Utils_Array::value( $name, $params, array());
            $params[$name] = implode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR,
                                     array_keys($par));
        }
        //require_once 'CRM/Quest/Partner/DAO/Princeton.php';
        $dao =& new CRM_Quest_Partner_DAO_Princeton( );
        $dao->contact_id = $this->_contactID;
        $dao->find( true );
        $dao->copyValues($params);
        $dao->save( );
        
        //require_once 'CRM/Quest/BAO/Test.php';
        $test =& new CRM_Quest_DAO_Test( );
        $test->contact_id = $this->_contactID;
        $test->test_id = $this->_testTypes['Princeton test'];
        $test->delete( );
        
        for ( $i = 1; $i <= 6; $i++ ) {
            $values = array();
            $values['contact_id'] = $this->_contactID;
            $values['test_id']    = $this->_testTypes['Princeton test'];
            if ( $params['subject_'.$i] ) {
                $values['subject']          = $params['subject_'.$i];
                $values['test_date']        = CRM_Utils_Date::format($params['test_date_'.$i]);
                $values['sl_hl']            = $params['slhl_'.$i];
                $values['score_composite']  = $params['score_'.$i];
                CRM_Quest_BAO_Test::create( $values, $ids );
            }
        }

        parent::postProcess( );
    }
    
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

   

}

?>