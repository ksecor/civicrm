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
 * This class previews the uploaded file and returns summary
 * statistics
 */
class CRM_Import_Form_Preview extends CRM_Core_Form {

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        $skipColumnHeader = $this->controller->exportValue( 'UploadFile', 'skipColumnHeader' );
       
        //get the data from the session             
        $dataValues         = $this->get('dataValues');
        $mapper             = $this->get('mapper');
        $invalidRowCount    = $this->get('invalidRowCount');
        $conflictRowCount  = $this->get('conflictRowCount');

        //get the mapping name displayed if the mappingId is set
        $mappingId = $this->get('loadMappingId');
        if ( $mappingId ) {
            $mapDAO =& new CRM_Core_DAO_Mapping();
            $mapDAO->id = $mappingId;
            $mapDAO->find( true );
            $this->assign('loadedMapping', $mappingId);
            $this->assign('savedName', $mapDAO->name);
        }


        if ( $skipColumnHeader ) {
            $this->assign( 'skipColumnHeader' , $skipColumnHeader );
            $this->assign( 'rowDisplayCount', 3 );
        } else {
            $this->assign( 'rowDisplayCount', 2 );
        }
        

        $groups =& CRM_Core_PseudoConstant::group();
        $this->set('groups', $groups);
        
                             
        if ($invalidRowCount) {
            $this->set('downloadErrorRecordsUrl', CRM_Utils_System::url('civicrm/export', 'type=1'));
        }

        if ($conflictRowCount) {
            $this->set('downloadConflictRecordsUrl', CRM_Utils_System::url('civicrm/export', 'type=2'));
        }
        $properties = array( 'mapper', 'locations', 'phones',
                             'dataValues', 'columnCount',
                             'totalRowCount', 'validRowCount', 
                             'invalidRowCount', 'conflictRowCount',
                             'downloadErrorRecordsUrl',
                             'downloadConflictRecordsUrl',
                             'related', 'relatedContactDetails', 'relatedContactLocType',
                             'relatedContactPhoneType'
                    );
                             
