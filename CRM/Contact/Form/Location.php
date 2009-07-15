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
}


