<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
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
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
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
        $this->_advanced = CRM_Utils_Request::retrieve( 'advance', $this, false );
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
            
            if( count($rule)>=1 ) {
                $rule = implode(' AND ',$rule)            ;
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
                    CRM_Core_Session::setStatus(ts("The Duplicate Matching rule has not been saved because of the rule is invalid. Rules should contain only valid field names,'AND','OR' or 'parentheses'  )"));
                    return;
                } else {
                    $dupematch = CRM_Core_BAO_DupeMatch::add($rule);
                }
        }
        CRM_Core_Session::setStatus(ts('The Duplicate Matching rule has been saved.'));
    }
}

?>
