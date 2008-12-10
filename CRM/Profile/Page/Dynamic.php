<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';

/**
 * Create a page for displaying CiviCRM Profile Fields.
 *
 * Heart of this class is the run method which checks
 * for action type and then displays the appropriate
 * page.
 *
 */
class CRM_Profile_Page_Dynamic extends CRM_Core_Page {
    
    /**
     * The contact id of the person we are viewing
     *
     * @var int
     * @access protected
     */
    protected $_id;

    /**
     * the profile group are are interested in
     * 
     * @var int 
     * @access protected 
     */ 
    protected $_gid;

    /**
     * The profile types we restrict this page to display
     *
     * @var string
     * @access protected
     */
    protected $_restrict;

    /**
     * Should we bypass permissions
     *
     * @var boolean
     * @access prootected
     */
    protected $_skipPermission;

    /**
     * class constructor
     *
     * @param int $id  the contact id
     * @param int $gid the group id
     *
     * @return void
     * @access public
     */
    function __construct( $id, $gid, $restrict, $skipPermission = false ) {
        $this->_id       = $id;
        $this->_gid      = $gid;
        $this->_restrict = $restrict;
        $this->_skipPermission = $skipPermission;

        parent::__construct( );
    }

    /**
     * Get the action links for this page.
     *
     * @return array $_actionLinks
     *
     */
    function &actionLinks()
    {
        return null;
    }
    
    /**
     * Run the page.
     *
     * This method is called after the page is created. It checks for the  
     * type of action and executes that action. 
     *
     * @return void
     * @access public
     *
     */
    function run()
    {
        $template =& CRM_Core_Smarty::singleton( ); 
        if ( $this->_id && $this->_gid ) {
            require_once 'CRM/Core/BAO/UFGroup.php';

            $values = array( );
            $fields = CRM_Core_BAO_UFGroup::getFields( $this->_gid, false, CRM_Core_Action::VIEW,
                                                       null, null, false, $this->_restrict, $this->_skipPermission );

            // make sure we dont expose all fields based on permission
            $admin = false; 
            $session  =& CRM_Core_Session::singleton( ); 
            if ( CRM_Core_Permission::check( 'administer users' ) || 
                 $this->_id == $session->get( 'userID' ) ) { 
                $admin = true; 
            }

            if ( ! $admin ) {
                foreach ( $fields as $name => $field ) {
                    // make sure that there is enough permission to expose this field 
                    if ( $field['visibility'] == 'User and User Admin Only' ) {
                        unset( $fields[$name] );
                    }
                }
            }
            CRM_Core_BAO_UFGroup::getValues( $this->_id, $fields, $values );

            $template->assign_by_ref( 'row', $values );
        }
        return trim( $template->fetch(  $this->getTemplateFileName( ) ) );
    }

    function getTemplateFileName() {
        if ( $this->_gid ) {
            $templateFile = "CRM/Profile/Page/{$this->_gid}/Dynamic.tpl";
            $template     =& CRM_Core_Page::getTemplate( );
            if ( $template->template_exists( $templateFile ) ) {
                return $templateFile;
            }
        }
        return parent::getTemplateFileName( );
    }

}


