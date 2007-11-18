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

require_once 'CRM/Contribute/Form/ContributionPage.php';

class CRM_Contribute_Form_ContributionPage_Widget extends CRM_Contribute_Form_ContributionPage {
    
    protected $_colors;

    protected $_widget;

    function preProcess( ) {
        parent::preProcess( );

        require_once 'CRM/Contribute/DAO/Widget.php';
        $widget = new CRM_Contribute_DAO_Widget( );
        $widget->contribution_page_id = $this->_id;
        if ( $widget->find( true ) ) {
            $this->_widget = $widget;
        } else {
            $this->_widget = null;
        }
        
        $config =& CRM_Core_Config::singleton( );
        $title = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_ContributionPage',
                                              $this->_id,
                                              'title' );

        $this->_fields = array( 'title'               => array( ts( 'Title' ),
                                                                'text',
                                                                true,
                                                                $title ),
                                'url_logo'            => array( ts( 'URL to an image logo' ),
                                                                'text',
                                                                false ,
                                                                null ),
                                'button_title'        => array( ts( 'Button Title' ),
                                                                'text',
                                                                false,
                                                                ts( 'Contribute!' ) ),
                                'about'               => array( ts( 'About' ),
                                                                'textarea',
                                                                true,
                                                                null ),
                                'url_homepage'        => array( ts( 'URL to Home Page' ),
                                                                'text',
                                                                true,
                                                                $config->userFrameworkBaseURL ),
                                'color_title'         => array( ts( 'Title Text Color' ),
                                                                'text',
                                                                true,
                                                                '0x000000' ),
                                'color_button'        => array( ts( 'Button Color' ),
                                                                'text',
                                                                true,
                                                                '0xCC9900' ),
                                'color_bar'           => array( ts( 'Progress Bar Color' ),
                                                                'text',
                                                                true,
                                                                '0xCC9900' ),
                                'color_main_text'     => array( ts( 'Additional Text Color' ),
                                                                'text',
                                                                true,
                                                                '0x000000' ),
                                'color_main'          => array( ts( 'Inner Background Gradient from Bottom' ),
                                                                'text',
                                                                true,
                                                                '0x96E0E0' ),
                                'color_main_bg'       => array( ts( 'Inner Background Top Area' ),
                                                                'text',
                                                                true,
                                                                '0xFFFFFF' ),
                                'color_bg'            => array( ts( 'Border Color' ),
                                                                'text',
                                                                true,
                                                                '0x66CCCC' ),
                                'color_about_link'    => array( ts( 'About Link Color' ),
                                                                'text',
                                                                true,
                                                                '0x336699' ),
                                'color_homepage_link' => array( ts( 'Homepage Link Color' ),
                                                                'text',
                                                                true,
                                                                '0x336699' ),
                                );
    }

    function setDefaultValues( ) {
        $defaults = array( );
        // check if there is a widget already created
        if ( $this->_widget ) {
            $this->_widget->storeValues( $defaults );
        } else {
            foreach ( $this->_fields as $name => $val ) {
                $defaults[$name] = $val[3];
            }
        }
        return $defaults;
    }

    function buildQuickForm( ) {
        $attributes = CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_Widget' );
        
        $this->addElement( 'checkbox',
                           'is_active',
                           ts( 'Widget enabled?' ),
                           null,
                           array( 'onclick' => "widgetBlock(this)" ) );

        foreach ( $this->_fields as $name => $val ) {
            $this->add( $val[1],
                        $name,
                        $val[0],
                        $attributes[$name],
                        $val[2] );
        }

        $this->assign_by_ref( 'fields', $this->_fields );

        parent::buildQuickForm( );
    }

    /** 
     * Return a descriptive name for the page, used in wizard header 
     * 
     * @return string 
     * @access public 
     */ 
    public function getTitle( ) {
        return ts( 'Widget Settings' );
    }

}

?>
