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
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';
require_once 'CRM/Event/PseudoConstant.php';
require_once 'CRM/Core/BAO/CustomGroup.php';

/**
 * This class generates form components for processing a ontribution 
 * 
 */
class CRM_Event_Form_Participant extends CRM_Core_Form
{
    /**
     * the id of the contribution that we are proceessing
     *
     * @var int
     * @protected
     */
    protected $_id;
    /**
     * the id of the contact associated with this contribution
     *
     * @var int
     * @protected
     */
    protected $_contactID;
    /** 
     * Function to build the form 
     * 
     * @return None 
     * @access public 
     */ 
    public function buildQuickForm( )  
    { 
      $this->applyFilter('__ALL__', 'trim');
      $urlParams = "reset=1&cid={$this->_contactID}&context=event";
      if ( $this->_id ) {
	$urlParams .= "&action=update&id={$this->_id}";
      } else {
	$urlParams .= "&action=add";
      }
      $url = CRM_Utils_System::url( 'civicrm/contact/view/event',
                                      $urlParams, true, null, false ); 
      $this->assign("refreshURL",$url);

      $element =& $this->addElement('select', 'event_type', 
				    ts( 'Event' ), 
				    array(''=>ts( '-select-' )) + CRM_Event_PseudoConstant::event( )
				    );
      
      $element =& $this->add('date', 'registration_date', ts('Registration Date'), CRM_Core_SelectValues::date('manual', 3, 1), false );            
      $element =& $this->add( 'text', 'fee_amount', ts('Fee Amount') );
      
      $session = & CRM_Core_Session::singleton( );
      $uploadNames = $session->get( 'uploadNames' );
      if ( is_array( $uploadNames ) && ! empty ( $uploadNames ) ) {
	$buttonType = 'upload';
      } else {
	$buttonType = 'next';
      }
        
      $this->addButtons(array( 
			      array ( 'type'      => $buttonType, 
				      'name'      => ts('Save'), 
				      'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
				      'isDefault' => true   ), 
			      array ( 'type'      => 'cancel', 
				      'name'      => ts('Cancel') ), 
			      ) 
			);

    }
}

?>
