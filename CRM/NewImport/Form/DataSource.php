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
require_once 'CRM/NewImport/Parser/Contact.php';

/**
 * This class delegates to the chosen DataSource to grab the data to be
 *  imported.
 */
class CRM_NewImport_Form_DataSource extends CRM_Core_Form {
    
    private $_dataSource;
    
    private $_dataSourceIsValid = false;
    
    private $_dataSourceClassFile;
    
    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */
    public function preProcess( ) {
        $this->_dataSourceIsValid = false;
        $this->_dataSource = CRM_Utils_Request::retrieve( 'dataSource', 'String',
                                                          CRM_Core_DAO::$_nullObject );

        $this->_params = $this->controller->exportValues( $this->_name );
        if ( ! $this->_dataSource ) {
            $this->_dataSource = CRM_Utils_Array::value( 'hidden_dataSource',
                                                         $_POST,
                                                         CRM_Utils_Array::value( 'hidden_dataSource',
                                                                                 $this->_params ) );
            $this->assign( 'showOnlyDataSourceFormPane', false );
        } else {
            $this->assign( 'showOnlyDataSourceFormPane', true );
        }
        
        if ( strpos( $this->_dataSource, 'CRM_NewImport_DataSource_' ) === 0 ) {
            $this->_dataSourceIsValid = true;
            $this->assign( 'showDataSourceFormPane', true );
            $dataSourcePath = split( '_', $this->_dataSource );
            $templateFile = "CRM/NewImport/Form/" . $dataSourcePath[3] . ".tpl";
            $this->assign( 'dataSourceFormTemplateFile', $templateFile );
        }
    }
    
    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    
    public function buildQuickForm( ) {
        
        // If there's a dataSource in the query string, we need to load
        // the form from the chosen DataSource class
        if ( $this->_dataSourceIsValid ) {
            $this->_dataSourceClassFile = str_replace( '_', '/', $this->_dataSource ) . ".php";
            require_once $this->_dataSourceClassFile;
            eval( "{$this->_dataSource}::buildQuickForm( \$this );" );
        }
        
        // Get list of data sources and display them as options
        $dataSources = $this->_getDataSources();
        
        $this->assign( 'urlPath'   , "civicrm/newimport" );
        $this->assign( 'urlPathVar', 'snippet=4' );
        
        $this->add( 'select', 'dataSource', ts('Data Source'),
            array('' => ts('- select -'))+$dataSources,
            true,
            array('onchange' => "buildDataSourceFormBlock( this.value );") );
            
        // duplicate handling options
        $duplicateOptions = array();        
        $duplicateOptions[] = HTML_QuickForm::createElement('radio',
            null, null, ts('Skip'), CRM_NewImport_Parser::DUPLICATE_SKIP);
        $duplicateOptions[] = HTML_QuickForm::createElement('radio',
            null, null, ts('Update'), CRM_NewImport_Parser::DUPLICATE_UPDATE);
        $duplicateOptions[] = HTML_QuickForm::createElement('radio',
            null, null, ts('Fill'), CRM_NewImport_Parser::DUPLICATE_FILL);
        $duplicateOptions[] = HTML_QuickForm::createElement('radio',
            null, null, ts('No Duplicate Checking'), CRM_NewImport_Parser::DUPLICATE_NOCHECK);

        $this->addGroup($duplicateOptions, 'onDuplicate', 
                        ts('For Duplicate Contacts'));
                          
        require_once "CRM/Core/BAO/Mapping.php";
        require_once "CRM/Core/OptionGroup.php";
        $mappingArray = CRM_Core_BAO_Mapping::getMappings( CRM_Core_OptionGroup::getValue( 'mapping_type',
                                                                                                           'Import Contact',
                                                                                                           'name' ) );

        $this->assign('savedMapping',$mappingArray);
        $this->addElement('select','savedMapping', ts('Mapping Option'), array('' => ts('- select -'))+$mappingArray, array('onchange' =>  "if (this.value) document.getElementById('loadMapping').disabled = false; else document.getElementById('loadMapping').disabled = true;"));

        $this->setDefaults(array('onDuplicate' =>
                                    CRM_NewImport_Parser::DUPLICATE_SKIP));
            
        // contact types option
        $contactOptions = array();        
        $contactOptions[] = HTML_QuickForm::createElement('radio',
            null, null, ts('Individual'), CRM_NewImport_Parser::CONTACT_INDIVIDUAL);
        $contactOptions[] = HTML_QuickForm::createElement('radio',
            null, null, ts('Household'), CRM_NewImport_Parser::CONTACT_HOUSEHOLD);
        $contactOptions[] = HTML_QuickForm::createElement('radio',
            null, null, ts('Organization'), CRM_NewImport_Parser::CONTACT_ORGANIZATION);

        $this->addGroup($contactOptions, 'contactType', 
                        ts('Contact Type'));

        $this->setDefaults(array('contactType' =>
                                 CRM_NewImport_Parser::CONTACT_INDIVIDUAL));
        
        $this->addButtons( array( 
                                 array ( 'type'         => 'next',
                                         'name'         => ts('Continue >>'),
                                         'spacing'      => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                         'isDefault'    => true ),
                                 array ( 'type'         => 'cancel',
                                         'name'         => ts('Cancel') ),
                                 )
                         );
    }
    
