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

require_once 'CRM/Core/Page.php';

/**
 * Page for displaying list of pledges
 */
class CRM_PledgeBank_Page_ManagePledgeBank extends CRM_Core_Page
{
    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     * @static
     */
    static $_actionLinks = null;

    static $_links = null;

    protected $_pager = null;

    protected $_sortByCharacter;

    /**
     * Get action Links
     *
     * @return array (reference) of action links
     */
    function &links()
    {
        if (!(self::$_actionLinks)) {
            // helper variable for nicer formatting
            $disableExtra = ts('Are you sure you want to disable this Pledge?');
            $deleteExtra = ts('Are you sure you want to delete this Pledge?');
            $copyExtra = ts('Are you sure you want to make a copy of this Pledge?');

            self::$_actionLinks = array(
                                        CRM_Core_Action::UPDATE  => array(
                                                                          'name'  => ts('Configure'),
                                                                          'url'   => CRM_Utils_System::currentPath( ),
                                                                          'qs'    => 'action=update&id=%%id%%&reset=1',
                                                                          'title' => ts('Configure Pledge') 
                                                                          ),
                                        
                                        CRM_Core_Action::DISABLE => array(
                                                                          'name'  => ts('Disable'),
                                                                          'url'   => CRM_Utils_System::currentPath( ),
                                                                          'qs'    => 'action=disable&id=%%id%%',
                                                                          'extra' => 'onclick = "return confirm(\'' . $disableExtra . '\');"',
                                                                          'title' => ts('Disable Pledge') 
                                                                          ),
                                        CRM_Core_Action::ENABLE  => array(
                                                                          'name'  => ts('Enable'),
                                                                          'url'   => CRM_Utils_System::currentPath( ),
                                                                          'qs'    => 'action=enable&id=%%id%%',
                                                                          'title' => ts('Enable Pledge') 
                                                                          ),
                                        CRM_Core_Action::DELETE  => array(
                                                                          'name'  => ts('Delete'),
                                                                          'url'   => CRM_Utils_System::currentPath( ),
                                                                          'qs'    => 'action=delete&id=%%id%%',
                                                                          'extra' => 'onclick = "return confirm(\'' . $deleteExtra . '\');"',
                                                                          'title' => ts('Delete Pledge') 
                                                                          ),
                                        CRM_Core_Action::COPY     => array(
                                                                           'name'  => ts('Copy Pledge'),
                                                                           'url'   => CRM_Utils_System::currentPath( ),                                                                                                'qs'    => 'reset=1&action=copy&id=%%id%%',
                                                                           'extra' => 'onclick = "return confirm(\'' . $copyExtra . '\');"',
                                                                           'title' => ts('Copy Pledge') 
                                                                          )
                                        );
        }
        return self::$_actionLinks;
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
        $id = CRM_Utils_Request::retrieve('id', 'Positive',
                                          $this, false, 0);
        
        // set breadcrumb to append to 2nd layer pages
        $breadCrumb = array ( array('title' => ts('Manage Pledges'),
                                    'url'   => CRM_Utils_System::url( CRM_Utils_System::currentPath( ), 
                                                                      'reset=1' )) );

        // what action to take ?
        if ( $action & CRM_Core_Action::ADD ) {
            $session =& CRM_Core_Session::singleton( ); 
            
            $title = "New Pledge Wizard";
            $session->pushUserContext( CRM_Utils_System::url( CRM_Utils_System::currentPath( ), 'reset=1' ) );
            CRM_Utils_System::appendBreadCrumb( $breadCrumb );
            CRM_Utils_System::setTitle( $title );
            
            require_once 'CRM/PledgeBank/Controller/ManagePledgeBank.php';
            $controller =& new CRM_PledgeBank_Controller_ManagePledgeBank( );
            return $controller->run( );
        } else if ($action & CRM_Core_Action::UPDATE ) {
            CRM_Utils_System::appendBreadCrumb( $breadCrumb );

            require_once 'CRM/PledgeBank/Page/ManagePledgeBankEdit.php';
            $page =& new CRM_PledgeBank_Page_ManagePledgeBankEdit( );
            return $page->run( );
        } else if ($action & CRM_Core_Action::DISABLE ) {
            require_once 'CRM/PledgeBank/BAO/Pledge.php';
            CRM_PledgeBank_BAO_Pledge::setIsActive($id ,0);
        } else if ($action & CRM_Core_Action::ENABLE ) {
            require_once 'CRM/PledgeBank/BAO/Pledge.php';
            CRM_PledgeBank_BAO_Pledge::setIsActive($id ,1); 
        } else if ($action & CRM_Core_Action::DELETE ) {
            $session =& CRM_Core_Session::singleton();
            $session->pushUserContext( CRM_Utils_System::url( CRM_Utils_System::currentPath( ), 'reset=1&action=browse' ) );
            $controller =& new CRM_Core_Controller_Simple( 'CRM_PledgeBank_Form_ManagePledgeBank_Delete',
                                                           'Delete Pledge',
                                                           $action );
            $id = CRM_Utils_Request::retrieve('id', 'Positive',
                                              $this, false, 0);
            $controller->set( 'id', $id );
            $controller->process( );
            return $controller->run( );
        } else if ($action & CRM_Core_Action::COPY ) {
            $this->copy( );
        }
          
        // finally browse the custom groups
        $this->browse();
        
        // parent run 
        parent::run();
    }
    
