<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                 |
 +--------------------------------------------------------------------+
*/


/**
 * Income Form Page
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
 * This class generates form components for income
 * 
 */
class CRM_Quest_Form_CPS_Income extends CRM_Quest_Form_App
{
    protected $_personID;
    protected $_incomeID;
    protected $_totalIncome;
    protected $_deleteButtonName = null;

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        parent::preProcess();
       
        $this->_incomeID  = CRM_Utils_Array::value( 'incomeID', $this->_options );
        $this->_personID  = CRM_Utils_Array::value( 'personID', $this->_options );
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

        if ( $this->_incomeID ) {
            require_once 'CRM/Quest/DAO/Income.php';
            $dao = & new CRM_Quest_DAO_Income();
            $dao->id = $this->_incomeID;
            if ($dao->find(true)) {
                CRM_Core_DAO::storeValues( $dao , $defaults );
            }
            
            //for type of income
            $defaults['type_of_income_id_1'] = $defaults['source_1_id'];
            $defaults['type_of_income_id_2'] = $defaults['source_2_id'];
            $defaults['type_of_income_id_3'] = $defaults['source_3_id'];
        }

        if ( $this->_personID ) {
            //set the first name and last name
            require_once 'CRM/Quest/DAO/Person.php';
            $dao = & new CRM_Quest_DAO_Person();
            $dao->id = $this->_personID;
            if ($dao->find(true)) {
                $defaults['first_name'] = $dao->first_name;
                $defaults['last_name']  = $dao->last_name;
            }
        }
        
        // Assign show and hide blocks lists to the template for optional Academic Honors blocks
        if ( ! ( $this->_action & CRM_Core_Action::VIEW ) ) {
            $this->_showHide =& new CRM_Core_ShowHideBlocks( );
            for ( $i = 2; $i <= 3; $i++ ) {
                if ( CRM_Utils_Array::value( "type_of_income_id_$i", $defaults )) {
                    $this->_showHide->addShow( "id_income_{$i}" );
                    $this->_showHide->addHide( "id_income_{$i}_show" );
                } else {
                    $this->_showHide->addHide( "id_income_{$i}" );
                }
            }
            $this->_showHide->addToTemplate( );
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
        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_DAO_Person');

        $this->addElement( 'text', "first_name",
                           ts('First Name'),
                           $attributes['first_name'] );
        $this->addElement( 'text', "last_name",
                           ts('Last Name'),
                           $attributes['last_name'] );

        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_DAO_Income');

        // Add up to 3 sets of income type fields for this income source
        require_once 'CRM/Core/ShowHideBlocks.php';
        $income = array();
        for ( $i = 1; $i <= 4; $i++ ) {
            if ( $i < 2) {
                $this->addSelect( 'type_of_income', ts( 'Type of Income' ), "_$i" ,true );
                $this->addElement( 'text', "amount_$i",
                               ts( 'Total 2005 Income from this Source' ),
                                   array("onkeyup" => "return calculateIncome();") );
                $this->addRule("amount_$i","Please enter total 2005 income from this source.",'required');
                $this->addRule("amount_$i","Please enter a valid income.",'numeric');
            } else {
                $this->addSelect( 'type_of_income', ts( 'Additional Income Type' ), "_$i");
                $this->addElement( 'text', "amount_$i",
                               ts( 'Additional 2005 Income Amount' ),
                                array("onkeyup" => "return calculateIncome();"));
                $this->addRule("amount_$i","Please enter a valid income.",'numeric');
            }
            $this->addElement( 'text', "job_$i",
                               ts( 'Job Description (if applicable)' ),
                               $attributes['job_1'] );
	    $this->addRule("job_$i",'Maximum length 128 characters','maxlength',128);
            
            if ( ! ( $this->_action & CRM_Core_Action::VIEW ) ) {
                $income[$i] = CRM_Core_ShowHideBlocks::links( $this,"income_{$i}",
                                                              ts('add another type of income'),
                                                              ts('hide this type of income'),
                                                              false );
            }
        }
        
        $maxIncome = 3;
        if ( $this->_action & CRM_Core_Action::VIEW ) {
            $defaults = $this->setDefaultValues( );
            $maxIncome = 0;
            for ( $i = 1; $i <= 4; $i++ ) {
                if ( CRM_Utils_Array::value( "type_of_income_id_$i", $defaults )) {
                    $maxIncome++;
                }
            }
        }
        

        // Assign showHide links to tpl
        $this->assign( 'income', $income );
        $this->assign( 'maxIncome', $maxIncome + 1 );

        // if this is the last form, add another income source button
        if ( $this->_options['lastSource'] ) {
            $this->add( 'checkbox', "another_income_source", ts( "Add another income source?" ), ts( "Add another income source?" ) );
        }

        $this->addElement( 'text', "total_amount",ts('Total Income entered for this person'));
        $this->_deleteButtonName = $this->getButtonName( 'next'   , 'delete' );
        $this->assign( 'deleteButtonName', $this->_deleteButtonName );
        $this->add( 'submit', $this->_deleteButtonName, ts( 'Delete this Income Source' ) );
     
        $this->addFormRule(array('CRM_Quest_Form_CPS_Income', 'formRule'));
   
        parent::buildQuickForm();
            
    } //end of function

