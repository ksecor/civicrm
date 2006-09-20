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
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Quest/Form/App.php';
require_once 'CRM/Core/OptionGroup.php';

/**
 * This class generates form components for relationship
 * 
 */
class CRM_Quest_Form_App_Testing extends CRM_Quest_Form_App
{
    protected $_testIDs = array();

    protected $_tests;
    protected $_multiTests;
    protected $_sections;
    protected $_parts;

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
        static $defaults   = null;

        // to fix the view tempalte, we call this from
        // buildQF during view, hence we cache the result
        // and do all the db intensive work only once
        if ( ! $defaults ) {
            $defaults = array( );
            require_once 'CRM/Utils/Date.php';

            $this->_testIDs = array();
            
            $testTypes  = CRM_Core_OptionGroup::values( 'test');
            $testSet1 = array('act','psat','sat','pact');
            
            
            $dao = & new CRM_Quest_DAO_Test();
            $dao->contact_id = $this->_contactID;
            $dao->find();
            while( $dao->fetch() ) {
                if( in_array(strtolower($testTypes[$dao->test_id]),$testSet1 )) {
                    $this->_testIDs[strtolower($testTypes[$dao->test_id])] = $dao->id;
                } else if ( $testTypes[$dao->test_id] == 'SAT II' ){
                    $count = count($this->_testIDs['satII']) + 1;
                    $this->_testIDs['satII'][$count] = $dao->id;
                } else {
                    $count = count($this->_testIDs['ap']) + 1;
                    $this->_testIDs['ap'][$count] = $dao->id;
                }
            }
            
            
            //set the default values
            $subject = array('english','reading','criticalReading','writing','math','science');
            foreach ($this->_testIDs as $test => $value ) {
                if ( ! is_array($value) ) {
                    $dao = & new CRM_Quest_DAO_Test();
                    $dao->id = $value;
                    $dao->find(true);
                    foreach ( $subject as $sub ) {
                        $field = "score_$sub";
                        $defaults["{$test}_$sub"] = $dao->$field;
                        if ( $sub == 'criticalReading' ) {
                            $defaults["{$test}_criticalreading"] = $dao->score_reading;
                        }
                    }
                    $defaults["{$test}_date"] = CRM_Utils_Date::unformat( $dao->test_date , '-' );
                } else {
                    foreach ( $value as $k => $v ) {
                        $dao = & new CRM_Quest_DAO_Test();
                        $dao->id = $v;
                        $dao->find(true);
                        $defaults["{$test}_subject_id_$k"] = $dao->subject;
                        if ( $test != 'ap') {
                            $defaults["{$test}_score_$k"]   = $dao->score_composite;
                        } else {
                            $defaults["{$test}_score_id_$k"]   = $dao->score_composite;
                        }
                        $defaults["{$test}_date_$k"]    = CRM_Utils_Date::unformat( $dao->test_date , '-' );
                    }
                }
            }
            
            require_once 'CRM/Quest/DAO/Student.php';
            $studDAO = & new CRM_Quest_DAO_Student();
            $studDAO->contact_id =$this->_contactID;
            $studDAO->find(true);
            if ( $studDAO->test_tutoring ) {
                $selected = explode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR,$studDAO->test_tutoring);
                foreach ($selected as $val ) {
                    $defSeleted[$val] = 1;
                }
                $defaults['test_tutoring']    = $defSeleted;
                $defaults['is_test_tutoring'] = 1;
            }
            