    /**
     * Browse all Pledges.
     * 
     * @return void
     * @access public
     * @static
     */
    function browse()
    {
        $this->_sortByCharacter = CRM_Utils_Request::retrieve( 'sortByCharacter',
                                                               'String',
                                                               $this );
        if ( $this->_sortByCharacter == 1 ||
             ! empty( $_POST ) ) {
            $this->_sortByCharacter = '';
            $this->set( 'sortByCharacter', '' );
        }
        
        $this->_force = null;
        $this->_searchResult = null;
        $this->search( );
        
        $config =& CRM_Core_Config::singleton( );
        
        $params = array( );
        $this->_force = CRM_Utils_Request::retrieve( 'force', 'Boolean', $this, false ); 
        $this->_searchResult = CRM_Utils_Request::retrieve( 'searchResult', 'Boolean', $this );
        $whereClause = $this->whereClause( $params, false, $this->_force );
        $this->pagerAToZ( $whereClause, $params );
        
        $params      = array( );
        $whereClause = $this->whereClause( $params, true, $this->_force );
        $this->pager( $whereClause, $params );
        list( $offset, $rowCount ) = $this->_pager->getOffsetAndRowCount( );
        
        //get all pledges sorted by weight
        $managePledge = array();
        
        $query = "
SELECT     civicrm_pb_pledge.id as id, civicrm_pb_pledge.creator_name as creator_name,
           civicrm_pb_pledge.creator_pledge_desc as creator_pledge_desc, 
           civicrm_pb_pledge.signers_limit as signers_limit, civicrm_pb_pledge.signer_description_text as signer_description_text, 
           civicrm_pb_pledge.signer_pledge_desc as signer_pledge_desc, civicrm_pb_pledge.deadline as deadline,
           civicrm_pb_pledge.is_active as is_active, civicrm_contact.display_name as display_name
FROM       civicrm_pb_pledge
LEFT JOIN  civicrm_contact ON ( civicrm_pb_pledge.creator_id = civicrm_contact.id )
WHERE      $whereClause
GROUP BY   civicrm_pb_pledge.id
ORDER BY   civicrm_pb_pledge.deadline ASC
LIMIT      $offset, $rowCount
";
        $dao = CRM_Core_DAO::executeQuery( $query, $params );
        
        $properties = array( 'creatorPledgeDesc'     => 'creator_pledge_desc',     'signersLimit'     => 'signers_limit', 
                             'signerDescriptionText' => 'signer_description_text', 'signerPledgeDesc' => 'signer_pledge_desc', 
                             'deadline'              => 'deadline',                'isActive'         => 'is_active', 
                             'displayName'           => 'display_name',           
                             );
        
        while ($dao->fetch()) {
            $managePledge[$dao->id] = array();
            foreach ( $properties as $property => $name ) {
                $managePledge[$dao->id][$property] = $dao->$name;
            }
            $managePledge[$dao->id]['title'] = ts( '%1 will %2 but only if %3 %4 will %5', 
                                                   array( 1 => $dao->creator_name, 
                                                          2 => $dao->creator_pledge_desc,
                                                          3 => $dao->signers_limit,
                                                          4 => $dao->signer_description_text,
                                                          5 => $dao->signer_pledge_desc ));
            
            //form all action links
            $action = array_sum( array_keys( $this->links( ) ) );
            
            if ($dao->is_active) {
                $action -= CRM_Core_Action::ENABLE;
            } else {
                $action -= CRM_Core_Action::DISABLE;
            }
            
            $managePledge[$dao->id]['action'] = CRM_Core_Action::formLink(self::links(), $action, 
                                                                          array('id' => $dao->id));
            //get the pledge status
            require_once 'CRM/PledgeBank/BAO/Pledge.php';
            $managePledge[$dao->id]['status'] = CRM_PledgeBank_BAO_Pledge::getPledgeStatus( $dao->id ); 
            
        }
        
        $this->assign( 'rows', $managePledge );
    }
    
