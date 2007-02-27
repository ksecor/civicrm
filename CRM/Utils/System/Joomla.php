<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                  |
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
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

/**
 * Joomla specific stuff goes here
 */
class CRM_Utils_System_Joomla {

    /**
     * sets the title of the page
     *
     * @param string $title title to set
     *
     * @return void
     * @access public
     */
    function setTitle( $title ) {
        $template =& CRM_Core_Smarty::singleton( );
        $template->assign( 'pageTitle', $title );
        
        global $mainframe;
        if ( $mainframe ) {
            $mainframe->setPageTitle( $title );
        }

        return;
    }

    /**
     * Append an additional breadcrumb tag to the existing breadcrumb
     *
     * @param string $bc the new breadcrumb to be appended
     *
     * @return void
     * @access public
     * @static
     */
    static function appendBreadCrumb( $bc ) {
        return;
    }

    /**
     * Reset an additional breadcrumb tag to the existing breadcrumb
     *
     * @param string $bc the new breadcrumb to be appended
     *
     * @return void
     * @access public
     * @static
     */
    static function resetBreadCrumb( $bc ) {
        return;
    }

    /**
     * Append a string to the head of the html file
     *
     * @param string $head the new string to be appended
     *
     * @return void
     * @access public
     * @static
     */
    static function addHTMLHead( $head ) {
        return;
    }

    /**
     * Generate an internal CiviCRM URL
     *
     * @param $path     string   The path being linked to, such as "civicrm/add"
     * @param $query    string   A query string to append to the link.
     * @param $absolute boolean  Whether to force the output to be an absolute link (beginning with http:).
     *                           Useful for links that will be displayed outside the site, such as in an
     *                           RSS feed.
     * @param $fragment string   A fragment identifier (named anchor) to append to the link.
     * @param $htmlize  boolean  whether to convert to html eqivalant
     *
     * @return string            an HTML string containing a link to the given path.
     * @access public
     *
     */
    function url($path = null, $query = null, $absolute = true, $fragment = null, $htmlize = true ) {
        $config        =& CRM_Core_Config::singleton( );

        if ( $config->userFrameworkFrontend ) {
            $script = 'index.php';
        } else {
            $script = 'index2.php';
        }

        if (isset($fragment)) {
            $fragment = '#'. $fragment;
        }

        //$base = ($absolute ? $config->httpBase : '');
        $base = ($absolute ? $config->userFrameworkBaseURL : '');

        if ( isset( $query ) ) {
            return $base . $script .'?option=com_civicrm&task=' . $path .'&'. $query . $fragment;
        } else {
            return $base . $script .'?option=com_civicrm&task=' . $path . $fragment;
        }
    }

    /** 
     * rewrite various system urls to https 
     * 
     * @return void 
     * access public  
     * @static  
     */  
    static function mapConfigToSSL( ) {
        global $mosConfig_live_site;
        $mosConfig_live_site = str_replace( 'http://', 'https://', $mosConfig_live_site );
    }

    /**
     * figure out the post url for the form
     *
     * @param $action the default action if one is pre-specified
     *
     * @return string the url to post the form
     * @access public
     * @static
     */
    function postURL( $action ) {
        if ( ! empty( $action ) ) {
            return $action;
        }

        return self::url( $_GET['task'] );
    }

    /**
     * Function to set the email address of the user
     *
     * @param object $user handle to the user object
     *
     * @return void
     * @access public
     */
    function setEmail( &$user ) {
        global $database;
        $query = "SELECT email FROM #__users WHERE id='$user->id'";
        $database->setQuery( $query );
        $user->email = $database->loadResult();
    }

    /**
     * Authenticate the user against the joomla db
     *
     * @param string $name     the user name
     * @param string $password the password for the above user name
     *
     * @return mixed false if no auth
     *               array( contactID, ufID, unique string ) if success
     * @access public
     * @static
     */
    static function authenticate( $name, $password ) {
        require_once 'DB.php';

        $config =& CRM_Core_Config::singleton( );
        
        $dbJoomla = DB::connect( $config->userFrameworkDSN );
        if ( DB::isError( $dbJoomla ) ) {
            CRM_Core_Error::fatal( "Cannot connect to drupal db via $config->userFrameworkDSN, " . $dbJoomla->getMessage( ) ); 
        }                                                      

        $password  = md5( $password );
        $name      = strtolower( $name );
        $sql = 'SELECT u.* FROM ' . $config->userFrameworkUsersTableName .
            " u WHERE LOWER(u.username) = '$name' AND u.password = '$password'";
        $query = $dbJoomla->query( $sql );

        $user = null;
        // need to change this to make sure we matched only one row
        require_once 'CRM/Core/BAO/UFMatch.php';
        while ( $row = $query->fetchRow( DB_FETCHMODE_ASSOC ) ) { 
            CRM_Core_BAO_UFMatch::synchronizeUFMatch( $user, $row['id'], $row['email'], 'Drupal' );
            $contactID = CRM_Core_BAO_UFMatch::getContactId( $row['id'] );
            if ( ! $contactID ) {
                return false;
            }
            return array( $contactID, $row['id'], mt_rand() );
        }
        return false;
    }

    /**    
     * Set a message in the UF to display to a user  
     *    
     * @param string $message  the message to set  
     *    
     * @access public    
     * @static    
     */    
    static function setMessage( $message ) { 
        return;
    }

}

?>
