<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.3                                                |
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

class CRM_Report_Form_Instance {

    static function buildForm( &$form ) {
        $attributes = CRM_Core_DAO::getAttribute( 'CRM_Report_DAO_Instance' );

        $form->add( 'text',
                    'title',
                    ts( 'Report Title' ),
                    $attributes['title'] );

        $form->add( 'text',
                    'to_emails',
                    ts( 'To' ),
                    $attributes['to_emails'] );
        
        $form->add( 'text',
                    'cc_emails',
                    ts( 'CC' ),
                    $attributes['cc_emails'] );
        
        $form->add( 'textarea',
                    'report_header',
                    ts( 'Report Header' ),
                    $attributes['header'] );
        
        $form->add( 'textarea',
                    'report_footer',
                    ts( 'Report Footer' ),
                    $attributes['footer'] );
        
        $form->addButtons( array(
                                 array ( 'type'      => 'submit',
                                         'name'      => ts('Save Report'),
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );

        $form->addFormRule( array( 'CRM_Report_Form_Instance', 'formRule' ), $form );
    }

    static function formRule( &$fields, &$errors, &$self ) {
        $buttonName = $self->controller->getButtonName( );
        $selfButtonName = $self->getVar( '_instanceButtonName' );
        
        $errors = array( );
        if ( $selfButtonName == $buttonName ) {
            if ( empty( $fields['title'] ) ) {
                $errors['title'] = ts( 'Title is a required field' );
            }
        }

        return empty( $errors ) ? true : $errors;
    }

    static function setDefaultValues( &$form, &$defaults ) {
        $instanceID = $form->getVar( '_id' );
        if ( $instanceID ) {
            // this is already retrieved via Form.php
            $defaults['report_header'] = $defaults['header'];
            $defaults['report_footer'] = $defaults['footer'];
        } else {
            $defaults['report_header'] = "<html>
  <head>
    <title>CiviCRM Report</title>
  </head>
  <body>";

            $defaults['report_footer'] = "  </body>
</html>
";
        }
         
    }

    static function postProcess( &$form ) {
        $params = $form->getVar( '_params' );

        $params['header'] = $params['report_header'];
        $params['footer'] = $params['report_footer'];

        require_once 'CRM/Report/DAO/Instance.php';
        $dao = new CRM_Report_DAO_Instance( );
        $dao->copyValues( $params );

        // unset all the params that we use
        $fields = array( 'title', 'to_emails', 'cc_emails', 'header', 'footer',
                         'qfKey', '_qf_default', 'report_header', 'report_footer' );
        foreach ( $fields as $field ) {
            unset( $params[$field] );
        }
        $dao->form_values = serialize( $params );

        $instanceID = $form->getVar( '_id' );
        if ( $instanceID ) {
            $dao->id = $instanceID;
        }


        $dao->save( );

        $form->set( 'id', $dao->id );
    }

}



