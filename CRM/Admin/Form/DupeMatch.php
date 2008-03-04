<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Admin/Form.php';

/**
 * This class generates form components for DupeMatch
 * 
 */
class CRM_Admin_Form_DupeMatch extends CRM_Admin_Form
{
    /**
     * this variable used to differanciate between basic and advance mode
     *
     */
    protected $_advanced;

    /**
     * Function to pre processing
     *
     * @return None
     * @access public
     */

    function preProcess( ) 
    {
        $this->_BAOName = CRM_Admin_Page_DupeMatch::getBAOName();
        $this->_advanced = CRM_Utils_Request::retrieve( 'advance', 'Boolean',
                                                        $this, false );
        $dupematch               =& new CRM_Core_DAO_DupeMatch( );
        $dupematch->domain_id    = CRM_Core_Config::domainID( );
        $dupematch-> find(true);
        $id = $dupematch->id;
        $rule = $dupematch->rule;
        $tokens = preg_split('/[\s]+/',$rule,-1, PREG_SPLIT_NO_EMPTY );
        $rule = explode(' ' , $rule);
        if(count($tokens) > 9 ) {
            $this->_advanced = true;
        }
        foreach($rule as $value ) {
            if ( $value == 'OR' || $value == '(' || $value == ')') {
                $this->_advanced = true;
            } else if(substr($value,0,1) == '(') {
                $this->_advanced = true;
            }
                
        }
        $this->_id = $id;
        $this->assign('advance',$this->_advanced);
        
    }
    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
       
        if ( $this->_advanced ) {
            $this->addElement('textarea', 'match_on'.$count, ts('Match On:'));
        } else {
            require_once 'CRM/Contact/BAO/Contact.php';
            $fields =& CRM_Contact_BAO_Contact::importableFields('Individual', 1);
            foreach ($fields as $name => $field ) {
                if ( $name == 'note' ) {
                    continue;
                }
                $selectFields    [$name] = $field['title'];
            }
            $this->applyFilter('__ALL__', 'trim');
            for ( $count = 1; $count <= 5 ; $count++ ) { 
                $this->addElement('select', 'match_on_'.$count, ts('Match On:'), $selectFields );
            }
            $this->addFormRule( array( 'CRM_Admin_Form_DupeMatch', 'formRule' ));
        }
        parent::buildQuickForm( );
    }

       
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        require_once 'CRM/Core/BAO/DupeMatch.php';        
        $params = $this->exportValues();

        if( ! $this->_advanced) {
            $rule = array();
            for ( $count = 1; $count <= 5 ; $count++ ) { 
                if( $params['match_on_'.$count] != '' ) {
                    $rule[] = $params['match_on_'.$count] ;
                }
            }
            //updated for CRM-974
            if(count($rule)==0){
                $rule = 'none';
                $dupematch = CRM_Core_BAO_DupeMatch::add($rule);
            } else {
                $rule = implode(' AND ',$rule);
                $dupematch = CRM_Core_BAO_DupeMatch::add($rule);
            }
        } else {
            
            $inValid = false;
            $rule   = trim($params['match_on']);
            $tokens = preg_split('/[\s]+/',$rule,-1, PREG_SPLIT_NO_EMPTY );
            //$tokens = preg_split('/([AND])|([OR()])/',$rule, -1 ,PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
            $openParen  = $closeParen = $andCount = $orCount = $fieldCount  = 0;
            foreach($tokens as $token) {
                $token = trim($token); 
                if ($token == '(') {
                    $openParen++;
                } else if ($token == ')') {
                    $closeParen ++;
                } else if ($token == 'AND') {
                    $andCount++;
                } else if ($token == 'OR') {
                    $orCount++;
                } else {
                    $fieldCount++;
                }
            }
            if(($openParen != $closeParen) || ( $fieldCount-1 != ( $andCount+$orCount )) ) {
                $inValid = true;
            }    
            // need to do proper validation
            $fields =& CRM_Contact_BAO_Contact::importableFields('Individual', 1);
            $ruleFields = preg_split('/[ANDOR()\s]+/',$rule,-1, PREG_SPLIT_NO_EMPTY );
                foreach($ruleFields as $value) {
                    if( isset($value) ){
                        if(! array_key_exists($value,$fields)) {
                            $inValid = true;
                        }
                    }
                }
                if( $inValid ) {
                    CRM_Core_Session::setStatus(ts("The Contact Matching rule has not been saved, because the rule is invalid. Rules should contain only valid field names, 'AND', 'OR' or parentheses."));
                    return;
                } else {
                    $dupematch = CRM_Core_BAO_DupeMatch::add($rule);
                }
        }
        CRM_Core_Session::setStatus(ts('The Contact Matching rule has been saved.'));
    }
    
    /**
     * global validation rules for the form
     *
     * @param   array  $fields   posted values of the form
     *
     * @return  array  list of errors to be posted back to the form
     * @static
     * @access  public
     */
    static function formRule( &$fields ) 
    {
        
        $dupRecords = array();
        $errors     = array();
        foreach ( $fields as $key => $value ) {
            if ( array_key_exists( $value, $dupRecords ) ) {
                $errors[$key] = 'Duplicate value(s) not allowed';
            } elseif ( ! empty( $value ) ) {
                $dupRecords[$value] = 1; 
            }
        }
        
        return empty($errors) ? true : $errors;
    }
}


