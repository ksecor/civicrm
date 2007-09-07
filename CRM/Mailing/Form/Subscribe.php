<?php 

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
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

require_once 'CRM/Core/Form.php';

class CRM_Mailing_Form_Subscribe extends CRM_Core_Form
{
    protected $_groupID = null;

    function preProcess( ) 
    { 
        parent::preProcess( );
        $this->_groupID = CRM_Utils_Request::retrieve( 'gid', 'Integer', $this );

        if ( $this->_groupID ) {
            $this->assign( 'groupID'  , $this->_groupID );
            $this->assign( 'groupName',
                           CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Group',
                                                        $this->_groupID,
                                                        'title' ) );
            $this->assign( 'single', true  );
        } else {
            $this->assign( 'single', false );
        }
    }

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */

    public function buildQuickForm( ) 
    {
        // add the email address
        $this->add( 'text',
                    'email',
                    ts( 'Email' ),
                    CRM_Core_DAO::getAttribute('CRM_Core_DAO_Email',
                                               'email' ),
                    true );
        $this->addRule( 'email', ts('Email is not valid.'), 'email' );

        if ( ! $this->_groupID ) {
            // create a selector box of all public groups
            $groups =& CRM_Core_PseudoConstant::staticGroup( true );
            $groups =  array_flip( $groups );
            $this->addCheckbox( 'group_id',
                                ts( 'Mailing Lists' ),
                                $groups,
                                null, null, true,
                                null, array( '&nbsp;&nbsp;',
                                             '&nbsp;&nbsp;',
                                             '<br/>' ) );
        }

        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Subscribe'),
                                         'isDefault' => true   ),
                                 array ( 'type'       => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                                 
                           );
    }
    
    /**
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {    
        $params = $this->controller->exportValues( $this->_name );

        if ( $this->_groupID ) {
            $groups = array( $this->_groupID );
        } else {
            $groups = array_keys( $params['group_id'] );
        }

        require_once 'CRM/Mailing/Event/BAO/Subscribe.php';
        $domainID = CRM_Core_Config::domainID( );
        $success = true;
        foreach ( $groups as $groupID ) {
            $se = CRM_Mailing_Event_BAO_Subscribe::subscribe( $domainID,
                                                              $groupID,
                                                              $params['email'] );
            if ( $se !== null ) {
                /* Ask the contact for confirmation */
                $se->send_confirm_request($params['email']);
            } else {
                $success = false;
            }
        }
    }//end of function

}
?>
