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
class CRM_Quest_Form_App_Income extends CRM_Quest_Form_App
{
    protected $_personID;
    protected $_incomeID;

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        parent::preProcess();

        $this->_contactId = $this->get('contact_id');
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
            $dao->person_id = $this->_incomeID;
            if ($dao->find(true)) {
                CRM_Core_DAO::storeValues( $dao , $defaults );
            }
            
            //for type of income
            $defaults['type_of_income_id_1'] = $defaults['source_1_id'];
            $defaults['type_of_income_id_2'] = $defaults['source_2_id'];
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

        for ( $i = 1; $i <= 3; $i++ ) {
            if ( $i < 2) {
                $this->addSelect( 'type_of_income', ts( 'Type of Income' ), "_$i" ,true );
                $this->addElement( 'text', "amount_$i",
                               ts( 'Total 2005 income from this source' ),
                               $attributes['amount_1'] );
                $this->addRule("amount_$i","Pleae enter total 2005 income from this source",'required');
            } else {
                $this->addSelect( 'type_of_income', ts( 'Type of Income' ), "_$i");
                $this->addElement( 'text', "amount_$i",
                               ts( 'Total 2005 income from this source' ),
                               $attributes['amount_1'] );
                
            }
            $this->addElement( 'text', "job_$i",
                               ts( 'Job Description (if applicable)' ),
                               $attributes['job_1'] );
            
            $this->addElement( 'text', "amount_$i",
                               ts( 'Total 2005 income from this source' ),
                               $attributes['amount_1'] );
        }

        // if this is the last form, add another income source button
        if ( $this->_options['lastSource'] ) {
            $this->add( 'checkbox', "another_income_source", ts( "Add another income source?" ), ts( "Add another income source?" ) );
        }

        parent::buildQuickForm();
            
    }//end of function

    /** 
     * process the form after the input has been submitted and validated 
     * 
     * @access public 
     * @return void 
     */ 
    public function postProcess()  
    {
        $params  = $this->controller->exportValues( $this->_name );

        $params['source_1_id'] = $params['type_of_income_id_1']; 
        $params['source_2_id'] = $params['type_of_income_id_2'];

        if ( ! $this->_personID ) {
            $personParams = array( );
            $personParams['first_name'] = $params['first_name'];
            $personParams['last_name']  = $params['last_name'];
            $personParams['contact_id'] = $this->get( 'contact_id' );
            $relationship = CRM_Core_OptionGroup::values( 'relationship' );
            $personParams['relationship_id'] = array_search( 'Other', $relationship );
            require_once 'CRM/Quest/BAO/Person.php';
            $person = CRM_Quest_BAO_Person::create( $personParams , $ids );
            $this->_personID = $person->id;
        }

        $params['person_id']   = $this->_personID;
        
        $ids['id'] = $this->_incomeID;

        require_once 'CRM/Quest/BAO/Income.php';
        $income = CRM_Quest_BAO_Income::create( $params , $ids );

        $details = $this->controller->get( 'incomeDetails' );
        $details[ "Income-{$this->_personID}"] =
            array( 'className' => 'CRM_Quest_Form_App_Income',
                   'title'     => "{$params['first_name']} {$params['last_name']} Income Details",
                   'options'   => array( 'personID'   => $this->_personID,
                                         'incomeID'   => $income->id,
                                         'lastSource' => false ) );

        if ( CRM_Utils_Array::value( 'another_income_source', $params ) ) {
            $details['NewSource'] = array( 'className' => 'CRM_Quest_Form_App_Income',
                                           'title'     => 'Add an Income Source',
                                           'options'   => array( 'personID'   => null,
                                                                 'incomeID'   => null,
                                                                 'lastSource' => true ) );
        } else {
            $keys = array_keys( $details );
            $last = array_pop( $keys );
            $details[$last]['options']['lastSource'] = true;
        }
        $this->controller->set( 'incomeDetails', $details );
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

    static function &getPages( &$controller ) {
        $details = $controller->get( 'incomeDetails' );

        if ( ! $details ) {
            $cid = $controller->get( 'contact_id' ); 
            $last = null;
            require_once 'CRM/Quest/DAO/Income.php';
            $dao =& new CRM_Quest_DAO_Income( );
            $dao->contact_id = $cid;
            $dao->find( );

            $details = array( );
            while ( $dao->fetch( ) ) {
                require_once 'CRM/Quest/DAO/Person.php';
                $person =& new CRM_Quest_DAO_Person();
                $person->contact_id = $cid; 
                $person->id = $dao->person_id;
                if ( $person->find( true ) ) {
                    $details[ "Income-{$dao->person_id}"] =
                        array( 'className' => 'CRM_Quest_Form_App_Income',
                               'title'     => "{$person->first_name} {$person->last_name} Income Details",
                               'options'   => array( 'personID'   => $person->id,
                                                     'incomeID'   => $dao->id,
                                                     'lastSource' => false ) );
                    $last = "Income-{$dao->person_id}";
                } else {
                    CRM_Core_Error::fatal( "Database is inconsistent" );
                }
            }

            // now get all parent/guardians that have some income
            require_once 'CRM/Quest/DAO/Person.php';
            require_once 'CRM/Quest/Form/App/Guardian.php';
            $dao =& new CRM_Quest_DAO_Person( );
            $dao->contact_id = $cid;
            $dao->is_parent_guardian = true;
            $dao->find( );
            while ( $dao->fetch( ) ) {
                if ( ! CRM_Utils_Array::value( "Income-{$dao->id}", $details ) &&
                     $dao->industry_id &&
                     $dao->industry_id != CRM_Quest_Form_App_Guardian::INDUSTRY_UNEMPLOYED ) {
                    $details[ "Income-{$dao->person_id}"] =
                        array( 'className' => 'CRM_Quest_Form_App_Income',
                               'title'     => "{$dao->first_name} {$dao->last_name} Income Details",
                               'options'   => array( 'personID'   => $dao->id,
                                                     'incomeID'   => null,
                                                     'lastSource' => false ) );
                    $last = "Income-{$dao->person_id}";
                }
            }
            $details[$last]['options']['lastSource'] = true;
            $controller->set( 'incomeDetails', $details );
        }

        if ( empty( $details ) ) {
            // dont store this in session, always add at end
            $details['NewSource'] = array( 'className' => 'CRM_Quest_Form_App_Income',
                                           'title'     => 'Add an Income Source',
                                           'options'   => array( 'personID'   => null,
                                                                 'incomeID'   => null,
                                                                 'lastSource' => true ) );
        }

        return $details;
    }
    
}

?>