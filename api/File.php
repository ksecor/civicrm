<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
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
 | at http://www.openngo.org/faqs/licensing.html                      |
 +--------------------------------------------------------------------+
*/

/**
 * Definition of the Tag of the CRM API. 
 * More detailed documentation can be found 
 * {@link http://objectledge.org/confluence/display/CRM/CRM+v1.0+Public+APIs
 * here}
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

/**
 * Files required for this package
 */
require_once 'api/utils.php'; 

/**
 * Create a file 
 *  
 * This API is used for creating a file
 * 
 * @param   array  $params  an associative array of name/value property values of civicrm_file
 * @return object of newly created file.
 * @access public
 */
function crm_create_file($params) 
{
    if ( ! is_array($params) ) {
        return _crm_error('Params is not an array.');
    }
    
    if ( !isset($params['upload_date']) ) {
        $params['upload_date'] = date("Ymd");
    }
    
    require_once 'CRM/Core/DAO/File.php';
    
    $fileDAO = new CRM_Core_DAO_File();
    $properties = array('id', 'file_type_id', 'mime_type', 'uri', 'document', 'description', 'upload_date');
    
    foreach ($properties as $name) {
        if (array_key_exists($name, $params)) {
            $fileDAO->$name = $params[$name];
        }
    }
    
    $fileDAO->save();
    return $fileDAO;
}

/**
 * Get a file.
 * 
 * This api is used for finding an existing file.
 * Required parameters : id of a file
 * 
 * @params  array $params  an associative array of name/value property values of civicrm_file
 *
 * @return  Array of all found file objects.
 * @access public
 */
function crm_get_file($params) 
{
    if ( ! is_array($params) ) {
        return _crm_error('params is not an array.');
    }
    if ( ! isset($params['id']) ) {
        return _crm_error('Required parameters missing.');
    }
    
    require_once 'CRM/Core/DAO/File.php';
    $fileDAO = new CRM_Core_DAO_File();
    
    $properties = array('id', 'file_type_id', 'mime_type', 'uri', 'document', 'description', 'upload_date');
    
    foreach ( $properties as $name) {
        if (array_key_exists($name, $params)) {
            $fileDAO->$name = $params[$name];
        }
    }
    
    if ( $fileDAO->find() ) {
        while ( $fileDAO->fetch() ) {
            $files[$fileDAO->id] = clone($fileDAO);
        }
    }
    return $files;
}

/**
 * Update an existing file
 *
 * This api is used for updating an existing file.
 * Required parrmeters : id of a file
 * 
 * @param  Array   $params  an associative array of name/value property values of civicrm_file
 * 
 * @return updated file object
 * @access public
 */
function &crm_update_file( &$params ) {
    if ( !is_array( $params ) ) {
        return _crm_error( 'Params is not an array' );
    }
    
    if ( !isset($params['id']) ) {
        return _crm_error( 'Required parameter missing' );
    }
    
    require_once 'CRM/Core/DAO/File.php';
    $fileDAO =& new CRM_Core_DAO_File( );
    $fileDAO->id = $params['id'];
    if ($fileDAO->find(true)) {
        $fileDAO->copyValues( $params );
        if ( !$params['upload_date'] && !$fileDAO->upload_date) {
            $fileDAO->upload_date = date("Ymd");
        }
        $fileDAO->save();
    }
    return $fileDAO;
}

/**
 * Deletes an existing file
 * 
 * This API is used for deleting a file
 * Required parameters : id of a file
 * 
 * @param  Int  $fileId  Id of the file to be deleted
 * 
 * @return null if successfull, object of CRM_Core_Error otherwise
 * @access public
 */
function &crm_delete_file( $fileId ) {
    if ( empty($fileId) ) {
        return _crm_error( 'Required parameter missing' );
    }
    
    require_once 'CRM/Core/DAO/EntityFile.php';
    $entityFileDAO =& new CRM_Core_DAO_EntityFile( );
    $entityFileDAO->file_id = $fileId;
    if ($entityFileDAO->find()) {
        $entityFileDAO->delete();
    }
    
    require_once 'CRM/Core/DAO/File.php';
    $fileDAO =& new CRM_Core_DAO_File( );
    $fileDAO->id = $fileId;
    if ($fileDAO->find(true)) {
        $del = $fileDAO->delete();
    }
    return $del ? null : _crm_error('Error while deleting a file.');
}

/**
 * Assigns an entity to a file
 *
 * @param object  $file            valid file object
 * @param object  $entity          valid entity object
 * @param string  $entity_table    
 *
 * @return object of newly created entity-file object
 * @access public
 */
function crm_create_entity_file(&$file, &$entity, $entity_table = 'civicrm_contact')
{
    require_once 'CRM/Core/DAO/EntityFile.php';
    
    if ( ! isset($file->id) || ! isset($entity->id)) {
        return _crm_error('Required parameters missing');
    }
    
    $params = array('entity_id'    => $entity->id,
                    'file_id'      => $file->id,
                    'entity_table' => $entity_table
                    );
    
    $entityFileDAO =& new CRM_Core_DAO_EntityFile( );
    $entityFileDAO->copyValues( $params );
    $entityFileDAO->save( );
    return $entityFileDAO;
}

/**
 * Returns all files assigned to a single entity instance.
 *
 * @param object $entity         Valid object of one of the supported entity types.
 * @param string $entity_table   
 *
 * @return array of entity-file objects.
 * @access public
 */
function crm_get_files_by_entity(&$entity, $entity_table = 'civicrm_contact')
{
    require_once 'CRM/Core/DAO/EntityFile.php';
    
    if (! isset($entity->id)) {
        return _crm_error('Required parameters missing');
    }
    
    $entityFileDAO =& new CRM_Core_DAO_EntityFile();
    $entityFileDAO->entity_table = $entity_table;
    $entityFileDAO->entity_id = $entity->id;
    if ( $entityFileDAO->find() ) {
        while ($entityFileDAO->fetch()) {
            $files[$entityFileDAO->file_id] = $entityFileDAO;
        }
    } else {
        return _crm_error('Exact match not found');
    }
    return $files;
}

/**
 * Deletes an existing entity file assignment.
 *
 * @param   array $params   an associative array of name/value property values of civicrm_entity_file.
 * 
 * @return  null if successfull, object of CRM_Core_Error otherwise
 * @access public
 */
function crm_delete_entity_file(&$params)
{
    require_once 'CRM/Core/DAO/EntityFile.php';
    
    if ( ! isset($params['id']) && ( !isset($params['entity_id']) || !isset($params['entity_file']) ) ) {
        return _crm_error('Required parameters missing');
    }
    
    $entityFileDAO =& new CRM_Core_DAO_EntityFile( );
    
    $properties = array( 'id', 'entity_id', 'entity_table', 'file_id' );
    foreach ( $properties as $name) {
        if ( array_key_exists($name, $params) ) {
            $entityFileDAO->$name = $params[$name];
        }
    }
    
    return $entityFileDAO->delete() ? null : _crm_error('Error while deleting');
}

?>