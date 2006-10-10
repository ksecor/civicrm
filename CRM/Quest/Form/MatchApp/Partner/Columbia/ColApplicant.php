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
 * Columbia Applicant
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
 * This class generates form components for the columbia applicant
 * 
 */
class CRM_Quest_Form_MatchApp_Partner_Columbia_ColApplicant extends CRM_Quest_Form_App
{
    
    protected $_fields;

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        parent::preProcess();
        require_once 'CRM/Quest/BAO/Essay.php';
        $this->_essays = CRM_Quest_BAO_Essay::getFields( 'cm_partner_columbia_applicant', $this->_contactID, $this->_contactID );        
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
        $defaults['essay'] = array( );

        require_once 'CRM/Quest/Partner/DAO/Columbia.php';
        $dao =& new CRM_Quest_Partner_DAO_Columbia( );
        $dao->contact_id = $this->_contactID;
        if ( $dao->find( true ) ) {
            $dao->storeValues($dao, $defaults);
        }

        $fields = array( 'career' => 'columbia_career', 'interest' => 'columbia_interest');
        foreach( $fields as $key => $field ) {
            if ( $defaults[$key] ) {
                $value = explode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR , $defaults[$key] );
            }
            $defaults[$field] = array();
            if ( is_array( $value ) ) {
                foreach( $value as $v ) {
                    $defaults[$field][$v] = 1;
                }
            }
        }

        require_once "CRM/Quest/BAO/Essay.php";
        CRM_Quest_BAO_Essay::setDefaults( $this->_essays, $defaults['essay'] );

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
        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_Partner_DAO_Columbia');

        $this->addCheckBox( 'columbia_career',
                            ts( 'Select possible careers you see yourself pursuing after schooling (check any that apply)' ),
                            CRM_Core_OptionGroup::values( 'columbia_career', true ),
                            true, '<br/>',true,
                            array ('onclick' => "return showHideByValue('columbia_career[14]', '1', 'career_other', '', 'radio', false);") );

        $this->addElement('text', 'career_other', ts( 'Other Career' ),
                          $attributes['career_other'] );

        $this->addYesNo( 'is_reside_campus', ts( 'Do you wish to reside on campus (Housing is guaranteed to all entering students.) ?' ) ,null,true);
        $this->addYesNo( 'is_parent_fulltime', ts( 'Is either parent a full-time employee of Columbia?' ) ,null,false);
        $this->addYesNo( 'is_financial_aid', ts( 'Are you requesting need-based financial aid from Columbia?' ) ,null,true);
        $this->addYesNo( 'is_visited_campus', ts( 'Have you visited the Columbia campus?' ) ,null,true);

        $this->addCheckBox( 'columbia_interest',
                            ts( 'How has your interest in Columbia developed? (check all that apply) ' ),
                            CRM_Core_OptionGroup::values( 'columbia_interest', true ),
                            true, '<br/>',true,
                            array ('onclick' => "return showHideByValue('columbia_interest[14]', '1', 'interest_other', '', 'radio', false);") );

        $this->addElement('text', 'interest_other', ts( 'Other Interest' ),
                          $attributes['interest_other'] );

        CRM_Quest_BAO_Essay::buildForm( $this, $this->_essays );
        
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
        return ts( 'Columbia University' );
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
        //CRM_Core_Error::debug('d', $params);
        require_once 'CRM/Quest/Partner/DAO/Columbia.php';
        $dao =& new CRM_Quest_Partner_DAO_Columbia( );
        $dao->contact_id = $this->_contactID;
        $dao->find( true );

        $dao->copyValues( $params );
        
        if ( $params['columbia_career'] ) {
            $dao->career = implode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR,array_keys($params['columbia_career']));
            //CRM_Core_Error::debug('ss', $dao->career);
        }

        if ( $params['columbia_interest'] ) {
            $dao->interest = implode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR,array_keys($params['columbia_interest']));
        }

        $dao->save( );

        CRM_Quest_BAO_Essay::create( $this->_essays, $params['essay'], $this->_contactID, $this->_contactID );
                
        parent::postProcess( );
    } 
   
}

?>