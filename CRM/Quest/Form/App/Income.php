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
        $personDetails = $this->get('personDetails');
        $params['source_1_id'] = $params['type_of_income_id_1']; 
        $params['source_2_id'] = $params['type_of_income_id_2']; 
        $params['person_id']   = $personDetails[$this->_name];
        
        $this->_incomeIDs = $this->get( 'incomeIDs' );
        $ids = array();
        if ( $this->_incomeIDs[$this->_name] ) {
            $ids['id'] = $this->_incomeIDs[$this->_name];
        }

        require_once 'CRM/Quest/BAO/Income.php';
        $income = CRM_Quest_BAO_Income::create( $params , $ids );
        $this->_incomeIDs[$this->_name] = $income->id;
        $this->set('incomeIDs' , $this->_incomeIDs);
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

}

?>