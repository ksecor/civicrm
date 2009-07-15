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

require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/SelectValues.php';
require_once 'CRM/Contact/Form/Edit/Phone.php';
require_once 'CRM/Contact/Form/Edit/Email.php';
require_once 'CRM/Contact/Form/Edit/IM.php';
require_once 'CRM/Contact/Form/Edit/OpenID.php';
require_once 'CRM/Contact/Form/Edit/Address.php';

class CRM_Contact_Form_Location
{  
    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     */ 
    function preProcess( &$form ) 
    {  
        $form->_addBlockName  = CRM_Utils_Array::value( 'block', $_GET );
        $additionalblockCount = CRM_Utils_Array::value( 'count', $_GET );
        $form->assign( "addBlock", false );
        if ( $form->_addBlockName && $additionalblockCount ) {
            $form->assign( "addBlock", true );
            $form->assign( "blockName", $form->_addBlockName );
            $form->assign( "blockId",  $additionalblockCount );
            $form->set( 'blockName', $form->_addBlockName );
            $form->set( $form->_addBlockName."_Block_Count", $additionalblockCount );
        }
        $form->assign( 'className', CRM_Utils_System::getClassName( $form ) );
        
        $form->_blocks = array( 'Address', 'Email', 'Phone' );
        $form->assign( 'blocks', $form->_blocks );
    }
    