        foreach ( $properties as $property ) {
            $this->assign( $property, $this->get( $property ) );
        }
    }

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) {
        $this->addElement( 'checkbox', 'newGroup', ts('Create a new group from imported records'));
        $this->addElement( 'text', 'newGroupName', ts('Name for new group'));
        $this->addElement( 'text', 'newGroupDesc', ts('Description of new group'));
        $this->addFormRule(array('CRM_Import_Form_Preview', 'newGroupRule'));
        
        $groups =& $this->get('groups');
        
        $this->addElement( 'select', 'groups', ts('Join new contacts to existing group(s)'), $groups, array('multiple' => true, 'size' => 5));

        $this->addButtons( array(
                                 array ( 'type'      => 'back',
                                         'name'      => ts('<< Previous') ),
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Import Now >>'),
                                         'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );
    }

    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle( ) {
        return ts('Preview');
    }

    /**
     * Process the mapped fields and map it into the uploaded file
     * preview the file and extract some summary statistics
     *
     * @return void
     * @access public
     */
    public function postProcess( ) {
        $fileName         = $this->controller->exportValue( 'UploadFile', 'uploadFile' );
        $skipColumnHeader = $this->controller->exportValue( 'UploadFile', 'skipColumnHeader' );
        $invalidRowCount    = $this->get('invalidRowCount');
        $conflictRowCount   = $this->get('conflictRowCount');
        $onDuplicate        = $this->get('onDuplicate');
        $newGroup           = $this->controller->exportValue( $this->_name, 'newGroup');
        $newGroupName       = $this->controller->exportValue( $this->_name, 'newGroupName');
        $newGroupDesc       = $this->controller->exportValue( $this->_name, 'newGroupDesc');
        $groups             = $this->controller->exportValue( $this->_name, 'groups');
        $allGroups          = $this->get('groups');

        $seperator = ',';

        $mapper = $this->controller->exportValue( 'MapField', 'mapper' );

        $mapperKeys = array();
        $mapperLocTypes = array();
        $mapperPhoneTypes = array();
        $mapperRelated = array();
        $mapperRelatedContactType = array();
        $mapperRelatedContactDetails = array();
        $mapperRelatedContactLocType = array();
        $mapperRelatedContactPhoneType = array();

        foreach ($mapper as $key => $value) {
            $mapperKeys[$key] = $mapper[$key][0];
            if (is_numeric($mapper[$key][1])) {
                $mapperLocTypes[$key] = $mapper[$key][1];
            } else {
                $mapperLocTypes[$key] = null;
            }
            
            if (!is_numeric($mapper[$key][2])) {
                $mapperPhoneTypes[$key] = $mapper[$key][2];
            } else {
                $mapperPhoneTypes[$key] = null;
            }

            list($id, $first, $second) = explode('_', $mapper[$key][0]);
            if ( ($first == 'a' && $second == 'b') || ($first == 'b' && $second == 'a') ) {
                $relationType =& new CRM_Contact_DAO_RelationshipType();
                $relationType->id = $id;
                $relationType->find(true);
                eval( '$mapperRelatedContactType[$key] = $relationType->contact_type_'.$second.';');
                $mapperRelated[$key] = $mapper[$key][0];
                $mapperRelatedContactDetails[$key] = $mapper[$key][1];
                $mapperRelatedContactLocType[$key] = $mapper[$key][2];
                $mapperRelatedContactPhoneType[$key] = $mapper[$key][3];
            } else {
                $mapperRelated[$key] = null;
                $mapperRelatedContactType[$key] = null;
                $mapperRelatedContactDetails[$key] = null;
                $mapperRelatedContactLocType[$key] = null;
                $mapperRelatedContactPhoneType[$key] = null;
            }
        }

        $parser =& new CRM_Import_Parser_Contact( $mapperKeys, $mapperLocTypes,
                                                  $mapperPhoneTypes, $mapperRelated, $mapperRelatedContactType,
                                                  $mapperRelatedContactDetails, $mapperRelatedContactLocType, 
                                                  $mapperRelatedContactPhonetype);
        $parser->run( $fileName, $seperator, 
                      $mapperKeys,
                      $skipColumnHeader,
                      CRM_Import_Parser::MODE_IMPORT,
                      $this->get('contactType'),
                      $onDuplicate);
        
        // add the new contacts to selected groups
        $contactIds =& $parser->getImportedContacts();
        
        $newGroupId = null;
        if ($newGroup) {
            /* Create a new group */
            $gParams = array(
                             'domain_id'     => CRM_Core_Config::domainID(),
                             'name'          => $newGroupName,
                             'title'         => $newGroupName,
                             'description'   => $newGroupDesc,
                             'is_active'     => true,
                             );
            $group =& CRM_Contact_BAO_Group::create($gParams);
            $groups[] = $newGroupId = $group->id;
        }
        
        if(is_array($groups)) {
            $groupAdditions = array();
            foreach ($groups as $groupId) {
                $addCount =& CRM_Contact_BAO_GroupContact::addContactsToGroup($contactIds, $groupId);
                if ($groupId == $newGroupId) {
                    $name = $newGroupName;
                    $new = true;
                } else {
                    $name = $allGroups[$groupId];
                    $new = false;
                }
                $groupAdditions[] = array(
                                          'url'   => 'civicrm/group/search?reset=1&force=1&context=smog&gid='.$group->id,
                                          'name'  => $name,
                                          'added' => $addCount[1],
                                          'notAdded' => $addCount[2],
                                          'new'   => $new
                                          );
            }
            $this->set('groupAdditions', $groupAdditions);
        }
        
        // add all the necessary variables to the form
        $parser->set( $this, CRM_Import_Parser::MODE_IMPORT );
        
        // check if there is any error occured
        
        $errorStack =& CRM_Core_Error::singleton();
        $errors     = $errorStack->getErrors();
        
        $errorMessage = array();
        
        $config =& CRM_Core_Config::singleton( );
        
        if( is_array( $errors ) ) {
            foreach($errors as $key => $value) {
                $errorMessage[] = $value['message'];
            }
            
            $errorFile = $fileName . '.error.log';
            
            if ( $fd = fopen( $errorFile, 'w' ) ) {
                fwrite($fd, implode('\n', $errorMessage));
            }
            fclose($fd);
            
            $this->set('errorFile', $errorFile);
            $this->set('downloadErrorRecordsUrl', CRM_Utils_System::url('civicrm/export', 'type=1'));
            $this->set('downloadConflictRecordsUrl', CRM_Utils_System::url('civicrm/export', 'type=2'));
        }
    }


    /**
     * function for validation
     *
     * @param array $params (reference) an assoc array of name/value pairs
     *
     * @return mixed true or array of errors
     * @access public
     * @static
     */
    static function newGroupRule( &$params ) {
        if (CRM_Utils_Array::value('_qf_Import_refresh', $_POST)) {
            return true;
        }
        
        /* If we're not creating a new group, accept */
        if (! $params['newGroup']) {
            return true;
        }

        $errors = array();
        
        if ($params['newGroupName'] === '') {
            $errors['newGroupName'] = ts( 'Please enter a name for the new group.');
        } else {
            if (! CRM_Utils_Rule::objectExists($params['newGroupName'],
                    array('CRM_Contact_DAO_Group')))
            {
                $errors['newGroupName'] = ts( "Group '%1' already exists.",
                        array( 1 => $params['newGroupName']));
            }
        }

        return empty($errors) ? true : $errors;
    }
}

?>