    function validate( ) {
        // check if the delete button has been submitted 
        // if so skip all validation
        $buttonName = $this->controller->getButtonName( ); 
        if ( $buttonName == $this->_deleteButtonName ) { 
            return true;
        } 

        return parent::validate( );
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
            // check if the delete button has been submitted
            $buttonName = $this->controller->getButtonName( );
            if ( $buttonName == $this->_deleteButtonName ) {
                // delete the income record
                if ( $this->_incomeID ) {
                    require_once 'CRM/Quest/DAO/Income.php'; 
                    $dao = & new CRM_Quest_DAO_Income();
                    $dao->id = $this->_incomeID;
                    $dao->delete( );
                }

                $this->fixTotalIncome( );
                // if there is a person, delete if not guardian
                if ( $this->_personID ) {
                    require_once 'CRM/Quest/DAO/Person.php';
                    $dao = & new CRM_Quest_DAO_Person( );
                    $dao->id = $this->_personID;
                    if ( $dao->find( true ) ) {
                        if ( ! $dao->is_parent_guardian ) {
                            $dao->delete( );
                        } else {
                            // reset the contributor flag of this guardian
                            // we do it this way to optimize what we save
                            $dao = & new CRM_Quest_DAO_Person( );
                            $dao->id = $this->_personID;
                            $dao->is_income_source = false;
                            $dao->save( );
                        }
                    }
                }

                self::getPages( $this->controller, true );
                return;
            }
            
            $params  = $this->controller->exportValues( $this->_name );
            
            $params['source_1_id'] = $params['type_of_income_id_1']; 
            $params['source_2_id'] = $params['type_of_income_id_2'];
            $params['source_3_id'] = $params['type_of_income_id_3'];
            
            if ( ! $this->_personID ) {
                $personParams = array( );
                $personParams['first_name'] = $params['first_name'];
                $personParams['last_name']  = $params['last_name'];
                $personParams['contact_id'] = $this->_contactID;
                $personParams['is_income_source'] = true;
                $relationship = CRM_Core_OptionGroup::values( 'relationship' );
                $personParams['relationship_id'] = array_search( 'Other', $relationship );
                
                $ids = array( );
                require_once 'CRM/Quest/BAO/Person.php';
                $person = CRM_Quest_BAO_Person::create( $personParams , $ids );
                $this->_personID = $person->id;
            }
            
            $params['person_id'] = $this->_personID;
            $params['amount_1']  = (int)(str_replace(",","",$params['amount_1']));
            $params['amount_2']  = (int)(str_replace(",","",$params['amount_2']));
            $params['amount_3']  = (int)(str_replace(",","",$params['amount_3']));

            $ids = array( 'id' => $this->_incomeID );
            
            require_once 'CRM/Quest/BAO/Income.php';
            $income = CRM_Quest_BAO_Income::create( $params , $ids );

            $this->fixTotalIncome( );

            $details = $this->controller->get( 'incomeDetails' );
            $details[ $this->_name ] =
                array( 'className' => 'CRM_Quest_Form_CPS_Income',
                       'title'     => "{$params['first_name']} {$params['last_name']}",
                       'options'   => array( 'personID'   => $this->_personID,
                                             'incomeID'   => $income->id,
                                             'lastSource' => false ) );
            
            if ( CRM_Utils_Array::value( 'another_income_source', $params ) ) {
                $details[$this->_name . '-1'] = array( 'className' => 'CRM_Quest_Form_CPS_Income',
                                                       'title'     => 'Add an Income Source',
                                                       'options'   => array( 'personID'   => null,
                                                                             'incomeID'   => null,
                                                                             'lastSource' => false ) );
            }
            $keys = array_keys( $details );
            $last = array_pop( $keys );
            $details[$last]['options']['lastSource'] = true;
            
            $this->controller->set( 'incomeDetails', $details );
            
            $this->controller->rebuild( );
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
        return $this->_title ? $this->_title : ts('Household Income');
    }

