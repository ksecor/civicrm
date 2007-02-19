<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
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
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions      |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2007
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
     * class constructor
     *
     * @param int $id  the contact id
     * @param int $gid the group id
     *
     * @return void
     * @access public
     */
    function __construct( $id, $gid, $restrict ) {
        $this->_id       = $id;
        $this->_gid      = $gid;
        $this->_restrict = $restrict;
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
                                                       null, null, false, $this->_restrict );

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

        return trim( $template->fetch( 'CRM/Profile/Page/Dynamic.tpl' ) ); 
    }

    function getTemplateFileName() {
        if ( $this->_gid ) {
            $templateFile = "CRM/Profile/Page/{$this->_gid}/Dynamic.tpl";
            if ( CRM_Core_Page::$_template->template_exists( $templateFile ) ) {
                return $templateFile;
            }
        }
        return parent::getTemplateFileName( );
    }

}

?>
