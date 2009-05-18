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
 * Page for displaying list of Reprots available
 */
class CRM_Report_Page_List extends CRM_Core_Page 
{


    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     * @static
     */
    static $_links = null;

    public static function &info( ) {
        $sql = "
                SELECT v.value, v.description, v.name
                FROM   civicrm_option_group g,
                       civicrm_option_value v
                WHERE  v.option_group_id = g.id AND
                       g.name = 'report_list'   AND
                       v.is_active = 1
                ORDER By  v.weight
               ";
        $dao = CRM_Core_DAO::executeQuery( $sql,
                                           CRM_Core_DAO::$_nullArray );

        $rows = array();
        while ( $dao->fetch( ) ) {
            if ( trim( $dao->description ) ) {
                $url = 'civicrm/report/';
                $rows[$dao->value][] = $dao->description;
                $temp = explode( '_', $dao->name );
                if ( $val = CRM_Utils_Array::value( 3, $temp ) ) {
                    $val{0} = strtolower($val{0});
                    $url   .= $val;
                }
                if ( $val = CRM_Utils_Array::value( 4, $temp ) ) {
                    $val{0} = strtolower($val{0});
                    $url   .= '/' . $val;
                }
                $rows[$dao->value][] = CRM_Utils_System::url( $url, 'reset=1');
            }
        }
        return $rows;
    }

    /**
     * Browse all Report List.
     *
     * @return content of the parents run method
     *
     */
    function browse()
    {
        CRM_Utils_System::setTitle( ts('Reports') );
        $rows =& self::info( );
        $this->assign('list', $rows);
        return parent::run();
    }

    /**
     * run this page (figure out the action needed and perform it).
     *
     * @return void
     */
    function run() {
        $action = CRM_Utils_Request::retrieve( 'action',
                                               'String',
                                               $this, false, 'browse' );

        $this->assign( 'action', $action );
        $this->browse( );
    }
}
?>