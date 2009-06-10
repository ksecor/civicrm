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
require_once 'CRM/Report/Utils/Report.php';

/**
 * Page for invoking report instances
 */
class CRM_Report_Page_InstanceList extends CRM_Core_Page 
{
   /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     * @static
     */
    static $_links = null;

    public static function &info( $ovID = null ) {

        $report = '';
        if ( $ovID ) {
            $report = " AND v.id = {$ovID} ";
        }
        $sql = "
        SELECT inst.id, inst.title, inst.report_id, inst.description, v.label, v.component_id
          FROM civicrm_option_group g,
               civicrm_option_value v,
               civicrm_report_instance inst
            
         WHERE v.option_group_id = g.id AND
               g.name      = 'report_template'   AND
               v.value     = inst.report_id  AND
               v.is_active = 1
               {$report}

         ORDER By  v.weight
        ";
        $dao = CRM_Core_DAO::executeQuery( $sql );

        $query        = "SELECT id, name FROM civicrm_component ";
        $componentDAO = CRM_Core_DAO::executeQuery( $query );

        $component    = array();
        while ( $componentDAO->fetch( ) ) {
            //use component name CiviContribute as Contribute same for
            //other component
            $component[$componentDAO->id] = substr($componentDAO->name, 4 ); 
        }
                
        $rows = array();
        while ( $dao->fetch( ) ) {
            if ( trim( $dao->title ) ) {
                $url = 'civicrm/report/instance';
                $compName = 'Contact';
                if ( $dao->component_id ) {
                    $compName = $component[$dao->component_id];
                }
                $rows[$compName][$dao->id]['title'] = $dao->title;               
                $rows[$compName][$dao->id]['label'] = $dao->label;
                $rows[$compName][$dao->id]['description'] = $dao->description;               
                $rows[$compName][$dao->id]['url'] = CRM_Utils_System::url( $url, "reset=1&id={$dao->id}");
                if ( CRM_Core_Permission::check( 'access CiviReport' ) ) {
                    $rows[$compName][$dao->id]['deleteUrl'] = 
                        CRM_Utils_System::url( $url, "action=delete&reset=1&id={$dao->id}");
                }
            }
        }
        return $rows;
    }

    /**
     * Browse all Report Instance List.
     *
     * @return content of the parents run method
     *
     */
    function browse()
    {
        //option value ID of the Report
        $ovID = CRM_Utils_Request::retrieve( 'ovid', 'Positive', $this );
        $rows =& self::info( $ovID );
        $this->assign('list', $rows);
        $templateUrl  = CRM_Utils_System::url('civicrm/report/template/list', "reset=1");
        $this->assign( 'templateUrl', $templateUrl );        
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
