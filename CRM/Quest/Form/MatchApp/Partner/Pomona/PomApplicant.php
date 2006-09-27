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
 * Pomona Applicant
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
 * This class generates form components for the pomona application
 * 
 */
class CRM_Quest_Form_MatchApp_Partner_Pomona_PomApplicant extends CRM_Quest_Form_App
{
    
    protected $_fields;
    protected $_essays; 

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        parent::preProcess();

        $this->_fields =
            array( 'name_1', 'department_1', 'relationship_1',
                   'name_2', 'department_2', 'relationship_2',
                   'name_3', 'department_3', 'relationship_3',
                   'is_broader_context', 'is_factors_work' );

        require_once 'CRM/Quest/BAO/Essay.php';
        $this->_essays = CRM_Quest_BAO_Essay::getFields( 'cm_partner_pomona_applicant', $this->_contactID, $this->_contactID );
      
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

        require_once 'CRM/Quest/Partner/DAO/Pomona.php';
        $dao =& new CRM_Quest_Partner_DAO_Pomona( );
        $dao->contact_id = $this->_contactID;

        if ( $dao->find( true ) ) {
            foreach ( $this->_fields as $name ) {
                $defaults[$name] = $dao->$name;
            }
        }
        
        $defaults['essay'] = array( );
        CRM_Quest_BAO_Essay::setDefaults( $this->_essays, $defaults );
        
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
        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_Partner_DAO_Pomona');
     
        // add a checkbox and text box for each of the above
        foreach ( $this->_fields as $name ) {
            if ( substr( $name, 0, 3 ) == 'is_' ) {
                continue;
            }
            $this->add( 'text', $name, null, $attributes[$name] );
        }
        
        $attr_bc = array('onclick' => "return showHideByValue('is_broader_context', '1', 'tr_broader_context', 'table-row', 'radio', false);");
        $this->addYesNo( 'is_broader_context', "Is there a broader context in which we should consider your performance and involvements? Are there any external factors we should consider (e.g. family situation, work, sibling childcare responsibilities or other personal circumstances)?", null, true, $attr_bc);
        
        $attr_fw = array('onclick' => "return showHideByValue('is_factors_work', '1', 'tr_factors_work', 'table-row', 'radio', false);");
        $this->addYesNo( 'is_factors_work', "Are there any factors or circumstances that may affect your adjustment to college life or work?", null, true, $attr_fw );
        
        $this->addElement( 'textarea', "broader_context",
                           ts('If yes, please explain:'),"cols=45 rows=5" );
        
        $this->addElement( 'textarea', "factors_work",
                           ts('If yes, please explain:') ,"cols=45 rows=5");
        
        //CRM_Quest_BAO_Essay::buildForm( $this, $this->_essays ); 
        
        $this->assign( 'fields', $this->_fields);       
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
         return ts('Applicant Information');
    }

    public function getRootTitle( ) {
        return ts( 'Pomona College' );
    }

    /** 
     * process the form after the input has been submitted and validated 
     * 
     * @access public 
     * @return void 
     */ 
    public function postProcess() {
        if ( $this->_action &  CRM_Core_Action::VIEW ) {
            return;
        }
        $params = $this->controller->exportValues( $this->_name );
        require_once 'CRM/Quest/Partner/DAO/Pomona.php';
        $dao =& new CRM_Quest_Partner_DAO_Pomona( );
        $dao->contact_id = $this->_contactID;
        $dao->find( true );

        foreach ( $this->_fields as $name ) {
            if ( substr( $name, 0, 3 ) == 'is_' ) {
                $dao->$name = CRM_Utils_Array::value( $name, $params, 0 );
            } else {
                $dao->$name = $params[$name];
            }
        }
        $dao->save( );
       
        CRM_Quest_BAO_Essay::create( $this->_essays, $params,
                                     $this->_contactID, $this->_contactID ); 

        parent::postProcess( );
    } 
   
}

?>