    public function getRootTitle( ) {
        return "Income Details: ";
    }

    public function fixTotalIncome( ) {
        // since we cant depend on the session, retrive all income
        // objecsts from db and calculate total income
        $query = "
SELECT i.amount_1 as amount_1, i.amount_2 as amount_2, i.amount_3 as amount_3
FROM   quest_income i, quest_person p
WHERE  i.person_id  = p.id
  AND  p.contact_id = %1
";
        $p = array( 1 => array( $this->_contactID, 'Integer' ) );
        $dao =& CRM_Core_DAO::executeQuery( $query, $p );
        
        $totalIncome = 0.0;
        while ( $dao->fetch( ) ) {
            for ( $i = 1; $i <= 3; $i++ ) {
                $field = "amount_$i";
                if ( $dao->$field ) {
                    $totalIncome += (float ) $dao->$field;
                }
            }
        }
      
        //add total Income in student Table
        // $studValues = array( );
//         $studValues['household_income_total'] = $totalIncome;
//         $ids = array( 'id'         => $this->_studentID,
//                       'contact_id' => $this->_contactID );
        
//         require_once 'CRM/Quest/BAO/Student.php';
//         $student = CRM_Quest_BAO_Student::create( $studValues, $ids);
        
        $summaryValue = array();
        $ids = array();
        $summaryValue['household_income_total'] = $totalIncome;
        $summaryValue['contact_id'] =  $this->_contactID;
        require_once "CRM/Quest/DAO/StudentSummary.php";
        $dao = & new CRM_Quest_DAO_StudentSummary();
        $dao->contact_id = $this->_contactID;
        if ( $dao->find(true) ) {
            $ids = array( 'id' => $dao->id);
        }
        
        require_once "CRM/Quest/BAO/Student.php";
        $studentSummary = CRM_Quest_BAO_Student::createStudentSummary( $summaryValue, $ids);
        
    }
    
