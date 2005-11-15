<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
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
 * @copyright Social Source Foundation (c) 2005
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

        $dupematch               =& new CRM_Core_DAO_DupeMatch( );
        $dupematch->domain_id    = CRM_Core_Config::domainID( );
        $dupematch-> find(true);
        $id = $dupematch->id;

        $this->_id = $id;
        $this->_advanced = CRM_Utils_Request::retrieve( 'advance', $this, false );
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
        if ($this->_action & CRM_Core_Action::DELETE ) { 
            
            return;
        }
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
        if($this->_action & CRM_Core_Action::DELETE) {
            if(CRM_Core_BAO_DupeMatch::del($this->_id)) {
                CRM_Core_Session::setStatus( ts('Selected DupeMatch type has been deleted.') );
            } else {
                CRM_Core_Session::setStatus( ts('Selected DupeMatch type has not been deleted.') );
            }
        } else {
            
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
                $rule = trim($params['match_on']);
                // need to do proper validation
                $dupematch = CRM_Core_BAO_DupeMatch::add($rule);
            }
            CRM_Core_Session::setStatus(ts('The DupeMatch rule has been saved.'));
        }
    }
}

?>