    private function _getDataSources() {
        // Open the data source dir and scan it for class files
        $config = CRM_Core_Config::singleton();
        $dataSourceDir = $config->importDataSourceDir;
        $dataSources = array( );
        #print "DataSource dir: $dataSourceDir<br/><br/>\n";
        if (!is_dir($dataSourceDir)) {
            CRM_Core_Error::fatal( "Import DataSource directory $dataSourceDir does not exist" );
        }
        if (!$dataSourceHandle = opendir($dataSourceDir)) {
            CRM_Core_Error::fatal( "Unable to access DataSource directory $dataSourceDir" );
        }

        while (($dataSourceFile = readdir($dataSourceHandle)) !== false) {
            $fileType = filetype($dataSourceDir . $dataSourceFile);
            $matches = array( );
            if (($fileType == 'file' || $fileType == 'link') &&
                preg_match('/^(.+)\.php$/',$dataSourceFile,$matches)) {
                $dataSourceClass = "CRM_NewImport_DataSource_" . $matches[1];
                $dataSourceName = $matches[1];
                #print "Found DataSource: $dataSourceClass<br/>\n";
                $dataSources[$dataSourceClass] = $dataSourceName;
            }
        }
        closedir($dataSourceHandle);
        return $dataSources;
    }
    
    /**
     * Call the DataSource's postProcess method to take over
     * and then setup some common data structures for the next step
     *
     * @return void
     * @access public
     */
    public function postProcess( ) {
        if ($this->_dataSourceIsValid) {
            // Setup the params array 
            $this->_params = $this->controller->exportValues( $this->_name );
 
            $onDuplicate  = $this->controller->exportValue( $this->_name,
                             'onDuplicate' );
            $contactType  = $this->controller->exportValue( $this->_name, 
                             'contactType' );
            $savedMapping = $this->controller->exportValue( $this->_name, 
                             'savedMapping' );
            $this->set('onDuplicate', $onDuplicate);
            $this->set('contactType', $contactType);
            $this->set('savedMapping', $savedMapping);

            // Get the PEAR::DB object
            $dao = new CRM_Core_DAO();
            $db = $dao->getDatabaseConnection();
            
            require_once $this->_dataSourceClassFile;
            eval( "$this->_dataSource::postProcess( \$this->_params, \$db );" );
            
            // We should have the data in the DB now, parse it
            $importTableName = $this->get( 'importTableName' );
            $fieldNames = $this->_prepareImportTable( $db, $importTableName );
            $mapper = array( );

            $parser =& new CRM_NewImport_Parser_Contact( $mapper );
            $parser->setMaxLinesToProcess( 100 );
            $parser->run( $importTableName, $mapper,
                          CRM_NewImport_Parser::MODE_MAPFIELD, $contactType,
                          $fieldNames['pk'], $fieldNames['status']);
                          
            // add all the necessary variables to the form
            $parser->set( $this );
        } else {
            CRM_Core_Error::fatal("Invalid DataSource on form post. This shouldn't happen!");
        }
    }
    
    /**
     * Add a PK and status column to the import table so we can track our progress
     * Returns the name of the primary key and status columns
     *
     * @return array
     * @access private
     */
    private function _prepareImportTable( $db, $importTableName ) {
        /* TODO: Add a check for an existing _status field;
         *  if it exists, create __status instead and return that
         */
        $statusFieldName = '_status';
        $primaryKeyName  = '_id';
        
        $this->set( 'primaryKeyName', $primaryKeyName );
        $this->set( 'statusFieldName', $statusFieldName );
        
        /* Make sure the PK is always last! We rely on this later.
         * Should probably stop doing that at some point, but it
         * would require moving to associative arrays rather than
         * relying on numerical order of the fields. This could in
         * turn complicate matters for some DataSources, which
         * would also not be good. Decisions, decisions...
         */
        $alterQuery = "ALTER TABLE $importTableName
                       ADD COLUMN $statusFieldName VARCHAR(32)
                            DEFAULT 'NEW' NOT NULL,
                       ADD COLUMN ${statusFieldName}Msg VARCHAR(255),
                       ADD COLUMN $primaryKeyName INT PRIMARY KEY NOT NULL
                               AUTO_INCREMENT";
        $db->query( $alterQuery );
        
        return array('status' => $statusFieldName, 'pk' => $primaryKeyName);
    }
    
    /**
     * Return a descriptive name for the page, used in wizard header
     *
     *
     * @return string
     * @access public
     */
    public function getTitle( ) {
        return "Choose Data Source";
    }
    
}