    /**
     * This function is to make a copy of a Event, including
     * all the fields in the event wizard
     *
     * @return void
     * @access public
     */
    function copy( )
    {
        $id = CRM_Utils_Request::retrieve('id', 'Positive', $this, true, 0, 'GET');
        
        require_once 'CRM/Event/BAO/Event.php';
        CRM_Event_BAO_Event::copy( $id );
        
        return CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm/event/manage', 'reset=1' ) );
    }
    
    
    function search( ) {
        if ( isset($this->_action) &
             ( CRM_Core_Action::ADD    |
               CRM_Core_Action::UPDATE |
               CRM_Core_Action::DELETE ) ) {
            return;
        }
       
        $form = new CRM_Core_Controller_Simple( 'CRM_Event_Form_SearchEvent', ts( 'Search Events' ), CRM_Core_Action::ADD );
        $form->setEmbedded( true );
        $form->setParent( $this );
        $form->process( );
        $form->run( );
    }
    
    function whereClause( &$params, $sortBy = true, $force ) {
        $values  =  array( );
        $clauses = array( );
        $title   = $this->get( 'title' );
        if ( $title ) {
            $clauses[] = "creator_pledge_desc LIKE %1";
            if ( strpos( $title, '%' ) !== false ) {
                $params[1] = array( trim($title), 'String', false );
            } else {
                $params[1] = array( trim($title), 'String', true );
            }
        }
        
        $pledgeByDates = $this->get( 'pledgeByDates' );
        if ($this->_searchResult) {
            if ( $pledgeByDates) {
                require_once 'CRM/Utils/Date.php';
                $from = $this->get( 'created_date' );
                if ( ! CRM_Utils_System::isNull( $from ) ) {
                    $from = CRM_Utils_date::format( $from );
                    $from .= '000000';
                    $clauses[] = 'created_date >= %3';
                    $params[3] = array( $from, 'String' );
                }
                $to = $this->get( 'deadline' );
                if ( ! CRM_Utils_System::isNull( $to ) ) {
                    $to = CRM_Utils_date::format( $to );
                    $to .= '235959';
                    $clauses[] = 'created_date <= %4';
                    $params[4] = array( $to, 'String' );
                }
            } else {
                $curDate = date( 'YmdHis' );
                $clauses[5] =  "(deadline >= {$curDate} OR deadline IS NULL)";
            }
            
        } else {
            $curDate = date( 'YmdHis' );
            $clauses[] =  "( deadline >= {$curDate} OR deadline IS NULL)";
        }
        if ( $sortBy &&
             $this->_sortByCharacter ) {
            $clauses[] = 'creator_pledge_desc LIKE %6';
            $params[6] = array( $this->_sortByCharacter . '%', 'String' );
        }
        
        // dont do a the below assignement when doing a 
        // AtoZ pager clause
        if ( $sortBy ) {
            if ( count( $clauses ) > 1 ) {
                $this->assign( 'isSearch', 1 );
            } else {
                $this->assign( 'isSearch', 0 );
            }
        }
        
        if ( empty( $clauses ) ) {
            return 1;
        }
        
        return implode( ' AND ', $clauses );
    }
    
    function pager( $whereClause, $whereParams ) 
    {
        require_once 'CRM/Utils/Pager.php';
        
        $params['status']       = ts('PledgeBank %%StatusMessage%%');
        $params['csvString']    = null;
        $params['buttonTop']    = 'PagerTopButton';
        $params['buttonBottom'] = 'PagerBottomButton';
        $params['rowCount']     = $this->get( CRM_Utils_Pager::PAGE_ROWCOUNT );
        if ( ! $params['rowCount'] ) {
            $params['rowCount'] = CRM_Utils_Pager::ROWCOUNT;
        }
        
        $query = "
SELECT count(id)
  FROM civicrm_pb_pledge
 WHERE $whereClause";
        
        $params['total'] = CRM_Core_DAO::singleValueQuery( $query, $whereParams );
        
        $this->_pager = new CRM_Utils_Pager( $params );
        $this->assign_by_ref( 'pager', $this->_pager );
    }
    
    function pagerAtoZ( $whereClause, $whereParams ) 
    {
        require_once 'CRM/Utils/PagerAToZ.php';
        
        $query = "
   SELECT DISTINCT UPPER(LEFT(creator_pledge_desc, 1)) as sort_name
     FROM civicrm_pb_pledge
    WHERE $whereClause
 ORDER BY LEFT(creator_pledge_desc, 1)
";
        $dao = CRM_Core_DAO::executeQuery( $query, $whereParams );
        
        $aToZBar = CRM_Utils_PagerAToZ::getAToZBar( $dao, $this->_sortByCharacter, true );
        $this->assign( 'aToZ', $aToZBar );
    }
    
}

