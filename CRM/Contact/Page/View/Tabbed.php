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

require_once 'CRM/Contact/Page/View.php';

/**
 * Main page for viewing contact.
 *
 */
class CRM_Contact_Page_View_Tabbed extends CRM_Contact_Page_View {

    /** 
     * Heart of the viewing process. The runner gets all the meta data for 
     * the contact and calls the appropriate type of page to view. 
     * 
     * @return void 
     * @access public 
     * 
     */ 
    function preProcess( ) {
        parent::preProcess( );

        //Custom Groups Inline
        $entityType = CRM_Contact_BAO_Contact::getContactType($this->_contactId);
        $groupTree =& CRM_Core_BAO_CustomGroup::getTree($entityType, $this->_contactId);
        CRM_Core_BAO_CustomGroup::buildViewHTML( $this, $groupTree );
    }

    /**
     * Heart of the viewing process. The runner gets all the meta data for
     * the contact and calls the appropriate type of page to view.
     *
     * @return void
     * @access public
     *
     */
    function run( )
    {
        $this->preProcess( );

        if ( $this->_action & CRM_Core_Action::UPDATE ) {
            $this->edit( );
        } else {
            $this->view( );
        }

        return parent::run( );
    }

    /**
     * Edit name and address of a contact
     *
     * @return void
     * @access public
     */
    function edit( ) {
        // set the userContext stack
        $session =& CRM_Core_Session::singleton();
        $url = CRM_Utils_System::url('civicrm/contact/view/basic', 'action=browse&cid=' . $this->_contactId );
        $session->pushUserContext( $url );
        
        $controller =& new CRM_Core_Controller_Simple( 'CRM_Contact_Form_Edit', ts('Contact Page'), CRM_Core_Action::UPDATE );
        $controller->setEmbedded( true );
        $controller->process( );
        return $controller->run( );
    }