            if ( ! ( $this->_action & CRM_Core_Action::VIEW ) ) {
                // Assign show and hide blocks lists to the template for optional test blocks (SATII and AP)
                $this->_showHide =& new CRM_Core_ShowHideBlocks( );
                for ( $i = 2; $i <= 5; $i++ ) {
                    if ( CRM_Utils_Array::value( "satII_score_$i", $defaults )) {
                        $this->_showHide->addShow( "satII_test_$i" );
                    } else {
                        $this->_showHide->addHide( "satII_test_$i" );
                    }
                }
                for ( $i = 2; $i <= 32; $i++ ) {
                    if ( CRM_Utils_Array::value( "ap_score_id_$i", $defaults )) {
                        $this->_showHide->addShow( "ap_test_$i" );
                    } else {
                        $this->_showHide->addHide( "ap_test_$i" );
                    }
                }
            
                $this->_showHide->addToTemplate( );
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
        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_DAO_Test' );

        $this->_sections = array( 'English'          => 1,
                                  'Reading'          => 1,
                                  'CriticalReading'  => 6,
                                  'Writing'          => 6,
                                  'Math'             => 7,
                                  'Science'          => 1 );

        $this->_tests = array( 'act'  => 1,
                               'psat' => 2,
                               'sat'  => 4, 
                               'pact'=>  1);

        $this->_multiTests = array( 'satII' => 5,
                                    'ap'    => 32 );

        foreach ( $this->_tests as $testName => $testValue ) {
            foreach ( $this->_sections as $name => $value ) {
                if ( $value & $testValue ) {
                    $fieldName = $name;
                    $fieldName = ( $name == "CriticalReading" ) ? "Critical Reading" : $name;
                    $this->addElement( 'text',
                                       $testName . '_' . strtolower( $name ),
                                       ts('%1 Score', array(1 => $fieldName)),
                                       $attributes['score_english'] );
                    if ( $name == 'Composite') {
                        $this->addRule($testName . '_' . strtolower($name), ts('%1 score not valid.', array(1 => strtolower($name))), 'numeric');
                    } else {
                        $this->addRule($testName . '_' . strtolower($name), ts('%1 %2 score must be a whole number.', array(1 => strtoupper($testName), 2 => $name)), 'integer');
                    }
                }
            }

            // add the date field
            $this->addElement('date', $testName . '_date',
                              ts( 'Date Taken (month/year)' ),
                              CRM_Core_SelectValues::date( 'custom', 6, 0, "M\001Y" ) );
        }

        $maxSatIITests = 5;
        $maxAPTests    = 32;
        if ( $this->_action & CRM_Core_Action::VIEW ) {
            $defaults = $this->setDefaultValues( );
            $maxSatIITests = 1;
            for ( $i = 2; $i <= 5; $i++ ) {
                if ( CRM_Utils_Array::value( "satII_score_$i", $defaults )) {
                    $maxSatIITests++;
                }
            }
            
            $maxAPTests = 1;
            for ( $i = 2; $i <= 32; $i++ ) {
                if ( CRM_Utils_Array::value( "ap_score_id_$i", $defaults )) {
                    $maxAPTests++;
                }
            }
        }

        require_once 'CRM/Core/ShowHideBlocks.php';
        // add 5 Sat II tests
        $satII_test = array( );
        for ( $i = 1; $i <= $maxSatIITests; $i++ ) {
            $this->addSelect( 'satII_subject',
                               ts( 'Subject' ),
                              "_$i" );
            $this->addElement( 'text',
                               'satII_score_' . $i,
                               ts( 'Score' ),
                               $attributes['score_english'] );
            $this->addRule( 'satII_score_' . $i, ts( 'SAT II score not valid.'),'integer');
            $this->addElement('date', 'satII_date_' . $i,
                              ts( 'Date Taken (month/year)' ),
                              CRM_Core_SelectValues::date( 'custom', 6, 0, "M\001Y" ) );
            if ( ! ( $this->_action & CRM_Core_Action::VIEW ) ) {
                $satII_test[$i] = CRM_Core_ShowHideBlocks::links( $this,"satII_test_$i",
                                                                  ts('Add another SAT II test score'),
                                                                  ts('Hide this SAT II test'),
                                                                  false );
            }
        }
        $this->assign( 'satII_test', $satII_test );
        $this->assign( 'maxSAT', $maxSatIITests + 1 );

        // add 32 AP test
        $ap_test = array( );
        for ( $i = 1; $i <= $maxAPTests; $i++ ) {
            $this->addSelect( 'ap_subject',
                               ts( 'Subject' ),
                              "_$i" );

            $this->addSelect( 'ap_score' , ts( 'Score' ), "_$i" );
            $this->addRule( 'ap_score_id_' . $i, ts( 'AP Test score not valid.'),'integer');
            $this->addElement('date', 'ap_date_' . $i,
                              ts( 'Date Taken (month/year)' ),
                              CRM_Core_SelectValues::date( 'custom', 6, 0, "M\001Y" ) );
            if ( ! ( $this->_action & CRM_Core_Action::VIEW ) ) {
                $ap_test[$i] = CRM_Core_ShowHideBlocks::links( $this,"ap_test_$i",
                                                               ts('add another AP test score'),
                                                               ts('hide this AP test'),
                                                               false );
            }

        }
        $this->assign( 'ap_test', $ap_test );
        $this->assign( 'maxAP', $maxAPTests + 1 );

        $this->addYesNo( 'is_test_tutoring',
                         ts( 'Have you received tutoring or taken test prep classes for any of the standardized tests above?' ) );
        
        $this->addCheckBox( 'test_tutoring',
                            ts( 'If yes, for which tests?' ),
                            CRM_Core_OptionGroup::values( 'test',true ),
                            false ,null);

        $this->addFormRule(array('CRM_Quest_Form_App_Testing', 'formRule'));
       
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
           
        $tests = array( 'act', 'psat', 'sat' ,'pact');
        $sections = array( 'English', 'Reading', 'CriticalReading', 'Writing', 'Math',
                           'Science');

        foreach ( $tests as $testName ) {
            foreach ( $sections as $name ) {
                if ($params[$testName.'_'.strtolower( $name )]) {
                    foreach ( $sections as $checkName ) {
                        if ( (!$params[$testName.'_'.strtolower( $checkName )]) && 
                             array_key_exists($testName.'_'.strtolower( $checkName ), $params)) {
                            $errors[$testName.'_'.strtolower( $checkName )]= "Please enter the ".strtoupper($testName)." ".strtolower( $checkName )." score";
                        }
                    }
                    if ( (!$params[$testName.'_date']['M']) && (!$params[$testName.'_date']['Y']) ) {
                        $errors[$testName.'_date']= "Please enter the ".strtoupper($testName)." test date";
                    } else {
                        if ( (!$params[$testName.'_date']['M']) || !($params[$testName.'_date']['Y']) ) {
                            $errors[$testName.'_date']= "Please enter a valid ".strtoupper($testName)." test date";
                        }
                    }
                }
            }
        }

        $multiTests = array( 'satII' => 5,
                             'ap'    => 32 );
        
        foreach  (  $multiTests as $testName => $testCount ) { 
            for ( $i = 1; $i <= $testCount; $i++ ) { 
                
                $subjectKey   = "{$testName}_subject_id_$i";
                $valueSubject  = $params[$subjectKey];
                
                if ( $testName != 'ap' ) {
                    $scoreKey   = "{$testName}_score_$i";
                } else {
                    $scoreKey   = "{$testName}_score_id_$i";
                }
                
                $valueScore = $params[$scoreKey];
                
                $valueDate = "{$testName}_date_{$i}";

                if ( $valueSubject || $valueScore  ) {
                    if ( !$params[$valueDate]['M'] || !$params[$valueDate]['Y'] ) {
                        $errors[$valueDate] = "Please enter the valid date for ". strtoupper($testName);
                    }
                    if ( !$valueScore ) {
                        $errors[$scoreKey]= "Please enter the ".strtoupper($testName)." score.";
                    } else if( !$valueSubject ) {
                        $errors[$subjectKey]= "Please enter the ".strtoupper($testName)." subject.";
                    }
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
        if ( ! ( $this->_action &  CRM_Core_Action::VIEW ) ) {
            $params = $this->controller->exportValues( $this->_name );
            
            $testSet1 = array('act','psat','sat','pact');
            $testSet2 = array('satII','ap');
            
            $testTypes = CRM_Core_OptionGroup::values( 'test' ,true);
            
            $testParams1 = array();
            
            require_once 'CRM/Utils/Date.php';
            
            foreach ( $this->_tests as $testName => $testValue ) {
                $filled = false;
                foreach ( $this->_sections as $name => $value ) {
                    if ( $value & $testValue ) {
                        $key   = $testName . '_' . strtolower( $name );
                        $value = $params[$key];
                        if ( ! empty( $value ) ) {
                            $filled = true;
                            if ($name == 'CriticalReading') {
                                $testParams1[$testName]["score_reading"] = $value;
                            } else {
                                $testParams1[$testName]["score_" . strtolower( $name )] = $value;
                            }
                        }
                    }
                }
                
                $key   = "{$testName}_date";
                $value = $params[$key]; 
                if ( ! CRM_Utils_System::isNull( $value ) ) {
                    $filled = true;
                    $testParams1[$testName]["test_date"] = CRM_Utils_Date::format( $value );
                }
                
                if ( $filled ) {
                    $testParams1[$testName]['contact_id'] = $this->_contactID;
                    $testParams1[$testName]['test_id']    = $testTypes[strtoupper($testName)];
                }
            }
            
            // calculate total score for SAT , PSAT , ACT
                       
            if( is_array( $testParams1 ) ) {
                foreach( $testParams1 as $test => $score ) {
                    if ( $test == 'act' ) {
                        $totalACT = $score['score_reading']+ $score['score_english']+$score['score_science']+$score['score_math'] ;
                    } else if($test == 'psat') {
                        $totalPSAT         =  $score['score_reading'] + $score['score_math'] + $score['score_writing'];
                    } else if ( $test == 'sat' ) {
                        $totalSAT         =  $score['score_reading'] + $score['score_math'] + $score['score_writing'];
                    } else if ( $test == 'pact' ) {
                        $totalPACT = $score['score_reading']+ $score['score_english']+$score['score_science']+$score['score_math'] ;
                    }

                }
            }
            
            // calcuate(composite & total score)
            if ( $totalACT > 0 && is_array($testParams1['act'])) {
                $testParams1['act']['score_composite'] = round($totalACT/4);
            }
            if ( is_array($testParams1['psat'])) {
                $testParams1['psat']['score_composite'] = $totalPSAT;
            }
            if (is_array($testParams1['sat'])) {
                $testParams1['sat']['score_composite']  = $totalSAT;
            }
            if ( $totalPACT > 0 && is_array($testParams1['pact'])) {
                $testParams1['pact']['score_composite'] = round($totalPACT/4);
            }
            


            // process sat II/ ap stuff
            foreach  ( $this->_multiTests as $testName => $testCount ) { 
                for ( $i = 1; $i <= $testCount; $i++ ) { 
                    $filled = false;
                    
                    $key   = "{$testName}_subject_id_$i";
                    $value = $params[$key];
                    if ( ! empty( $value ) ) {
                        $filled = true;
                        $testParams2[$testName][$i]["subject"] = $value;
                    }
                    
                    if ( $testName != 'ap' ) {
                        $key   = "{$testName}_score_$i";
                    } else {
                        $key   = "{$testName}_score_id_$i";
                    }
                    
                    $value = $params[$key];
                    if ( ! empty( $value ) ) {
                        $filled = true;
                        $testParams2[$testName][$i]["score_composite"] = $value;
                    }
                    
                    $key   = "{$testName}_date_$i";
                    $value = $params[$key];
                    if ( ! CRM_Utils_System::isNull( $value ) ) {
                        $filled = true;
                        $testParams2[$testName][$i]["test_date"] = CRM_Utils_Date::format( $value );
                    }
                    
                    if ( $filled ) {
                        $testParams2[$testName][$i]['contact_id'] = $this->_contactID;
                        if ( $testName == 'satII' ) {
                            $testParams2[$testName][$i]['test_id']    = $testTypes[strtoupper('sat II')];
                        } else {
                            $testParams2[$testName][$i]['test_id']    = $testTypes[strtoupper($testName)];
                        }
                    }
                }
            }
            
            require_once 'CRM/Quest/BAO/Test.php';
            
            //delete all the tests records that are previously
            $dao             = & new CRM_Quest_DAO_Test();
            $dao->contact_id = $this->_contactID;
            $dao->delete();
            
        
            // add data to database
            // for 'act','psat','sat','pact'
            foreach ( $testParams1 as $key => $value ) {
                $testParam = $value;
                $ids  = array();
                $test = CRM_Quest_BAO_Test::create( $testParam ,$ids );
               
            }
            
            
            //for 'satII','ap'
            if ( is_array( $testParams2 ) ) {
                foreach ( $testParams2 as $key => $value ) {
                    foreach ( $value as $k => $v ) {
                        $testParam = $v;
                        $ids  = array();
                        $test = CRM_Quest_BAO_Test::create( $testParam ,$ids );
                        $this->_testIDs[$key][$k] = $test->id;
                    }
                }
            }
            
            // Insert  Student recornd  
            $values = array( );
            $values['score_SAT']     =  $totalSAT;
            $values['score_PSAT']    =  $totalPSAT;
            if ( $totalACT > 0) {
                $values['score_ACT'] =  round( $totalACT/4 );
            }
            if ( $totalPACT > 0) {
                $values['score_PLAN'] = round( $totalPACT/4 );
            }
            
            if ( CRM_Utils_Array::value( 'test_tutoring', $params ) &&
                 is_array( $params['test_tutoring'] ) &&
                 ! empty( $params['test_tutoring'] ) ) {
                require_once 'CRM/Core/BAO/CustomOption.php';
                $values['test_tutoring'] =  implode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR,array_keys($params['test_tutoring']));
            }
            
            $ids = array( 'id'         => $this->_studentID,
                          'contact_id' => $this->_contactID );
            
            require_once 'CRM/Quest/BAO/Student.php';
            $student = CRM_Quest_BAO_Student::create( $values, $ids);
            
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
        return ts('Testing Information');
    }
}

?>
