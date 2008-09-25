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

require_once 'CRM/Contribute/DAO/PCP.php';
require_once 'CRM/Contribute/DAO/PCPBlock.php';
require_once 'CRM/Contribute/DAO/Contribution.php';

class CRM_Contribute_BAO_PCP extends CRM_Contribute_DAO_PCP
{

    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     * @static
     */
    static $_pcpLinks = null;

    function __construct()
    {
        parent::__construct();
    }

    /**
     * function to add the Personal Campaign Page Block
     *
     * @param array $params reference array contains the values submitted by the form
     * @param array $ids    reference array contains the id     * 
     * @access public
     * @static 
     * @return object
     */
    static function add(&$params) 
    {
        if (! isset($params['MAX_FILE_SIZE']) ) {
            // action is taken depending upon the mode
            require_once 'CRM/Contribute/DAO/PCPBlock.php';
            $dao              =& new CRM_Contribute_DAO_PCPBlock( );
            $dao->copyValues( $params );
            $dao->save( );
            return $dao;
        } else {
            require_once 'CRM/Contribute/DAO/PCP.php';
            $dao              =& new CRM_Contribute_DAO_PCP( );
            $dao->copyValues( $params );
            $dao->save( );
            return $dao;
        }
    }
    
    /**
     * function to get the Display  name of a contact for a PCP
     *
     * @param  int    $id      id for the PCP
     *
     * @return null|string     Dispaly name of the contact if found
     * @static
     * @access public
     */
    static function displayName( $id ) 
    {
        $id = CRM_Utils_Type::escape( $id, 'Integer' );
        
        $query = "
SELECT civicrm_contact.display_name
FROM   civicrm_pcp, civicrm_contact
WHERE  civicrm_pcp.contact_id = civicrm_contact.id
  AND  civicrm_pcp.id = {$id}
";
        return CRM_Core_DAO::singleValueQuery( $query, CRM_Core_DAO::$_nullArray );
    }

    /**
     * Function to return PCP  Block info for dashboard
     * 
     * @return array     array of Pcp if found
     * @access public
     * @static
     */
    static function getPcpDashboardInfo( ) 
    {
        require_once 'CRM/Contribute/PseudoConstant.php';
        $query = "
        SELECT pg.id as pageId, pg.title as pageTitle, pg.start_date , 
                  pg.end_date, pcp.id as pcpId, pcp.title as pcpTitle, pcp.status_id as pcpStatus 
        FROM civicrm_contribution_page pg 
        LEFT JOIN civicrm_pcp pcp ON  (pg.id= pcp.contribution_page_id)
        LEFT JOIN civicrm_pcp_block as pcpblock ON ( pg.id = pcpblock.entity_id )
        WHERE pcpblock.is_active = 1";
        $pcpInfo = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        $pcpBlock = array();
        $links = self::pcpLinks();
        $mask  = 0;
        $pcpStatus = CRM_Contribute_PseudoConstant::pcpStatus( );
        while ( $pcpInfo->fetch( ) ) {
            if ( $links ) {
                $replace = array( 
                                 'pageId' => $pcpInfo->pageId, 
                                 'pcpId'  => $pcpInfo->pcpId,
                                 );
            }
            $pcpLink = $links['add'];
            if ( $pcpInfo->pcpStatus ) {
                $pcpLink = $links['all'];
            } 
            $action = CRM_Core_Action::formLink( $pcpLink , $mask, $replace );
            $pcpBlock[] = array ( 'pageId'     => $pcpInfo->pageId,
                                  'pageTitle'  => $pcpInfo->pageTitle,
                                  'start_date' => $pcpInfo->start_date,
                                  'end_date'   => $pcpInfo->end_date,
                                  'pcpId'      => $pcpInfo->pcpId,
                                  'pcpTitle'   => $pcpInfo->pcpTitle,
                                  'pcpStatus'  => $pcpStatus[$pcpInfo->pcpStatus],
                                  'status'     => $pcpInfo->pcpStatus,
                                  'action'     => $action
                                  );
        }
        return  $pcpBlock;
    } 
    
    /**
     * Get action links
     *
     * @return array (reference) of action links
     * @static
     */
    static function &pcpLinks()
    {
        if (! ( self::$_pcpLinks ) ) {
            self::$_pcpLinks['add']  = array (
                                              CRM_Core_Action::ADD => array( 'name'  => ts('Configure'),
                                                                             'url'   => 'civicrm/contribute/campaign',
                                                                             'qs'    => 'action=add&reset=1&pageId=%%pageId%%',
                                                                             'title' => ts('Configure')
                                                                             )
                                              );
            
            self::$_pcpLinks['all'] = array (
                                             CRM_Core_Action::UPDATE => array ( 'name'  => ts('Edit'),
                                                                                'url'   => 'civicrm/contribute/campaign',
                                                                                'qs'    => 'action=update&reset=1&id=%%pcpId%%',
                                                                                'title' => ts('Configure')
                                                                                ),
                                             CRM_Core_Action::VIEW   => array ( 'name'  => ts('View'),
                                                                                'url'   => 'civicrm/contribute/view/campaign',
                                                                                'qs'    => 'reset=1&id=%%pcpId%%',
                                                                                'title' => ts('View')
                                                                                ),
                                             CRM_Core_Action::DETACH => array ( 'name'  => ts('Tell a Friend'),
                                                                                'url'   => 'civicrm/contact/tellafriend',
                                                                                'qs'    => 'reset=1&id=%%pcpId%%',
                                                                                'title' => ts('Tell a Friend')
                                                                                ),
                                             );
        }
        return self::$_pcpLinks;
    }
}
?>