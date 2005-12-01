<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
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
        $_groupTree = CRM_Core_BAO_CustomGroup::getTree($entityType, $this->_contactId);

        //showhide blocks for Custom Fields inline
        $sBlocks = array();
        $hBlocks = array();
        $form = array();

        foreach ($_groupTree as $group) {           
            
            $groupId = $group['id'];
            foreach ($group['fields'] as $field) {
                
                $fieldId = $field['id'];                
                $elementName = $groupId . '_' . $fieldId . '_' . $field['name'];
                $form[$elementName]['name'] = $elementName;
                $form[$elementName]['html'] = null;
                
                if ( $field['data_type'] == 'String' ||
                     $field['data_type'] == 'Int' ||
                     $field['data_type'] == 'Float' ||
                     $field['data_type'] == 'Money') {
                    //added check for Multi-Select in the below if-statement
                    if ($field['html_type'] == 'Radio' || $field['html_type'] == 'CheckBox' || $field['html_type'] == 'Multi-Select') {
                        //$freezeString = $field['html_type'] == 'Radio' ? "( )" : "[ ]";
                        //$freezeStringChecked = $field['html_type'] == 'Radio' ? "(x)" : "[x]";
                        //added
                        $freezeString =  "";
                        $freezeStringChecked = "";

                        $customData = array();

                        //added check for Multi-Select in the below if-statement

                        if ( $field['html_type'] == 'CheckBox' || $field['html_type'] == 'Multi-Select') {

                            $customData = explode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, $field['customValue']['data']);
                            
                        } else {
                            $customData[] = $field['customValue']['data'];
                        }
                        
                        
                        $coDAO =& new CRM_Core_DAO_CustomOption();
                        $coDAO->entity_id  = $field['id'];
                        $coDAO->entity_table = 'civicrm_custom_field';
                        $coDAO->orderBy('weight ASC, label ASC');
                        $coDAO->find( );                    
                        
                        $counter = 1;
                        while($coDAO->fetch()) {
                           
                            //to show only values that are checked
                           if(in_array($coDAO->value, $customData)){
                               $checked = in_array($coDAO->value, $customData) ? $freezeStringChecked : $freezeString;
                               if($counter!=1)
                                   $form[$elementName]['html'] .= "<tt>". $checked ."</tt>,&nbsp;".$coDAO->label;
                               else
                                   $form[$elementName]['html'] .= "<tt>". $checked ."</tt>".$coDAO->label;
                               $form[$elementName][$counter]['html'] = "<tt>". $checked ."</tt>".$coDAO->label."\n";
                               $counter++;
                           }
                        }
                    } else {
                        if ( $field['html_type'] == 'Select' ) {
                            $coDAO =& new CRM_Core_DAO_CustomOption();
                            $coDAO->entity_id    = $field['id'];
                            $coDAO->entity_table = 'civicrm_custom_field';
                            $coDAO->orderBy('weight ASC, label ASC');
                            $coDAO->find( );
                            
                            while($coDAO->fetch()) {
                                if ( $coDAO->value == $field['customValue']['data'] ) {
                                    $form[$elementName]['html'] = $coDAO->label;
                                }
                            }
                        } else {

                            $form[$elementName]['html'] = $field['customValue']['data'];
                        }
                    }
                } else {
                    if ( isset($field['customValue']['data']) ) {
                        switch ($field['data_type']) {
                            
                        case 'Boolean':
                            
                            //$freezeString = "( )";
                            //$freezeStringChecked = "(x)";
    
                            $freezeString = "";
                            $freezeStringChecked = "";

                            if ( isset($field['customValue']['data']) ) {
                                if ( $field['customValue']['data'] == '1' ) {
                                    //$form[$elementName]['html'] = "<tt>".$freezeStringChecked."</tt>Yes&nbsp;<tt>".$freezeString."</tt>No\n";
                                    $form[$elementName]['html'] = "<tt>".$freezeStringChecked."</tt>Yes\n";

                                } else {
                                    //$form[$elementName]['html'] = "<tt>".$freezeString."</tt>Yes&nbsp;<tt>".$freezeStringChecked."</tt>No\n";
                                    $form[$elementName]['html'] = "<tt>".$freezeStringChecked."</tt>No\n";

                                }
                            } else {
                                //$form[$elementName]['html'] = "<tt>".$freezeString."</tt>Yes&nbsp;<tt>".$freezeString."</tt>No\n";
                                $form[$elementName]['html'] = "\n";

                            }                        
                            
                            break;
                            
                        case 'StateProvince':
                            $form[$elementName]['html'] = CRM_Core_PseudoConstant::stateProvince( $field['customValue']['data'] );
                            break;
                            
                        case 'Country':
                            $form[$elementName]['html'] = CRM_Core_PseudoConstant::country( $field['customValue']['data'] );
                            break;
                            
                        case 'Date':
                            $form[$elementName]['html'] = CRM_Utils_Date::customFormat($field['customValue']['data']);
                            break;
                            
                        default:
                            $form[$elementName]['html'] = $field['customValue']['data'];
                        }                    
                    }
                }
            }

            //showhide group
            if ( $group['collapse_display'] ) {
                $sBlocks[] = "'". $group['title'] . "[show]'" ;
                $hBlocks[] = "'". $group['title'] ."'";
            } else {
                $hBlocks[] = "'". $group['title'] . "[show]'" ;
                $sBlocks[] = "'". $group['title'] ."'";
            }
        }
        
        $showBlocks = implode(",",$sBlocks);
        $hideBlocks = implode(",",$hBlocks);

        $this->assign('viewForm',$form);
        $this->assign('showBlocks1',$showBlocks);
        $this->assign('hideBlocks1',$hideBlocks);

        $this->assign('groupTree', $_groupTree);
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

        // get the contributions, new style of doing stuff
        $controller =& new CRM_Core_Controller_Simple( 'CRM_Contribute_Form_Search', ts('Contributions'), $this->_action );  
        $controller->setEmbedded( true );                           
        $controller->reset( );  
        $controller->set( 'limit', 3 ); 
        $controller->set( 'force', 1 );
        $controller->set( 'cid'  , $this->_contactId );
        $controller->set( 'context', 'basic' ); 
        $controller->process( );  
        $controller->run( );
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
                                                         'notes[show]'          => 1,
                                                         'relationships[show]'  => 1,
                                                         'groups[show]'         => 1,
                                                         'openActivities[show]' => 1,
                                                         'activityHx[show]'     => 1 ),
                                                  array( 'notes'                => 1,
                                                         'commPrefs[show]'      => 1,
                                                         'relationships'        => 1,
                                                         'groups'               => 1,
                                                         'openActivities'       => 1,
                                                         'activityHx'           => 1 ) );                                                      
        
        if ( $defaults['contact_type'] == 'Individual' ) {
            // is there any demographics data?
            if ( CRM_Utils_Array::value( 'gender_id'     , $defaults ) ||
                 CRM_Utils_Array::value( 'is_deceased', $defaults ) ||
                 CRM_Utils_Array::value( 'birth_date' , $defaults ) ) {
                $showHide->addShow( 'demographics' );
                $showHide->addHide( 'demographics[show]' );
            } else {
                $showHide->addShow( 'demographics[show]' );
                $showHide->addHide( 'demographics' );
            }
        }

        if ( array_key_exists( 'location', $defaults ) ) {
            $numLocations = count( $defaults['location'] );
            if ( $numLocations > 0 ) {
                $showHide->addShow( 'location[1]' );
                $showHide->addHide( 'location[1][show]' );
            }
            for ( $i = 1; $i < $numLocations; $i++ ) {
                $locationIndex = $i + 1;
                $showHide->addShow( "location[$locationIndex][show]" );
                $showHide->addHide( "location[$locationIndex]" );
            }
        }
        
        $showHide->addToTemplate( );
    }

}

?>
