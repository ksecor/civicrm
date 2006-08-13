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
 * Household Information Form Page
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
 * This class generates form components for Household Information
 * 
 */
class CRM_Quest_Form_MatchApp_Household extends CRM_Quest_Form_App
{
    protected $_personIDs = null;

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
        $this->_personIDs = array( );

        $person_1_id = $person_2_id = null;
        for ( $i = 1; $i <= 2; $i++ ) {
            $this->_personIDs[$i] = array( );
            require_once 'CRM/Quest/DAO/Household.php';
            $dao = & new CRM_Quest_DAO_Household();
            $dao->contact_id     = $this->_contactID;
            $dao->household_type = ($i == 1 ) ? 'Current' : 'Previous';
            if ( $dao->find(true) ) {
                //CRM_Core_Error::debug( "dao", $dao );
                $defaults['member_count_'.$i]   = $dao->member_count;
                $defaults['years_lived_id_'.$i] = $dao->years_lived_id;
                if ( $i == 1 ) {
                    $defaults['description']     = $dao->description;
                    $defaults['foster_child']    = $dao->foster_child;
                }
                for ( $j = 1; $j <= 2; $j++ ) {
                    require_once 'CRM/Quest/DAO/Person.php';
                    $personDAO = & new CRM_Quest_DAO_Person();
                    $string = "person_{$j}_id"; 
                    $personDAO->id = $dao->$string;
                    if ( $personDAO->id && $personDAO->find(true) ) {
                        $this->_personIDs[$i][$j] = $personDAO->id;
                        //CRM_Core_Error::debug( "$i, $j", $personDAO );
                        $defaults["relationship_id_{$i}_{$j}"] = $personDAO->relationship_id;
                        $defaults["first_name_{$i}_{$j}"]      = $personDAO->first_name;
                        $defaults["last_name_{$i}_{$j}"]       = $personDAO->last_name;
                        if ( $i == 1 ) {
                            $$string = $personDAO->id;
                        } else if ( $personDAO->id == $person_1_id ||
                                    $personDAO->id == $person_2_id ) {
                            $defaults["same_{$i}_{$j}"] = 1;
                        }
                    }
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
        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_DAO_Household');
        for ( $i = 1; $i <= 2; $i++ ) {
            if ( $i == 1 ) {
                $title = ts( 'How many people live with you in your current household (including yourself)?' );
                
            } else {
                $title = ts( 'How many people lived with you in your previous household?' );
                
            }
           
            $this->addElement( 'text',
                               'member_count_' . $i,
                               $title,
                               $attributes['member_count'] );
            $this->addSelect( "years_lived",
                              ts( 'How long have you lived in this household?' ),
                              "_$i" );
            if ( $i == 1 ) {
                $this->addRule( "member_count_$i",ts('Please enter the number of people who live with you.'),'required');
                $this->addRule( "years_lived_id_$i", ts( 'Please select a value for years lived in this household.' ), 'required' );
            }
            $this->addRule('member_count_'.$i,ts('Not a valid number.'),'positiveInteger');
            
            for ( $j = 1; $j <= 2; $j++ ) {
                // if($j ==1) {
//                     $extra = array( 'onchange' => "return showHideByValue('relationship_id_1_1', '30|31|32|33|34', 'foster_child_show', 'table-row', 'select', false);" );
//                 } else {
//                 $extra = array( 'onchange' => "return showHideByValue('relationship_id_1_2', '30|31|32|33|34', 'foster_child_show', 'table-row', 'select', false);" );
//                 }
                $extra = array();
                if($i == 1) {
                    $extra = array( 'onchange' => "show_foster('relationship_id_" . $i . "','foster_child_show');" );
                }
                $this->addSelect( "relationship",
                                   ts( 'Relationship' ),
                                  "_".$i."_".$j ,null ,$extra);
                
                $this->addElement( 'text', "first_name_".$i."_".$j,
                                   ts('First Name'),
                                   $attributes['first_name'] );
                
                $this->addElement( 'text', "last_name_".$i."_".$j,
                                   ts('Last Name'),
                                   $attributes['last_name'] );
                
                if ( $i == 2 ) {
                    $checkboxName = "same_".$i."_".$j;
                    $this->addElement( 'checkbox', $checkboxName, null, null, array('onclick' => "copyNames(\"$checkboxName\",$j);") );
                }
            }
            require_once 'CRM/Core/ShowHideBlocks.php';
            $showHide = new CRM_Core_ShowHideBlocks();
            $showHide->addHide('foster_child_show');
            $showHide->addToTemplate();
        }

        $this->addElement('textarea',
                          'description',
                          ts( 'If this section above does not adequately capture your primary caregiver situation (e,g, perhaps your older sibling was your guardian), or if you have any other unique circumstances regarding your household situation, please describe it here:' ),
                          CRM_Core_DAO::getAttribute( 'CRM_Quest_DAO_Household', 'description' ) );

        $this->addFormRule(array('CRM_Quest_Form_MatchApp_Household', 'formRule'));
                
        $this->addYesNo( 'foster_child',
                         ts( 'Are you, or have you been, in foster care?' ) ,null,false);
        parent::buildQuickForm( );
                
    }//end of function

    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle()
    {
         return ts('Household Information');
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
        $numBlocks = 2;
        $fiveYearsOrMore = 35;

        $filled = false;
        for ( $i = 1; $i <= $numBlocks; $i++ ) {
            for ( $j = 1; $j <= $numBlocks; $j++ ) {
                if ($params["relationship_id_".$i."_".$j]) {
                    $filled = true;
                    if (! $params["first_name_".$i."_".$j]) {
                        $errors["first_name_".$i."_".$j] = "Please enter the family member First Name.";
                    }
                    if (! $params["last_name_".$i."_".$j]) {
                        $errors["last_name_".$i."_".$j] = "Please enter the family member Last Name.";
                    }
                    if ( $i != 1 && ! is_numeric( $params["member_count_$i"] ) && $params["member_count_$i"] <= 0 ) {
                        $errors["member_count_".$i] = "Please enter the number of people who lived with you";                        
                    }
                } else {
                    if ($params["first_name_".$i."_".$j] || $params["last_name_".$i."_".$j]) {
                        $errors["relationship_id_".$i."_".$j] = "Please select the type of Family Member.";
                    }
                }
            }
            if ($params["relationship_id_".$i."_1"] || $params["relationship_id_".$i."_2"]) {
                if (! $params["years_lived_id_".$i]) {
                    $errors["years_lived_id_".$i] = "Please specify the number of years you lived in the household.";
                }
            }
        }

        //error trapping for same as above
        for ( $i = 1; $i <= $numBlocks; $i++ ) {
            if ($params['same_2_'.$i]) {
                if( $params['relationship_id_2_'.$i] != $params['relationship_id_1_'.$i]) {
                    $errors['relationship_id_2_'.$i] = "Please enter the same relationship as relationship is current household";  
                }
            }
        }

        if ( (! empty($params["years_lived_id_1"])) && ($params["years_lived_id_1"] != $fiveYearsOrMore) ) {
            if ( (!$params["relationship_id_2_1"]) && (!$params["relationship_id_2_2"]) ) {
                $errors["relationship_id_2_1"] = "Please complete the information about your previous household (as you have indicated that you have lived in your current household for less than 5 years).";
            } 
        }
        
        if ( ! $filled &&
             empty( $params['description'] ) ) {
            $errors["_qf_default"] = "You have to enter at least one family member or explain your circumstances";
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
           // get all the relevant details so we can decide the detailed information we need
            $params  = $this->controller->exportValues( $this->_name );
            $relationship = CRM_Core_OptionGroup::values( 'relationship' );

            $details = $this->controller->get( 'householdDetails' );
            
            // unset all details other than father and mother
            // also set the other parent guardians as null
            if (is_array($details)) {
                foreach ( $details as $name => $value ) {
                    if ( $name == "Guardian-Mother" || $name == "Guardian-Father" ) {
                        continue;
                    }
                    
                    $query = "
UPDATE quest_person
SET    is_parent_guardian = 0
WHERE  id = {$value['options']['personID']}
";
                    $par = array();
                    CRM_Core_DAO::executeQuery( $query,$par );
                    
                    unset( $details[$name] );
                }
            }
            //add values to Student summary
            require_once "CRM/Quest/DAO/StudentSummary.php";            
            $summaryValue = array();
            $ids = array();
            for ( $i = 1; $i <= 2; $i++ ) {

                $householdParams = array( );
                $householdParams['contact_id']      = $this->_contactID;
                $householdParams['household_type'] = ( $i == 1 ) ? 'Current' : 'Previous';
                $householdParams['member_count']   = $params["member_count_$i"] ;
                $householdParams['years_lived_id'] = $params["years_lived_id_$i"];
                
                if ( $i == 1 ) {
                    $householdParams['description'] = $params["description"];
                    $householdParams['foster_child']= $params["foster_child"];
                }
               
                $needed = false;
                for ( $j = 1; $j <= 2; $j++ ) {
                    $personID = $this->getRelationshipDetail( $details, $relationship, $params, $i, $j );
                    if ( $personID ) {
                        $needed = true;
                        $householdParams["person_{$j}_id"] = $personID;
                    }
                }
                
                if ( $needed ) {
                    // now create the household
                    require_once 'CRM/Quest/BAO/Household.php';
                    $dao                 =& new CRM_Quest_DAO_Household();
                    $dao->contact_id     =  $householdParams['contact_id'];
                    $dao->household_type =  $householdParams['household_type'];
                    $id = null;
                    if ( $dao->find(true) ) {
                        $id = $dao->id;
                    }
                    $ids = array( 'id' => $id );
                    CRM_Quest_BAO_Household::create( $householdParams , $ids );
                }
                
            }

            //store member_count in quest_student_summary
            $summaryValue['household_member_count'] = $params['member_count_1'];
            $summaryValue['contact_id'] =  $this->_contactID;
            $daoStudent = & new CRM_Quest_DAO_StudentSummary();
            $daoStudent->contact_id = $this->_contactID;
            $daoStudent->contact_id = $this->_contactID;
            if ( $daoStudent->find(true) ) {
                $ids = array( 'id' => $daoStudent->id);
            }
            require_once 'CRM/Quest/BAO/Student.php';
            $studentSummary = CRM_Quest_BAO_Student::createStudentSummary( $summaryValue, $ids);
            
            // reset all parent guardian pages
            self::getPages( $this->controller, true );

            // also recreate all income pages
            require_once "CRM/Quest/Form/MatchApp/Income.php";
            CRM_Quest_Form_MatchApp_Income::getPages( $this->controller, true );
            
            $this->controller->rebuild( );
        }
       parent::postProcess( );
} 
   
    public function getRelationshipDetail( &$details, &$relationship, &$params, $i, $j )
    {
        $first = trim( CRM_Utils_Array::value( "first_name_{$i}_{$j}", $params ) );
        $last  = trim( CRM_Utils_Array::value( "last_name_{$i}_{$j}" , $params ) );
        $relationshipID = CRM_Utils_Array::value( "relationship_id_{$i}_{$j}", $params );
        $name = trim( $first . ' ' . $last );
        if ( ! $name ) {
            return;
        }

        $relationshipName = trim( CRM_Utils_Array::value( $relationshipID,
                                                          $relationship ) );

        if ( ! $relationshipName ) {
            return;
        }

        if ( CRM_Utils_Array::value( "same_{$i}_{$j}", $params ) ) {
            if ( CRM_Utils_Array::value( $relationshipID, $details ) ) {
                return $details[$relationshipID];
            } 
            CRM_Core_Error::fatal( ts( "This should have been trapped in a form rule" ) );
        }

        // we also need to create the person record here
        $personParams                       = array( );
        $personParams['first_name']         = $first;
        $personParams['last_name' ]         = $last;
        $personParams['relationship_id']    = $relationshipID;
        $personParams['contact_id']         = $this->_contactID;
        $personParams['is_parent_guardian'] = true;
        $personParams['is_income_source']   = true;

        $ids = array( );

        require_once 'CRM/Quest/BAO/Person.php'; 

        // check if the person already has an if
        $personID = CRM_Utils_Array::value( $j, $this->_personIDs[$i] );

        $dao =& new CRM_Quest_DAO_Person(); 
        if ( $personID ) {
            $dao->id = $personID;
        } else {
            $dao->contact_id      = $this->_contactID;
            $dao->relationship_id = $relationshipID;
            $dao->first_name      = $first;
            $dao->last_name       = $last;
        }

        if ( $dao->find(true) ) { 
            $personID = $dao->id;
            
            // keep the is income source the same value as prior
            $personParams['is_income_source'] = $dao->is_income_source;
        }

        $ids['id'] = $personID;
        $person = CRM_Quest_BAO_Person::create( $personParams , $ids );
        $personID = $person->id;

        $details[$relationshipID] = $personID;
        return $personID;
    } 
    
    static function &getPages( &$controller, $reset = false ) 
    {
        $details       = $controller->get( 'householdDetails' );

        if ( ! $details || $reset ) {
            $cid = $controller->get( 'contactID' ); 

            require_once 'CRM/Quest/DAO/Person.php';
            $dao =& new CRM_Quest_DAO_Person( );
            $dao->contact_id = $cid;
            $dao->is_parent_guardian = true;
            $dao->find( );

            $details = array( );
            $relationship = CRM_Core_OptionGroup::values( 'relationship' );

            $relationshipID = array_search( 'Mother', $relationship );
            $details["Guardian-Mother"] = array( 'className' => 'CRM_Quest_Form_MatchApp_Guardian', 
                                                 'title' => "Mother",
                                                 'options' => array( 'personID'         => null,
                                                                     'relationshipID'   => $relationshipID,
                                                                     'relationshipName' => 'Mother' ) );

            $relationshipID = array_search( 'Father', $relationship );
            $details["Guardian-Father"] = array( 'className' => 'CRM_Quest_Form_MatchApp_Guardian', 
                                                 'title' => "Father",
                                                 'options' => array( 'personID'         => null,
                                                                     'relationshipID'   => $relationshipID,
                                                                     'relationshipName' => 'Father' ) );
            while ( $dao->fetch( ) ) {
                $relationshipName = trim( CRM_Utils_Array::value( $dao->relationship_id,
                                                                  $relationship ) );
                $name = trim( "{$dao->first_name} {$dao->last_name}" );
                if ( $relationshipName == 'Mother' || $relationshipName == 'Father' ) {
                    $pageName = "Guardian-$relationshipName";
                } else {
                    $pageName = "Guardian-{$dao->id}";
                }
                $details[$pageName] = array( 'className' => 'CRM_Quest_Form_MatchApp_Guardian', 
                                             'title' => "$name",
                                             'options' => array( 'personID'       => $dao->id,
                                                                 'relationshipID' => $dao->relationship_id,
                                                                 'relationshipName' => $relationshipName ) );
            }

            $controller->set( 'householdDetails', $details );
        }

        return $details;
    }

}

?>