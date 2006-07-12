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
                $title = ts( 'How many people live with you in your current household?' );
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
                $this->addSelect( "relationship",
                                   ts( 'Relationship' ),
                                  "_".$i."_".$j );
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

        }

        $this->addElement('textarea',
                          'description',
                          ts( 'If this section above does not adequately capture your primary caregiver situation (e,g, perhaps your older sibling was your guardian), or if you have any other unique circumstances regarding your household situation, please describe it here:' ),
                          CRM_Core_DAO::getAttribute( 'CRM_Quest_DAO_Household', 'description' ) );

        $this->addFormRule(array('CRM_Quest_Form_App_Household', 'formRule'));
        
    
        $this->addYesNo( 'foster_child',
                         ts( 'Are you a foster child?' ) ,null,false);
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
        parent::getTitle();
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
        parent::formRule(&$params);
    }

    /** 
     * process the form after the input has been submitted and validated 
     * 
     * @access public 
     * @return void 
     */ 
    public function postProcess()  
    { 
        parent::postProcess( );
    }//end of function 

    public function getRelationshipDetail( &$details, &$relationship, &$params, $i, $j ) {
        parent::getRelationshipDetail( &$details, &$relationship, &$params, $i, $j );
    }

    static function &getPages( &$controller, $reset = false ) {
        parent::getPages( &$controller, $reset = false );
    }

}

?>