    /** 
     * Function to build the form 
     * 
     * @return None 
     * @access public 
     */ 
    function buildQuickForm ( &$form ) 
    { 
        //load form for child blocks
        if ( $form->_addBlockName ) {
            require_once( str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_Form_Edit_" . $form->_addBlockName ) . ".php");
            return eval( 'CRM_Contact_Form_Edit_' . $form->_addBlockName . '::buildQuickForm( $form );' );
        }
        
        //build 1 instance of all blocks, without using ajax ...
        foreach ( $form->_blocks  as $blockName ) {
            require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_Form_Edit_" . $blockName ) . ".php");
            $instanceStr = CRM_Utils_Array::value( "hidden_".$blockName ."_Instances", $_POST, 1 );
            
//             //hack for setdefault building.
//             if ( CRM_Utils_System::isNull( $_POST ) ) { 
//                 $name = strtolower($blockName);
//                 if ( CRM_Utils_Array::value( $name, $form->_values ) && 
//                      is_array( $form->_values[$name] ) ) { 
//                     foreach ( $form->_values[$name] as $instance => $blockValues ) {
//                         if ( $instance == 1 ) continue; 
//                         $instanceStr .= ",{$instance}";
//                     }
//                 }
//             }
            
            $instances = explode( ',', $instanceStr );
            
            foreach ( $instances as $instance ) {
                if ( $instance == 1 ) {
                    $form->assign( "addBlock", false );
                    $form->assign( "blockId",  $instance );
                } else {
                    //we are going to build other block instances w/ AJAX
                    $generateAjaxRequest++;
                    $ajaxRequestBlocks[$blockName][$instance] = true;
                }
                
                $form->set( $blockName."_Block_Count", $instance );
                eval( 'CRM_Contact_Form_Edit_' . $blockName . '::buildQuickForm( $form );' );
            }
        }
        
        //assign to generate AJAX request for building extra blocks.
        $form->assign( 'generateAjaxRequest', $generateAjaxRequest );
        $form->assign( 'ajaxRequestBlocks',   $ajaxRequestBlocks   );
    }
    

    //need to check following entire code .. is it valid to keep ?
    
    
    /**
     * BLOCKS constant determines max number of Phone, Email and IM blocks to offer
     * within the Location section.
     *
     * @var int
     * @const
     */
    const BLOCKS = 5;

    static $_commPrefs = array( 'phone','email','im','openid' );

    /**
     * function to build location block
     *
     * @param object $form the object of the form (QF Object)
     * @param int $maxLocationBlocks no of location blocks
     *
     * @param array $locationCompoments blocks to be displayed(Phone,Email,IM,OpenID)
     *
     * @static 
     * @access public
     */
    static function &buildLocationBlock( &$form, $maxLocationBlocks, $locationType = true, 
                                         $locationCompoments = null, $addressOnly  = false
                                         ) 
    {
        $location = array();
        $config = CRM_Core_Config::singleton( );
        for ($locationId = 1; $locationId <= $maxLocationBlocks; $locationId++) {
            if ( $locationType ) {
                $location[$locationId]['location_type_id'] =& $form->addElement('select',
                                                                                "address[$locationId][location_type_id]",
                                                                                null,
                                                                                array( '' => ts( '- select -' ) ) + CRM_Core_PseudoConstant::locationType( ) );
                
                $location[$locationId]['is_primary']       =& $form->addElement(
                                                                                'checkbox', 
                                                                                "address[$locationId][is_primary]", 
                                                                                ts('Primary location for this contact'),  
                                                                                ts('Primary location for this contact'), 
                                                                                array('onchange' => "location_onclick('" . $form->getName() . "', $locationId, $maxLocationBlocks, 'is_primary');" ) );

                $location[$locationId]['is_billing']       =& $form->addElement(
                                                                                'checkbox', 
                                                                                "address[$locationId][is_billing]", 
                                                                                ts('Billing location for this contact'),  
                                                                                ts('Billing location for this contact'), 
                                                                                array('onchange' => "location_onclick('" . $form->getName() . "', $locationId, $maxLocationBlocks, 'is_billing');" ) );
            }
            
            CRM_Contact_Form_Edit_Address::buildQuickForm( $form );
            
            require_once 'CRM/Core/ShowHideBlocks.php';
            CRM_Core_ShowHideBlocks::linksForArray( $form, $locationId, $maxLocationBlocks, "location", '', '' );
            
            if ( $addressOnly ) {
                continue;
            }
            
            if ( ! $locationCompoments ) {
                CRM_Contact_Form_Edit_Phone::buildQuickForm( $form ); 
                CRM_Contact_Form_Edit_Email::buildQuickForm( $form ); 
                CRM_Contact_Form_Edit_IM::buildQuickForm( $form ); 
                CRM_Contact_Form_Edit_OpenID::buildQuickForm( $form );
            } else {
                $blockCount = $maxLocationBlocks;
                foreach ( $locationCompoments as $key) {
                    eval('CRM_Contact_Form_Edit_' . $key . '::buildQuickForm( $form );');
                }
            }
            
        }
        return $location;
    }

    
    /**
     * function to show/hide the location block
     *
     * @param CRM_Core_ShowHideBlocks $showHide          the showHide object
     * @param int                     $maxLocationBlocks no of location blocks
     * @param array                   $prefixBloack      array of block names
     * @param boolean                 $showHideLocation  do you want
     *                                                   location show hide links 
     *
     * @access public
     */
    function setShowHideDefaults( &$showHide, $maxLocationBlocks ,$prefixBlock = null, $showHideLocation=true) {
        require_once 'CRM/Core/BAO/Preferences.php';
        $blockShow = CRM_Core_BAO_Preferences::valueOptions( 'address_options', true, null, true );

        for ($locationId = 1; $locationId <= $maxLocationBlocks; $locationId++) {
            if ( $showHideLocation ) {
                if ( $locationId == 1 ) {
                    $showHide->addShow( "id_location_{$locationId}" );
                    $showHide->addHide( "id_location_{$locationId}_show" );
                } else {
                    $showHide->addHide( "id_location_{$locationId}" );
                    if ( $locationId == 2 ) {
                        $showHide->addShow( "id_location_{$locationId}_show" );
                    } else {
                        $showHide->addHide( "id_location_{$locationId}_show" );
                    }
                }
            }
            
            if ( ! $prefixBlock ) {
                $prefixBlock = self::$_commPrefs ;
            }
        
            foreach ( $blockShow as $block => $value ) {
                $key = array_search( $block, $prefixBlock );
                if( $key && $value == 0 ) {
                    unset($prefixBlock[$key]);
                }
            }
            
            foreach ( $prefixBlock as $block ) {
                for ( $blockId = 1; $blockId <= self::BLOCKS; $blockId++ ) {
                    if ( $blockId != 1 ) {
                        $showHide->addHide( "id_location_{$locationId}_{$block}_{$blockId}");
                        if ( $blockId == 2 ) {
                            $showHide->addShow( "id_location_{$locationId}_{$block}_{$blockId}_show" );
                        } else {
                            $showHide->addHide( "id_location_{$locationId}_{$block}_{$blockId}_show" );
                        }
                    }
                }
            }
        }
    }
    
    /**
     * Fix what blocks to show/hide based on the default values set
     *
     * @param CRM_Core_ShowHideBlocks $showHide the showHide object
     * @param array @defaults the array of default values
     *
     * @return void
     */
    function updateShowHide( &$showHide, &$values, $maxLocationBlocks, $prefixBlock = null, $showHideLocation=true ) {
        require_once 'CRM/Core/BAO/Preferences.php';
        $blockShow = CRM_Core_BAO_Preferences::valueOptions( 'address_options', true, null, true );
        if ( empty( $values ) || $maxLocationBlocks <= 0 ) {
            return;
        }
        
        $locationKeys = array_keys( $values );
        
        foreach ( $locationKeys as $locationId ) {
            /*if( empty( $values[$locationId])){
                continue;
                }*/
            $location = $values[$locationId];
            
            $locationFlag = false;
            foreach($location as $locationKey=>$locationEntity) {
                if(is_array($locationEntity)) {
                    foreach($locationEntity as $entityKey=>$entity) {
                        if(is_array($entity)) {
                            foreach($entity as $subkey=>$subEntity) {
                                if($subEntity!='') {
                                    $locationFlag = true;
                                }
                            }
                        } else {
                            if($entity!='' and $entityKey!='country_id') {
                                $locationFlag = true;
                            }
                        }
                    }
                } 
            }
            if(!$locationFlag) {
                continue;
            }
            if ( $showHideLocation ) {
                $showHide->addShow( "id_location_{$locationId}" );
            }
            if ( $locationId != 1 ) {
                $showHide->addHide( "id_location_{$locationId}_show" );
            }
            if ( $locationId < $maxLocationBlocks ) {
                $nextLocationId = $locationId + 1;
                $showHide->addShow( "id_location_{$nextLocationId}_show" );
            }
            if ( ! $prefixBlock ) {
                $prefixBlock = self::$_commPrefs;
            }

            foreach ( $blockShow as $block => $value ) {
                $key = array_search( $block, $prefixBlock );
                if( $key && $value == 0 ) {
                    unset($prefixBlock[$key]);
                }
            }
            
            foreach ( $prefixBlock as $block ) {
                $tmpArray = CRM_Utils_Array::value( $block, $values[$locationId] );
                self::updateShowHideSubBlocks( $showHide, $block, "id_location_{$locationId}",
                                               $tmpArray );
            }
        }
        
    }

    /**
     * function to show/hide the block
     *
     * @param CRM_Core_ShowHideBlocks $showHide the showHide object
     *
     * @access public
     */
    function updateShowHideSubBlocks( &$showHide, $name, $prefix, &$values ) {
        if ( empty( $values ) ) {
            return;
        }
        $blockKeys = array_keys( $values );
        foreach ( $blockKeys as $blockId ) {
            
            /* if ( empty( $values[$blockId] ) ) {
                continue;
            }*/
            $blocks = $values[$blockId];
            $blockFlag = false;

            if ( is_array($blocks) ) {
                foreach($blocks as $block) {
                    if($block!='') {
                        $blockFlag= true;
                    }
                }
            }

            if (!$blockFlag) {
                continue;
            }

            // blockId 1 is always shows
            if ( $blockId == 1 ) {
                continue;
            }

            $showHide->addShow( "{$prefix}_{$name}_{$blockId}" );
            $showHide->addHide( "{$prefix}_{$name}_{$blockId}_show" );
            if ( $blockId < self::BLOCKS ) {
                $nextBlockId = $blockId + 1;
                $showHide->addShow( "{$prefix}_{$name}_{$nextBlockId}_show" );
            }
        }
    }
}


