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
class CRM_Quest_Form_App_Testing extends CRM_Quest_Form_App
{
    static $_testIDs = array();

    /**
     * This function sets the default values for the form. Relationship that in edit/view action
     * the default values are retrieved from the database
     * 
     * @access public
     * @return void
     */
    function setDefaultValues( ) 
    {
        $defaults   = array( );
        require_once 'CRM/Utils/Date.php';

        $this->_testIDs = array();
        $testTypes  = CRM_Core_OptionGroup::values( 'test');
        $testTypes  = array_flip($testTypes);
        $testSet1 = array('act','psat','sat');

        $contactID  = $this->get('contact_id');
        
        $dao = & new CRM_Quest_DAO_Test();
        $dao->contact_id = $contactID;
        $dao->find();
        while( $dao->fetch() ) {
            if( in_array(strtolower($testTypes[$dao->test_id]),$testSet1 )) {
                $this->_testIDs[strtolower($testTypes[$dao->test_id])] = $dao->id;
            } else if ( $testTypes[$dao->test_id] == 'SAT II' ){
                $count = count($this->_testIDs['satII']) + 1;
                $this->_testIDs['satII'][$count] = $dao->id;
            } else {
                $count = count($this->_testIDs[$testTypes[$dao->test_id]]) + 1;
                $this->_testIDs[strtolower($testTypes[$dao->test_id])][$count] = $dao->id;
            }
        }
       
        //set the default values // FIX ME
        $subject = array('english','reading','criticalReading','writing','math','science','composite','total');
        foreach ($this->_testIDs as $test => $value ) {
            if ( ! is_array($value) ) {
                $dao = & new CRM_Quest_DAO_Test();
                $dao->id = $value;
                $dao->find(true);
                foreach ( $subject as $sub ) {
                    $field = "score_".$sub;
                    $defaults[$test."_$sub"] = $dao->$field;
                    if ( $sub == 'criticalReading' ) {
                        $defaults[$test.'_criticalreading'] = $dao->score_reading;
                    }
                }
                if ( $sub == 'total' && ( $test =='psat' || $test =='sat'  )) {
                    $defaults[$test.'_total'] = $dao->score_composite;
                }
                $defaults[$test."_date"] = CRM_Utils_Date::unformat( $dao->test_date , '-' );
            } else {
                foreach ( $value as $k => $v ) {
                    $dao = & new CRM_Quest_DAO_Test();
                    $dao->id = $v;
                    $dao->find(true);
                    $defaults[$test."_".$k."_subject"] = $dao->subject;
                    $defaults[$test."_".$k."_score"]   = $dao->score_composite;
                    $defaults[$test."_".$k."_date"]    = CRM_Utils_Date::unformat( $dao->test_date , '-' );
                }

            }
                
        }
        require_once 'CRM/Quest/DAO/Student.php';
        $studDAO = & new CRM_Quest_DAO_Student();
        $studDAO->contact_id =$contactID;
        $studDAO->find(true);
        if ( $studDAO->test_tutoring ) {
            $selected = explode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR,$studDAO->test_tutoring);
            foreach ($selected as $val ) {
                $defSeleted[$val] = 1;
            }
            $defaults['test_tutoring']    = $defSeleted;
            $defaults['is_test_tutoring'] = 1;
        }

        $this->set( 'testIDs' ,$this->_testIDs );
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

        $params = array( 'English'          => 1,
                         'Reading'          => 1,
                         'CriticalReading'  => 6,
                         'Writing'          => 7,
                         'Math'             => 7,
                         'Science'          => 1,
                         'Composite'        => 1,
                         'Total'            => 6 );

        $tests = array( 'act'  => 1,
                        'psat' => 2,
                        'sat'  => 4 );

        foreach ( $tests as $testName => $testValue ) {
            foreach ( $params as $name => $value ) {
                if ( $value & $testValue ) {
                    $this->addElement( 'text',
                                       $testName . '_' . strtolower( $name ),
                                       ts( $name . ' Score' ),
                                       $attributes['score_english'] );
                }
            }

            // add the date field
            $this->addElement('date', $testName . '_date',
                              ts( 'Date Taken (month/year)' ),
                              CRM_Core_SelectValues::date( 'custom', 5, 1, "M\001Y" ) );
        }

        // add 5 Sat II tests
        for ( $i = 1; $i <= 5; $i++ ) {
            $this->addElement( 'text',
                               'satII_' . $i . '_subject',
                               ts( 'Subject' ),
                               $attributes['subject'] );
            $this->addElement( 'text',
                               'satII_' . $i . '_score',
                               ts( 'Score' ),
                               $attributes['score_english'] );
            $this->addElement('date', 'satII_' . $i . '_date',
                              ts( 'Date Taken (month/year)' ),
                              CRM_Core_SelectValues::date( 'custom', 5, 1, "M\001Y" ) );
        }