    /**
     * View summary details of a contact
     *
     * @return void
     * @access public
     */
    function view( ) {
        $params   = array( );
        $defaults = array( );
        $ids      = array( );

        $params['id'] = $params['contact_id'] = $this->_contactId;
        $contact = CRM_Contact_BAO_Contact::retrieve( $params, $defaults, $ids );

        CRM_Contact_BAO_Contact::resolveDefaults( $defaults );

        if (CRM_Utils_Array::value( 'gender_id',  $defaults )) {
            $gender =CRM_Core_PseudoConstant::gender();
            $defaults['gender_display'] =  $gender[CRM_Utils_Array::value( 'gender_id',  $defaults )];
        }

        // get the list of all the categories
        $tag =& CRM_Core_PseudoConstant::tag();
        // get categories for the contact id
        require_once 'CRM/Core/BAO/EntityTag.php';
        $entityTag =& CRM_Core_BAO_EntityTag::getTag('civicrm_contact', $this->_contactId);

        if ( $entityTag ) {
            $categories = array( );
            foreach ( $entityTag as $key ) {
                $categories[] = $tag[$key];
            }
            $defaults['contactTag'] = implode( ', ', $categories );
        }
        
        $defaults['privacy_values'] = CRM_Core_SelectValues::privacy();
        $this->assign( $defaults );
        $this->setShowHide( $defaults );        
        
        // also assign the last modifed details
        require_once 'CRM/Core/BAO/Log.php';
        $lastModified =& CRM_Core_BAO_Log::lastModified( $this->_contactId, 'civicrm_contact' );
        $this->assign_by_ref( 'lastModified', $lastModified );
        
        $allTabs  = array( );
        $weight = 10;
        
        require_once 'CRM/Core/BAO/Preferences.php';
        $this->_viewOptions = CRM_Core_BAO_Preferences::contactViewOptions( );
        
        // get the contributions, new style of doing stuff
        // do the below only if the person has access to contributions
        $config =& CRM_Core_Config::singleton( );
        if ( CRM_Core_Permission::access( 'CiviContribute' ) &&
             $this->_viewOptions[ts('Contributions')] ) {
            $allTabs[ts('Contributions')] = array ( 'id'     => 'contribute',
                                                    'url'    =>  CRM_Utils_System::url( 'civicrm/contact/view/contribution',
                                                                                     "reset=1&force=1&snippet=1&cid={$this->_contactId}" ),
                                                    'title'  => ts('Contributions'),
                                                    'weight' => $weight );
            $weight += 10;
        }

        // get the memberships, new style of doing stuff
        // do the below only if the person has access to memberships
        if ( CRM_Core_Permission::access( 'CiviMember' ) &&
             $this->_viewOptions[ts('Memberships')] ) {
            $allTabs[] = array ( 'id'  => 'member',
                                 'url' =>  CRM_Utils_System::url( 'civicrm/contact/view/membership',
                                                                  "reset=1&force=1&snippet=1&cid={$this->_contactId}" ),
                                 'title'  => ts('Memberships'),
                                 'weight' => $weight );
            $weight += 10;
        }

        // get the events, new style of doing stuff
        // do the below only if the person has access to events
        if ( CRM_Core_Permission::access( 'CiviEvent' ) &&
             $this->_viewOptions[ts('Events')] ) {
            $allTabs[ts('Events')] = array ( 'id'  => 'participant',
                                             'url' =>  CRM_Utils_System::url( 'civicrm/contact/view/participant',
                                                                              "reset=1&force=1&snippet=1&cid={$this->_contactId}" ),
                                             'title'  => ts('Events'),
                                             'weight' => $weight );
            $weight += 10;
        }
        
        $rest = array( 'activity'      => ts( 'Activities'    ),
                       'rel'           => ts( 'Relationships' ),
                       'group'         => ts( 'Groups'        ),
                       'note'          => ts( 'Notes'         ),
                       'tag'           => ts( 'Tags'          ),
                       'log'           => ts( 'Change Log'    ) );

        foreach ( $rest as $k => $v ) {
            if ( ! $this->_viewOptions[$v] ) {
                continue;
            }
            if ( $k == 'activity' ) {
                $history = array_key_exists( 'history', $_GET ) ? $_GET['history'] : 0;
                $allTabs[] = array( 'id'     => $k,
                                    'url'    => CRM_Utils_System::url( "civicrm/contact/view/$k",
                                                                      "reset=1&show=1&snippet=1&history={$history}&cid={$this->_contactId}" ),
                                    'title'  => $v,
                                    'weight' => $weight );
                $weight += 10;
            } else {
                $allTabs[] = array( 'id'     =>  $k,
                                    'url'    => CRM_Utils_System::url( "civicrm/contact/view/$k",
                                                                      "reset=1&snippet=1&cid={$this->_contactId}" ),
                                    'title'  => $v,
                                    'weight' => $weight );
                $weight += 10;
            }
        }

        // now add all the custom tabs
        $activeGroups =&
            CRM_Core_BAO_CustomGroup::getActiveGroups( CRM_Contact_BAO_Contact::getContactType($this->_contactId),
                                                       'civicrm/contact/view/cd',
                                                       $this->_contactId );
                                                                    
        foreach ( $activeGroups as $group ) {
            $id = "custom_{$group['id']}";
            $allTabs[] = array( 'id'     => $id,
                                'url'    => CRM_Utils_System::url( $group['path'], $group['query'] . "&snippet=1&selectedChild=$id"),
                                'title'  => $group['title'],
                                'weight' => $weight );
            $weight += 10;
        }

        // see if any other modules want to add any tabs
        require_once 'CRM/Utils/Hook.php';
        $hookTabs = CRM_Utils_Hook::links( 'tabs.contact.activity', 'Contact', $this->_contactId );
        if ( $hookTabs ) {
            $allTabs = array_merge( $allTabs, $hookTabs );
        }

        // now sort the tabs based on weight
        usort( $allTabs, array( 'CRM_Contact_Page_View_Tabbed', 'cmpFunc' ) );

        $this->assign( 'dojoIncludes', "dojo.require('dojo.widget.TabContainer');dojo.require('dojo.widget.ContentPane');dojo.require('dojo.widget.LinkPane');" );

        $this->assign( 'allTabs'     , $allTabs     );
     
        $selectedChild = CRM_Utils_Request::retrieve( 'selectedChild', 'String', $this, false, 'summary' );
        $this->assign( 'selectedChild', $selectedChild );
        
    }

    static function cmpFunc( $a, $b ) {
        return ( $a['weight'] <= $b['weight'] ) ? -1 : 1;
    }

    /**
     * Show hide blocks based on default values.
     *
     * @param array (reference) $defaults
     * @return void
     * @access public
     */
    function setShowHide( &$defaults ) {
        require_once 'CRM/Core/ShowHideBlocks.php';

        $showHide =& new CRM_Core_ShowHideBlocks( array( 'commPrefs'      => 1 ),
                                                  array( 'commPrefs_show' => 1 ) );

        $config =& CRM_Core_Config::singleton( ); 

        if ( $defaults['contact_type'] == 'Individual' ) {
            // is there any demographics data?
            if ( CRM_Utils_Array::value( 'gender_id'  , $defaults ) ||
                 CRM_Utils_Array::value( 'is_deceased', $defaults ) ||
                 CRM_Utils_Array::value( 'birth_date' , $defaults ) ) {
                $showHide->addShow( 'demographics' );
                $showHide->addHide( 'demographics_show' );
            } else {
                $showHide->addShow( 'demographics_show' );
                $showHide->addHide( 'demographics' );
            }
        }

        if ( array_key_exists( 'location', $defaults ) ) {
            $numLocations = count( $defaults['location'] );
            if ( $numLocations > 0 ) {
                $showHide->addShow( 'location_1' );
                $showHide->addHide( 'location_1_show' );
            }
            for ( $i = 1; $i < $numLocations; $i++ ) {
                $locationIndex = $i + 1;
                $showHide->addShow( "location_{$locationIndex}_show" );
                $showHide->addHide( "location_{$locationIndex}" );
            }
        }
        
        $showHide->addToTemplate( );
    }

}

?>
