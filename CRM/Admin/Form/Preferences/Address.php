<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
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

require_once 'CRM/Admin/Form/Preferences.php';

/**
 * This class generates form components for Address Section  
 */
class CRM_Admin_Form_Preferences_Address extends CRM_Admin_Form_Preferences
{
    function preProcess( ) {
        parent::preProcess( );

        CRM_Utils_System::setTitle(ts('Settings - Addresses'));

        // add all the checkboxes
        $this->_cbs = array(
                            'address_options'    => ts( 'Address Fields'   ),
                            );
    }

    function setDefaultValues( ) {
        $defaults = array( );

        $defaults['location_count'] =
            isset( $this->_config->location_count ) ? $this->_config->location_count : 1;

        $defaults['address_standardization_provider'] = $this->_config->address_standardization_provider;
        $defaults['address_standardization_userid'] = $this->_config->address_standardization_userid;
        $defaults['address_standardization_url'] = $this->_config->address_standardization_url;

        
        $this->addressSequence = isset($newSequence) ? $newSequence : "";

        if ( empty( $this->_config->address_format ) ) {
            $defaults['address_format'] = "
{street_address}
{supplemental_address_1}
{supplemental_address_2}
{city}{, }{state_province}{ }{postal_code}
{country}
";
        } else {
            $defaults['address_format'] = $this->_config->address_format;
        }

        if ( empty( $this->_config->mailing_format ) ) {
            $defaults['mailing_format'] = "
{street_address}
{supplemental_address_1}
{supplemental_address_2}
{city}{, }{state_province}{ }{postal_code}
{country}
";
        } else {
            $defaults['mailing_format'] = $this->_config->mailing_format;
        }


        if ( empty( $this->_config->individual_name_format ) ) {
            $defaults['individual_name_format'] =
                '{individual_prefix}{ } {first_name}{ }{middle_name}{ }{last_name}{ }{individual_suffix}';
        } else {
            $defaults['individual_name_format'] = $this->_config->individual_name_format;
        }


        parent::cbsDefaultValues( $defaults );

        return $defaults;
    }

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $this->add('text',
                   'location_count',
                   ts('Number of Locations'),
                   CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_Preferences', 'location_count' ) );
        $this->addRule( 'location_count', ts( 'Location count must be a positive integer (e.g. 1 or 2 or ...).' ), 'positiveInteger' );

        // address formatting options
        $this->addElement('text','individual_name_format', ts('Individual Name Format'));
        $this->addElement('textarea','mailing_format', ts('Mailing Label Format'));  
        $this->addElement('textarea','address_format', ts('Display Format'));  

        // Address Standarization
        $this->addElement('text', 'address_standardization_provider', ts('Provider'));
        $this->addElement('text', 'address_standardization_userid'  , ts('User ID'));
        $this->addElement('text', 'address_standardization_url'     , ts('Web Service URL'));

        $this->addFormRule( array( 'CRM_Admin_Form_Preferences_Address', 'formRule' ) );

        parent::buildQuickForm();
    }

    static function formRule( &$fields ) {
        $p = $fields['address_standardization_provider'] ;
        $u = $fields['address_standardization_userid'  ] ;
        $w = $fields['address_standardization_url'     ] ;

        // make sure that there is a value for all of them
        // if any of them are set
        if ( $p || $u || $w ) {
            if ( ! CRM_Utils_System::checkPHPVersion( 5, false ) ) {
                $errors['_qf_default'] = ts( 'Address Standardization features require PHP version 5 or greater.' );
                return $errors;
            }

            if ( ! ( $p && $u && $w ) ) {
                $errors['_qf_default'] = ts( 'You must provide values for all three Address Standarization fields.' );
                return $errors;
            }
        }
        
        return true;
    }

    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        if ( $this->_action == CRM_Core_Action::VIEW ) {
            return;
        }

        $this->_params = $this->controller->exportValues( $this->_name );

        // trim the format and unify line endings to LF
        $format = array( 'address_format', 'mailing_format', 'individual_name_format' );
        foreach ( $format as $f ) {
          if ( ! empty( $this->_params[$f] ) ) {
            $this->_params[$f] = trim( $this->_params[$f] );
            $this->_params[$f] = str_replace(array("\r\n", "\r"), "\n", $this->_params[$f] );
          }
        }

        
        $this->_config->copyValues( $this->_params );

        
        parent::postProcess( );
    }//end of function

}

?>