        // add 32 AP test
        for ( $i = 1; $i <= 32; $i++ ) {
            $this->addElement( 'text',
                               'ap_' . $i . '_subject',
                               ts( 'Subject' ),
                               $attributes['subject'] );
            $this->addElement( 'text',
                               'ap_' . $i . '_score',
                               ts( 'Score' ),
                               $attributes['score_english'] );
            $this->addElement('date', 'ap_' . $i . '_date',
                              ts( 'Date Taken (month/year)' ),
                              CRM_Core_SelectValues::date( 'custom', 5, 1, "M\001Y" ) );
        }

        $this->addYesNo( 'is_test_tutoring',
                         ts( 'Have you received tutoring for any of the standardized tests above?' ) );
        
        $this->addCheckBox( 'test_tutoring',
                            ts( 'If yes, for which tests?' ),
                            CRM_Core_OptionGroup::values( 'test',true ),
                            false ,null);
     
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
        $params = $this->controller->exportValues( $this->_name );
        $testSet1 = array('act','psat','sat');
        $testSet2 = array('satII','ap');

        $contactId = $this->get('contact_id');
        $testTypes = CRM_Core_OptionGroup::values( 'test' ,true);
        
        $testParams1 = array();
        $totalScore = array();

        require_once 'CRM/Utils/Date.php';
        foreach ( $testSet1 as  $sub ) {
            foreach ( $params as $key => $value  ) {
                $keyArray = explode( '_' ,$key );
                if ( $keyArray[0] == $sub ) {
                    if ( $keyArray[1] == 'date' ) {
                        
                        $testParams1[$sub]["test_".$keyArray[1]]  = CRM_Utils_Date::format( $value );
                    } else if ( $keyArray[1] == 'total' ) {
                        $testParams1[$sub]["score_composite"]     = $value;
                        $totalScore[strtoupper($sub)] = $value;
                    } else if ($keyArray[1] == 'criticalreading') {
                        $testParams1[$sub]["score_reading"] = $value;
                    } else {
                        $testParams1[$sub]["score_".$keyArray[1]] = $value;
                    }
                }
            }
            $testParams1[$sub]['contact_id'] = $contactId;
            $testParams1[$sub]['test_id']    = $testTypes[strtoupper($sub)];
        }
       
        $testParams2 = array();
        foreach ( $testSet2 as $sub ){
            foreach ( $params as $key => $value  ) {
                $keyArray = explode('_' , $key ) ;
                if ( $keyArray[0] == $sub ) {
                    if ( $keyArray[2] == 'date' ) {
                        $testParams2[$sub][$keyArray[1]]["test_".$keyArray[2]] =  CRM_Utils_Date::format( $value );
                    } else if ($keyArray[2] == 'score') { 
                        $testParams2[$sub][$keyArray[1]]["score_composite"] = $value;
                    } else {
                        $testParams2[$sub][$keyArray[1]][$keyArray[2]] = $value;
                    }
                    $testParams2[$sub][$keyArray[1]]['contact_id'] = $contactId;
                    if ( $sub == "satII" ) {
                        $testParams2[$sub][$keyArray[1]]['test_id']    = $testTypes[strtoupper('sat II')];
                    } else {
                        $testParams2[$sub][$keyArray[1]]['test_id']    = $testTypes[strtoupper($sub)];
                    }
                }
                
            }
        }
        require_once 'CRM/Quest/BAO/Test.php';
        
        $this->_testIDs = $this->get( 'testIDs' );
        
        // add data to database
        // for 'act','psat','sat'
        foreach ( $testParams1 as $key => $value ) {
            $testParam = $value;
            $ids  = array();
            if ( $this->_testIDs[$key] ) {
                $ids['id'] = $this->_testIDs[$key];
            }
            $test = CRM_Quest_BAO_Test::create( $testParam ,$ids );
            $this->_testIDs[$key] = $test->id;
        }
        

        //for 'satII','ap'

        foreach ( $testParams2 as $key => $value ) {
            foreach ( $value as $k => $v ) {
                $testParam = $v;
                 $ids  = array();
                 if ( $this->_testIDs[$key][$k] ) {
                     $ids['id'] = $this->_testIDs[$key][$k];
                 }
                 $test = CRM_Quest_BAO_Test::create( $testParam ,$ids );
                 $this->_testIDs[$key][$k] = $test->id;
            }
        }
        
        $this->set( 'testIDs' ,$this->_testIDs );
        
        // Insert  Student recornd  
        
        $values = $this->controller->exportValues( 'Personal' );
        $values['score_SAT']     =  $totalScore['SAT'];
        $values['score_PSAT']    =  $totalScore['PSAT'];
        $values['score_ACT']     =  $totalScore['ACT'];
        require_once 'CRM/Core/BAO/CustomOption.php';
        $values['test_tutoring'] =  implode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR,array_keys($params['test_tutoring']));
        $id = $this->get('id');
        $contact_id = $this->get('contact_id');
        $ids = array();
        $ids['id'] = $id;
        $ids['contact_id'] = $contact_id;

        require_once 'CRM/Quest/BAO/Student.php';
        $student = CRM_Quest_BAO_Student::create( $values, $ids);
              
        
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