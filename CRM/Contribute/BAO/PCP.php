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
    static function getPcpDashboardInfo( $contactId ) 
    {
        $links = self::pcpLinks();
        $mask  = 0;
        require_once 'CRM/Contribute/PseudoConstant.php';
        $query = "
        SELECT pg.id as pageId, pg.title as pageTitle, pg.start_date , 
                  pg.end_date 
        FROM civicrm_contribution_page pg 
        LEFT JOIN civicrm_pcp_block as pcpblock ON ( pg.id = pcpblock.entity_id )
        WHERE pcpblock.is_active = 1";

        $pcpBlockDao = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        $pcpBlock    = array();
        while ( $pcpBlockDao->fetch( ) ) {
            if ( $links ) {
                $replace = array( 'pageId' => $pcpBlockDao->pageId );
            }      
            $pcpLink = $links['add'];
            $action = CRM_Core_Action::formLink( $pcpLink , $mask, $replace );
            $pcpBlock[] = array ( 'pageId'     => $pcpBlockDao->pageId,
                                  'pageTitle'  => $pcpBlockDao->pageTitle,
                                  'start_date' => $pcpBlockDao->start_date,
                                  'end_date'   => $pcpBlockDao->end_date,
                                  'action'     => $action
                                  );
        }

        $query = "
        SELECT pg.start_date, pg.end_date, pcp.id as pcpId, pcp.title as pcpTitle, pcp.status_id as pcpStatus, 
               pcpblock.is_tellfriend_enabled as tellfriend, pcpblock.id as blockId, pcp.is_active as pcpActive
        FROM civicrm_contribution_page pg 
        LEFT JOIN civicrm_pcp pcp ON  (pg.id= pcp.contribution_page_id)
        LEFT JOIN civicrm_pcp_block as pcpblock ON ( pg.id = pcpblock.entity_id )
        INNER JOIN civicrm_contact as ct ON (ct.id = pcp.contact_id  AND pcp.contact_id = {$contactId})
        WHERE pcpblock.is_active = 1";

        $pcpInfoDao = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        $pcpInfo = array();
        $hide = $mask = array_sum( array_keys( $links['all'] ) );
        
        $pcpStatus = CRM_Contribute_PseudoConstant::pcpStatus( );
        while ( $pcpInfoDao->fetch( ) ) {

            $mask = $hide;
            if ( $links ) {
                $replace = array( 'pcpId'    => $pcpInfoDao->pcpId, 
                                  'pcpBock'  => $pcpInfoDao->blockId);
            }
            $pcpLink = $links['all'];
            if ( ! $pcpInfoDao->tellfriend || $pcpInfoDao->pcpStatus != 2 ||  $pcpInfoDao->pcpActive != 1 ) {
                $mask -= CRM_Core_Action::DETACH;
            }
            if ( $pcpInfoDao->pcpActive == 1 ) {
                $mask -= CRM_Core_Action::ENABLE;
            } else {
                $mask -= CRM_Core_Action::DISABLE;
            }
            $action  = CRM_Core_Action::formLink( $pcpLink , $mask, $replace );
            $pcpInfo[] = array ( 
                                 'start_date' => $pcpInfoDao->start_date,
                                 'end_date'   => $pcpInfoDao->end_date,
                                 'pcpId'      => $pcpInfoDao->pcpId,
                                 'pcpTitle'   => $pcpInfoDao->pcpTitle,
                                 'pcpStatus'  => $pcpStatus[$pcpInfoDao->pcpStatus],
                                 'status'     => $pcpInfoDao->pcpStatus,
                                 'action'     => $action
                                  );
        }
        return  array( $pcpBlock, $pcpInfo );
    } 
    
    /**
     * function to show the total amount for Personal Campaign Page on thermometer
     *
     * @param array $pcpId  contains the pcp ID
     * 
     * @access public
     * @static 
     * @return total amount
     */
    static function thermoMeter( $pcpId ) 
    {
        $query = "
     SELECT SUM(cc.total_amount) as total
     FROM civicrm_pcp pcp LEFT JOIN 
          civicrm_contribution cc ON ( pcp.id = cc.pcp_made_through_id )
     WHERE pcp.id = {$pcpId} AND cc.contribution_status_id =1 AND cc.is_test = 0";

        return CRM_Core_DAO::singleValueQuery( $query, CRM_Core_DAO::$_nullArray );
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
            $deleteExtra = ts('Are you sure you want to delete this Campaign Page ?\n This Action is Undone !!!');

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
                                             CRM_Core_Action::ENABLE => array ( 'name'  => ts('Enable'),
                                                                                'url'   => 'civicrm/contribute/pcp',
                                                                                'qs'    => 'action=enable&reset=1&id=%%pcpId%%',
                                                                                'title' => ts('Enable')
                                                                                ),
                                             CRM_Core_Action::DISABLE => array ( 'name'  => ts('Disable'),
                                                                                 'url'   => 'civicrm/contribute/pcp',
                                                                                 'qs'    => 'action=disable&reset=1&id=%%pcpId%%',
                                                                                 'title' => ts('Disable')
                                                                                 ),
                                             CRM_Core_Action::DETACH => array ( 'name'  => ts('Tell a Friend'),
                                                                                'url'   => 'civicrm/friend',
                                                                                'qs'    => 'eid=%%pcpId%%&blockId=%%pcpBock%%&reset=1&page=pcp',
                                                                                'title' => ts('Tell a Friend')
                                                                                ),
                                             CRM_Core_Action::DELETE => array ( 'name'  => ts('Delete'),
                                                                                'url'   => 'civicrm/contribute/pcp',
                                                                                'qs'    => 'action=delete&reset=1&id=%%pcpId%%',
                                                                                'extra' => 'onclick = "return confirm(\''. $deleteExtra . '\');"',
                                                                                'title' => ts('Delete')
                                                                                )
                                             );
        }
        return self::$_pcpLinks;
    }

    /**
     * Function to Delete the campaign page
     * 
     * @param int $id campaign page id
     *
     * @return null
     * @access public
     * @static
     *
     */
    function delete ( $id ) 
    {
        require_once 'CRM/Utils/Hook.php';
        CRM_Utils_Hook::pre( 'delete', 'Campaign', $id, CRM_Core_DAO::$_nullArray );
        
        require_once 'CRM/Core/Transaction.php';
        $transaction = new CRM_Core_Transaction( );
        
        // delete from pcp table
        $pcp     =& new CRM_Contribute_DAO_PCP( );
        $pcp->id = $id;
        $pcp->delete( );
       
        $transaction->commit( );
        
        CRM_Utils_Hook::post( 'delete', 'Campaign', $id, $pcp );
    } 

    /**
     * Function to Approve / Reject the campaign page
     * 
     * @param int $id campaign page id
     *
     * @return null
     * @access public
     * @static
     *
     */
    static function setIsActive( $id, $is_active ) {
        switch ($is_active) 
        {
        case 0:
            $is_active = 3;
            break;
            
        case 1:
            $is_active = 2;
            break;
        }
        return CRM_Core_DAO::setFieldValue( 'CRM_Contribute_DAO_PCP', $id, 'status_id', $is_active );
    }

    /**
     * Function to Enable / Disable the campaign page
     * 
     * @param int $id campaign page id
     *
     * @return null
     * @access public
     * @static
     *
     */
    static function setDisable( $id, $is_active ) {
        return CRM_Core_DAO::setFieldValue( 'CRM_Contribute_DAO_PCP', $id, 'is_active', $is_active );
    }

    /**
     * Function to get pcp block is active 
     * 
     * @param int $id campaign page id
     *
     * @return int
     * @access public
     * @static
     *
     */
    static function getStatus( $pcpId ) 
    {
        $query = "
     SELECT pb.is_active 
     FROM civicrm_pcp pcp 
          LEFT JOIN civicrm_pcp_block pb ON ( pcp.contribution_page_id = pb.entity_id )
          LEFT JOIN civicrm_contribution_page as cp ON ( cp.id =  pcp.contribution_page_id )
     WHERE pcp.id = {$pcpId}";
        
        return CRM_Core_DAO::singleValueQuery( $query, CRM_Core_DAO::$_nullArray );
    }

    /**
     * Function to get pcp block is enabled for contribution page 
     * 
     * @param int $id contribution page id
     *
     * @return String
     * @access public
     * @static
     *
     */
    static function getPcpBlockStatus( $pageId ) 
    {
        $query = "
     SELECT pb.link_text as linkText
     FROM civicrm_contribution_page cp 
          LEFT JOIN civicrm_pcp_block pb ON ( cp.id = pb.entity_id AND pb.entity_table = 'civicrm_contribution_page' )
     WHERE pb.is_active = 1 AND cp.id = {$pageId}";
        return CRM_Core_DAO::singleValueQuery( $query, CRM_Core_DAO::$_nullArray );
    }

}
?>