    static function &getPages( &$controller, $reset = false )  {
        $details = $controller->get( 'incomeDetails' );
        
        if ( ! $details || $reset ) {
            $cid = $controller->get( 'contactID' ); 
            $last = null;
            require_once 'CRM/Quest/DAO/Income.php';
            $query = "
SELECT i.id as id, i.person_id as person_id
FROM   quest_income i, quest_person p
WHERE  i.person_id = p.id
  AND  p.contact_id = %1
";
            $p = array( 1 => array( $cid, 'Integer' ) );
            $dao =& CRM_Core_DAO::executeQuery( $query, $p );

            $details = array( );
            while ( $dao->fetch( ) ) {
                require_once 'CRM/Quest/DAO/Person.php';
                $person =& new CRM_Quest_DAO_Person();
                $person->contact_id = $cid; 
                $person->id = $dao->person_id;
                if ( $person->find( true ) ) {
                    $deceasedYear = CRM_Utils_Date::unformat($person->deceased_year);
                    $deceasedYear = $deceasedYear['Y'];
                    if ( ! $person->is_deceased || $deceasedYear == date("Y") ) {
                        $details[ "Income-{$dao->person_id}"] =
                            array( 'className' => 'CRM_Quest_Form_CPS_Income',
                                   'title'     => "{$person->first_name} {$person->last_name}",
                                   'options'   => array( 'personID'   => $person->id,
                                                         'incomeID'   => $dao->id,
                                                         'lastSource' => false ) );
                        $last = "Income-{$dao->person_id}";
                    }
                } else {
                    CRM_Core_Error::fatal( "Database is inconsistent" );
                }
                    
            }

            // now get all parent/guardians that have some income
            require_once 'CRM/Quest/DAO/Person.php';
            require_once 'CRM/Quest/Form/CPS/Guardian.php';
            require_once 'CRM/Utils/Date.php';
            $dao =& new CRM_Quest_DAO_Person( );
            $dao->contact_id = $cid;
            $dao->is_parent_guardian = true;
            $dao->is_income_source   = true;
            $dao->find( );
            while ( $dao->fetch( ) ) {
                $deceasedYear = CRM_Utils_Date::unformat($dao->deceased_year);
                $deceasedYear = $deceasedYear['Y'];
                if ( ! CRM_Utils_Array::value( "Income-{$dao->id}", $details ) &&
                     ! $dao->is_deceased || $deceasedYear == date( 'Y' ) ) {
                    $details[ "Income-{$dao->id}"] =
                        array( 'className' => 'CRM_Quest_Form_CPS_Income',
                               'title'     => "{$dao->first_name} {$dao->last_name}",
                               'options'   => array( 'personID'   => $dao->id,
                                                     'incomeID'   => null,
                                                     'lastSource' => false ) );
                    $last = "Income-{$dao->id}";
                }
            }
            if ( $last ) {
                $details[$last]['options']['lastSource'] = true;
            }
            $controller->set( 'incomeDetails', $details );
         }

        
        if ( empty( $details ) ) {
            // dont store this in session, always add at end
            $details['Income-New'] = array( 'className' => 'CRM_Quest_Form_CPS_Income',
                                            'title'     => 'Add an Income Source',
                                            'options'   => array( 'personID'   => null,
                                                                  'incomeID'   => null,
                                                                  'lastSource' => true ) );
        }

        return $details;
    }

    /**
     * Function for form rules
     *
     * @param array $params (ref.) an assoc array of name/value pairs
     *
     * @return mixed true or array of errors
     * @access public
     * @static
     */
    public function formRule(&$params)
    {
        $errors = array( );

        //CRM_Core_Error::debug('d', $params);

        for ( $i = 2; $i <= 4; $i++ ) {
            if ( $params["amount_{$i}"] && !$params["type_of_income_id_{$i}"] ) {
                $errors["type_of_income_id_{$i}"] = "Please select Type of Income.";
            }

            if ( !$params["amount_{$i}"] && $params["type_of_income_id_{$i}"] ) {
                $errors["amount_{$i}"] = "Please enter total 2005 income from this source.";
            }

            if ( $params["amount_{$i}"] ) {
                if ( !is_numeric($params["amount_{$i}"]) ) {
                    $errors["amount_{$i}"]  = "Please enter a valid income.";
                }
            }
        }
        
        return empty($errors) ? true : $errors;
    } 

}

?>