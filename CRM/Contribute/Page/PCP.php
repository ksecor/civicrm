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

require_once 'CRM/Core/Page/Basic.php';

/**
 * Page for displaying list of contribution types
 */
class CRM_Contribute_Page_PCP extends CRM_Core_Page_Basic 
{
    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     * @static
     */
    static $_links = null;

    /**
     * Get BAO Name
     *
     * @return string Classname of BAO.
     */
    function getBAOName() 
    {
        return 'CRM_Contribute_BAO_PCP';
    }

    /**
     * Get action Links
     *
     * @return array (reference) of action links
     */
    function &links()
    {
        if (!(self::$_links)) {
            // helper variable for nicer formatting
            $disableExtra = ts('Are you sure you want to disable this contribution type?');

            self::$_links = array(
                                  CRM_Core_Action::VIEW  => array(
                                                                    'name'  => ts('View'),
                                                                    'url'   => 'civicrm/admin/contribute/contributionType',
                                                                    'qs'    => 'action=view&id=%%id%%',
                                                                    'title' => ts('View Personal Campaign Page') 
                                                                    ),                                 
                                  CRM_Core_Action::DELETE  => array(
                                                                    'name'  => ts('Delete'),
                                                                    'url'   => 'civicrm/admin/contribute/contributionType',
                                                                    'qs'    => 'action=delete&id=%%id%%',
                                                                    'title' => ts('Delete Personal Campaign Page') 
                                                                    ),
                                  CRM_Core_Action::ENABLE  => array(
                                                                    'name'  => ts('Approve'),
                                                                    'url'   => 'civicrm/admin/contribute/contributionType',
                                                                    'qs'    => 'action=approve&id=%%id%%',
                                                                    'title' => ts('Approve Personal Campaign Page') 
                                                                    ),
                                  CRM_Core_Action::DISABLE  => array(
                                                                    'name'  => ts('Reject'),
                                                                    'url'   => 'civicrm/admin/contribute/contributionType',
                                                                    'qs'    => 'action=reject&id=%%id%%',
                                                                    'title' => ts('Reject Personal Campaign Page') 
                                                                   )



                                 );
        }
        return self::$_links;
    }

    /**
     * Run the page.
     *
     * This method is called after the page is created. It checks for the  
     * type of action and executes that action.
     * Finally it calls the parent's run method.
     *
     * @return void
     * @access public
     *
     */
    function run()
    {
        // get the requested action
        $action = CRM_Utils_Request::retrieve('action', 'String',
                                              $this, false, 'browse'); // default to 'browse'

        // assign vars to templates
        $this->assign('action', $action);
        $this->edit($action) ;
        
        // parent run 
        parent::run();
    }

    /**
     * Browse all custom data groups.
     *  
     * 
     * @return void
     * @access public
     * @static
     */
    function browse()
    {
        
        require_once 'CRM/Contribute/PseudoConstant.php';
        $status = CRM_Contribute_PseudoConstant::pcpstatus( );
        $contribution_page = CRM_Contribute_PseudoConstant::contributionPage( );

        $pcpSummary = array();
        require_once 'CRM/Contribute/DAO/PCP.php';
        $dao =& new CRM_Contribute_DAO_PCP();

        $dao->orderBy('status_id');
        $dao->find( );

        while ( $dao->fetch() ) {
            $pcpSummary[$dao->id] = array();
            $action = array_sum(array_keys($this->links()));
            CRM_Core_DAO::storeValues( $dao, $pcpSummary[$dao->id]);
            $returnProperities = array('start_date', 'end_date' );
            $params = array( 'id' => $dao->contribution_page_id );
            $values = array( );
            CRM_Core_DAO::commonRetrieve( 'CRM_Contribute_DAO_ContributionPage', $params, $values, $returnProperities );
            
            switch ( $dao->status_id ) 
                {
                    
            case 2:                   
                $action -= CRM_Core_Action::ENABLE;
                break;

            case 3:                   
                $action -= CRM_Core_Action::DISABLE;
                break;
            }

            $pcpSummary[$dao->id]['page_active_from']     = $values['start_date'];
            $pcpSummary[$dao->id]['page_active_until']    = $values['end_date'];
            $pcpSummary[$dao->id]['status_id']            = $status[$dao->status_id];
            $pcpSummary[$dao->id]['contribution_page_id'] = $contribution_page[$dao->contribution_page_id];
            $pcpSummary[$dao->id]['action']               = CRM_Core_Action::formLink(self::links(), $action, 
                                                                                   array('id' => $dao->id));
        }
        $this->assign('rows', $pcpSummary);

    }

    /**
     * Get name of edit form
     *
     * @return string Classname of edit form.
     */
    function editForm() 
    { 
        return 'CRM_Contribute_Form_PCP_PCP';
    }
    
    /**
     * Get edit form name
     *
     * @return string name of this page.
     */
    function editName() 
    {
        return 'Personal Campaign Page';
    }
    
    /**
     * Get user context.
     *
     * @return string user context.
     */
    function userContext($mode = null) 
    {
        return 'civicrm/admin/contribute/pcp';
    }
}


