<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
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
class CRM_Contact_Page_View_Basic extends CRM_Contact_Page_View {

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
        $contact = CRM_Contact_BAO_Contact::retrieve( $params, $defaults, $ids, true );

        CRM_Contact_BAO_Contact::resolveDefaults( $defaults );

        if (CRM_Utils_Array::value( 'gender_id',  $defaults )) {
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
        $this->assign( $defaults );
        $this->setShowHide( $defaults );        

        // also assign the last modifed details
        require_once 'CRM/Core/BAO/Log.php';
        $lastModified =& CRM_Core_BAO_Log::lastModified( $this->_contactId, 'civicrm_contact' );
        $this->assign_by_ref( 'lastModified', $lastModified );
        
        // get the contributions, new style of doing stuff
        // do the below only if the person has access to contributions
        $config =& CRM_Core_Config::singleton( );
        if ( CRM_Core_Permission::access( 'CiviContribute' ) ) {
            $this->assign( 'accessContribution', true );
            $controller =& new CRM_Core_Controller_Simple( 'CRM_Contribute_Form_Search', ts('Contributions'), $this->_action );  
            $controller->setEmbedded( true );                           
            $controller->reset( );  
            $controller->set( 'limit', 3 ); 
            $controller->set( 'force', 1 );
            $controller->set( 'cid'  , $this->_contactId );
            $controller->set( 'context', 'basic' ); 
            $controller->process( );  
            $controller->run( );
        } else {
            $this->assign( 'accessContribution', false );
        }

        // get the memberships, new style of doing stuff
        // do the below only if the person has access to memberships
        if ( CRM_Core_Permission::access( 'CiviMember' ) ) {
            $this->assign( 'accessMembership', true );
            $controller =& new CRM_Core_Controller_Simple( 'CRM_Member_Form_Search', ts('Memberships'), $this->_action );  
            $controller->setEmbedded( true );                           
            $controller->reset( );  
            $controller->set( 'limit', 3 ); 
            $controller->set( 'force', 1 );
            $controller->set( 'cid'  , $this->_contactId );
            $controller->set( 'context', 'basic' ); 
            $controller->process( );  
            $controller->run( );
        } else {
            $this->assign( 'accessMembership', false );
        }

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

        $showHide =& new CRM_Core_ShowHideBlocks( array( 'commPrefs'           => 1,
                                                         'notes_show'          => 1,
                                                         'relationships_show'  => 1,
                                                         'groups_show'         => 1,
                                                         'openActivities_show' => 1,
                                                         'activityHx_show'     => 1 ),
                                                  array( 'notes'                => 1,
                                                         'commPrefs_show'      => 1,
                                                         'relationships'        => 1,
                                                         'groups'               => 1,
                                                         'openActivities'       => 1,
                                                         'activityHx'           => 1 ) );

        $config =& CRM_Core_Config::singleton( ); 
        if ( CRM_Core_Permission::access( 'CiviContribute' ) ) {
            $showHide->addShow( 'contributions_show' ); 
            $showHide->addHide( 'contributions' ); 
        }

        if ( CRM_Core_Permission::access( 'CiviMember' ) ) {
            $showHide->addShow( 'memberships_show' ); 
            $showHide->addHide( 'memberships' ); 
        }

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
