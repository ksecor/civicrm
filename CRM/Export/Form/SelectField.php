<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
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
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

/**
 * This class gets the name of the file to upload
 */
class CRM_Export_Form_SelectField extends CRM_Core_Form {
   
    /**
     * The array that holds all the contact ids
     *
     * @var array
     */
    protected $_contactIds;

    /**
     * various Contact types
     */
    const
        EXPORT_ALL      = 1,
        EXPORT_SELECTED = 2;
    
    /**
     * build all the data structures needed to build the form
     *
     * @param
     * @return void
     * @access public
     */
    function preProcess( ) 
    {
        $this->_contactIds = array( );

        //get the no of contacts selected from the session
        $session = & CRM_Core_Session::singleton();
        $values = $session->get('formValues', 'CRM_Contact_Controller_Search');

        // all contacts or action = save a search
        if ($values['radio_ts'] == 'ts_all')  {
            // need to perform action on all contacts
            // fire the query again and get the contact id's + display name
            $contact =& new CRM_Contact_BAO_Contact();
            $ids = $contact->searchQuery( $values, 0, 0, null,
                                          false, false, false,
                                          true, false );
            $this->_contactIds = explode( ',', $ids );
            $session->set('selectedAll',true);
        } else if($values['radio_ts'] == 'ts_sel') {
            // selected contacts only
            // need to perform action on only selected contacts
            foreach ( $values as $name => $value ) {
                if ( substr( $name, 0, CRM_Core_Form::CB_PREFIX_LEN ) == CRM_Core_Form::CB_PREFIX ) {
                    $this->_contactIds[] = substr( $name, CRM_Core_Form::CB_PREFIX_LEN );
                }
            }
            $session->set('selectedAll',false);
        }
        
        $session->set('contactIds', $this->_contactIds);
        $this->assign( 'totalSelectedContacts', count( $this->_contactIds ) );
    }


    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm( ) {
        //export option
        $exportoptions = array();        
        $exportOptions[] = HTML_QuickForm::createElement('radio',
                                                         null, null, ts('Export ALL contact fields'), CRM_Export_Form_SelectField::EXPORT_ALL);
        $exportOptions[] = HTML_QuickForm::createElement('radio',
                                                         null, null, ts('Select fields for export'), CRM_Export_Form_SelectField::EXPORT_SELECTED);

        $this->addGroup($exportOptions, 'exportOption', ts('Export Type'), '<br/>');

        $this->setDefaults(array('exportOption' => CRM_Export_Form_SelectField::EXPORT_ALL ));


        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Continue >>'),
                                         'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );
    }

    /**
     * Process the uploaded file
     *
     * @return void
     * @access public
     */
    public function postProcess( ) {
        $exportOption = $this->controller->exportValue( $this->_name, 'exportOption' ); 
        
        if ($exportOption == CRM_Export_Form_SelectField::EXPORT_ALL) {
            require_once 'CRM/Export/BAO/Export.php';
            $export =& new CRM_Export_BAO_Export();
            $export->exportContacts();
        }
        
    }

    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle( ) {
        return ts('Select Fields');
    }

}

?>
