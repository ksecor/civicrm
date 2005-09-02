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
require_once 'CRM/Import/Parser.php';

/**
 * This class gets the name of the file to upload
 */
class CRM_Import_Form_UploadFile extends CRM_Core_Form {
   
    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) {

        //Setting Upload File Size
        $config =& new CRM_Core_Config();
        if ($config->maxImportFileSize >= 8388608 ) {
            $uploadFileSize = 8388608;
        } else {
            $uploadFileSize = $config->maxImportFileSize;
        }
        $uploadSize = round(($uploadFileSize / (1024*1024)), 2);
                
        //CRM_Core_Error::debug('uploadFileSize1',$config->importFileSize);
        
        $this->assign('uploadSize', $uploadSize );
        
        $this->addElement( 'file', 'uploadFile', ts('Import Data File'), 'size=30 maxlength=60' );

        $this->addRule( 'uploadFile', ts('File size should be less than %1 MBytes (%2 bytes)', array(1 => $uploadSize, 2 => $uploadFileSize)), 'maxfilesize', $uploadFileSize );
        $this->setMaxFileSize( $uploadFileSize );
        $this->addRule( 'uploadFile', ts('Input file must be in CSV format'), 'asciiFile' );

        $this->addElement( 'checkbox', 'skipColumnHeader', ts('First row contains column headers') );

        $duplicateOptions = array();        
        $duplicateOptions[] = HTML_QuickForm::createElement('radio',
            null, null, ts('Skip'), CRM_Import_Parser::DUPLICATE_SKIP);
        //$duplicateOptions[] = HTML_QuickForm::createElement('radio',
        //    null, null, ts('Replace'), CRM_Import_Parser::DUPLICATE_REPLACE);
        $duplicateOptions[] = HTML_QuickForm::createElement('radio',
            null, null, ts('Update'), CRM_Import_Parser::DUPLICATE_UPDATE);
        $duplicateOptions[] = HTML_QuickForm::createElement('radio',
            null, null, ts('Fill'), CRM_Import_Parser::DUPLICATE_FILL);
        $duplicateOptions[] = HTML_QuickForm::createElement('radio',
            null, null, ts('No Duplicate Checking'), CRM_Import_Parser::DUPLICATE_NOCHECK);
        
        $this->addGroup($duplicateOptions, 'onDuplicate', 
                        ts('On duplicate entries'));
        $this->setDefaults(array('onDuplicate' =>
                                    CRM_Import_Parser::DUPLICATE_SKIP));

        $this->addButtons( array(
                                 array ( 'type'      => 'upload',
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
        $fileName         = $this->controller->exportValue( $this->_name, 'uploadFile' );
        $skipColumnHeader = $this->controller->exportValue( $this->_name, 'skipColumnHeader' );
        $onDuplicate      = $this->controller->exportValue( $this->_name,
                            'onDuplicate' );
        
        $this->set('onDuplicate', $onDuplicate);

        $seperator = ',';
        $mapper = array( );

        $parser =& new CRM_Import_Parser_Contact( $mapper );
        $parser->setMaxLinesToProcess( 100 );
        $parser->run( $fileName, $seperator,
                      $mapper,
                      $skipColumnHeader,
                      CRM_Import_Parser::MODE_MAPFIELD );

        // add all the necessary variables to the form
        $parser->set( $this );
    }

    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle( ) {
        return ts('Upload Data');
    }

}

?>
