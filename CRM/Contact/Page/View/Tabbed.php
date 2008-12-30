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
    function preProcess( ) 
    {
        parent::preProcess( );

        //Custom Groups Inline
        $entityType = CRM_Contact_BAO_Contact::getContactType($this->_contactId);
        $groupTree =& CRM_Core_BAO_CustomGroup::getTree($entityType, $this, $this->_contactId);

        CRM_Core_BAO_CustomGroup::buildCustomDataView( $this, $groupTree );

        // also create the form element for the activity links box
        $controller =& new CRM_Core_Controller_Simple( 'CRM_Activity_Form_ActivityLinks',
                                                       ts('Activity Links'), null );
        $controller->setEmbedded( true );
        $controller->run( );
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
    function edit( ) 
    {
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
    function view( ) 
    {
        $session =& CRM_Core_Session::singleton();
        $url     = CRM_Utils_System::url('civicrm/contact/view', 'reset=1&cid=' . $this->_contactId );
        $session->pushUserContext( $url );

        $params   = array( );
        $defaults = array( );
        $ids      = array( );

        $params['id'] = $params['contact_id'] = $this->_contactId;
        $contact = CRM_Contact_BAO_Contact::retrieve( $params, $defaults, $ids, true );

        CRM_Contact_BAO_Contact::resolveDefaults( $defaults );
        
        // unset locations if empty
        if ( ( count( $defaults['location'] ) == 1 ) &&
             ( count( $defaults['location'][1] ) == 1 ) ) {
            // this means this is only one element in the first location
            // which is is_primary, so we ignore it in view mode
            unset( $defaults['location'] );
        }

        if ( CRM_Utils_Array::value( 'gender_id',  $defaults ) ) {
            $gender =CRM_Core_PseudoConstant::gender();
            $defaults['gender_display'] =  $gender[CRM_Utils_Array::value( 'gender_id',  $defaults )];
        }

        // get the list of all the categories
        $tag =& CRM_Core_PseudoConstant::tag();

        // get categories for the contact id
        require_once 'CRM/Core/BAO/EntityTag.php';
        $entityTag =& CRM_Core_BAO_EntityTag::getTag($this->_contactId);

        if ( $entityTag ) {
            $categories = array( );
            foreach ( $entityTag as $key ) {
                $categories[] = $tag[$key];
            }
            $defaults['contactTag'] = implode( ', ', $categories );
        }
        
        $defaults['privacy_values'] = CRM_Core_SelectValues::privacy();
        
        //Show blocks only if they are visible in edit form
        require_once 'CRM/Core/BAO/Preferences.php';
        $this->_editOptions  = CRM_Core_BAO_Preferences::valueOptions( 'contact_edit_options' );
        $configItems = array( 'CommBlock'     => 'Communication Preferences',
                              'Demographics'  => 'Demographics',
                              'TagsAndGroups' => 'Tags and Groups',
                              'Notes'         => 'Notes' );

        foreach ( $configItems as $c => $t ) {
            $varName = '_show' . $c;
            $this->$varName = $this->_editOptions[$c];
            $this->assign( substr( $varName, 1 ), $this->$varName );
        }

        //get the current employer name
        if ( $defaults['contact_type'] == 'Individual' ) {
            require_once 'CRM/Contact/BAO/Relationship.php';
            $currentEmployer = CRM_Contact_BAO_Relationship::getCurrentEmployer( array( $this->_contactId ) );
            $defaults['current_employer'] = $currentEmployer[ $this->_contactId ]['org_name'];
            $defaults['current_employer_id'] = $currentEmployer[ $this->_contactId ]['org_id'];
        }

        $this->assign( $defaults );
        $this->setShowHide( $defaults );        
        
        // also assign the last modifed details
        require_once 'CRM/Core/BAO/Log.php';
        $lastModified =& CRM_Core_BAO_Log::lastModified( $this->_contactId, 'civicrm_contact' );
        $this->assign_by_ref( 'lastModified', $lastModified );
        
        $allTabs  = array( );
        $weight = 10;
        
        
        $this->_viewOptions = CRM_Core_BAO_Preferences::valueOptions( 'contact_view_options', true );
        $changeLog = $this->_viewOptions['log'];
        $this->assign_by_ref( 'changeLog' , $changeLog );
        require_once 'CRM/Core/Component.php';
        $components = CRM_Core_Component::getEnabledComponents();

        foreach ( $components as $name => $component ) {
            if ( CRM_Utils_Array::value( $name, $this->_viewOptions ) &&
                 CRM_Core_Permission::access( $component->name ) ) {
                $elem = $component->registerTab();

                // FIXME: not very elegant, probably needs better approach
                // allow explicit id, if not defined, use keyword instead
                if( array_key_exists( 'id', $elem ) ) {
                    $i = $elem['id'];
                } else {
                    $i = $component->getKeyword();
                }
                $u = $elem['url'];
               
                //appending isTest to url for test soft credit CRM-3891. 
                //FIXME: hack dojo url.
                $q = "reset=1&snippet=1&force=1&cid={$this->_contactId}"; 
                if ( CRM_Utils_Request::retrieve('isTest', 'Positive', $this) ) {
                    $q = $q."&isTest=1";
                }                
                $allTabs[] = array( 'id'     =>  $i,
                                    'url'    => CRM_Utils_System::url( "civicrm/contact/view/$u", $q ),
                                    'title'  => $elem['title'],
                                    'weight' => $elem['weight'] );
                // make sure to get maximum weight, rest of tabs go after
                // FIXME: not very elegant again
                if( $weight < $elem['weight'] ) {
                    $weight = $elem['weight'];
                }
            }
        }
        
        $rest = array( 'activity'      => ts('Activities')    ,
                       'case'          => ts('Cases')         ,
                       'rel'           => ts('Relationships') ,
                       'group'         => ts('Groups')        ,
                       'note'          => ts('Notes')         ,
                       'tag'           => ts('Tags')          ,
                       'log'           => ts('Change Log')    ,
                       );

        $config =& CRM_Core_Config::singleton( );
        if ( isset( $config->sunlight ) &&
             $config->sunlight ) {
            $title = ts('Elected Officials');
            $rest['sunlight'] = $title;
            $this->_viewOptions[$title] = true;
        }

        foreach ( $rest as $k => $v ) {
            if ( CRM_Utils_Array::value($k, $this->_viewOptions) ) {
                
            
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
        CRM_Utils_Hook::tabs( $allTabs, $this->_contactId );

        if( $config->civiHRD ) {
            $hrdOrder = array(
                       'rel'           => 1,
                       'case'          => 2,
                       'activity'      => 3,
                       'participant'   => 4,
                       'grant'         => 5,
                       'contribute'    => 6,
                       'group'         => 7,
                       'note'          => 8,
                       'tag'           => 9,
                       'log'           => 10
                       );

            foreach( $allTabs as $i => $tab ) {
                if( array_key_exists( $tab['id'],  $hrdOrder ) ) {
                    $allTabs[$i]['weight'] = $hrdOrder[$tab['id']];
                }
            }
        }




        // now sort the tabs based on weight
        require_once 'CRM/Utils/Sort.php';
        usort( $allTabs, array( 'CRM_Utils_Sort', 'cmpFunc' ) );

        $this->assign( 'dojoIncludes', "dojo.require('dijit.layout.TabContainer');dojo.require('dojox.layout.ContentPane');dojo.require('dijit.layout.LinkPane'); dojo.require('dojo.parser');");

        $this->assign( 'allTabs'     , $allTabs     );
     
        $selectedChild = CRM_Utils_Request::retrieve( 'selectedChild', 'String', $this, false, 'summary' );
        $this->assign( 'selectedChild', $selectedChild );
        
    }



    /**
     * Show hide blocks based on default values.
     *
     * @param array (reference) $defaults
     * @return void
     * @access public
     */
    function setShowHide( &$defaults ) {
        
        $config =& CRM_Core_Config::singleton( );
        
        if ( isset($defaults['mail_to_household_id']) ) {
            $HouseholdName = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', 
                                                          $defaults['mail_to_household_id'], 
                                                          'display_name', 
                                                          'id' );
            $this->assign( 'HouseholdName',$HouseholdName );
        }
        
        require_once 'CRM/Core/ShowHideBlocks.php';
        $showHide =& new CRM_Core_ShowHideBlocks();
        
        if ( $this->_showCommBlock ) {
            $showHide->addShow( 'commPrefs' );
            $showHide->addHide( 'commPrefs_show' );
        }
        
        if ( $this->_showDemographics && $defaults['contact_type'] == 'Individual' ) {
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


