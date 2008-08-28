<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
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
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */
 
 require_once 'CRM/NewImport/DataSource.php';
 
 class CRM_NewImport_DataSource_SQL extends CRM_NewImport_DataSource {
     
     // docs inherited from parent
     public function getInfo() {
         return array( 'title' => 'SQL Import' );
     }
     
     // docs inherited from parent
     public function preProcess( &$form ) {
         // nop
     }
     
     // docs inherited from parent
     public function buildQuickForm( &$form ) {
         $form->add( 'textarea', 'sqlQuery', ts('Specify SQL Query' ),
             'rows=10 cols=45');
     }
     
     // docs inherited from parent
     public function postProcess( &$params, &$db ) {
         $sqlQuery = $params['sqlQuery'];
         
         $tableSuffix = time();
         $importTableName = "civicrm_import_job_$tableSuffix";
         $dropQuery = "DROP TABLE IF EXISTS $importTableName";
         $sqlQuery = "CREATE TABLE $importTableName " . $sqlQuery;
         //print "Running query: $sqlQuery<br/><br/>";
         
         // Execute the query
         $db->query($dropQuery);
         $db->query($sqlQuery);
         
         // Set some session variables
         $this->set( 'importTableName', $importTableName );
         
         // See how many records we're importing
         //$countQuery = "SELECT COUNT(*) FROM $importTableName";
         //$count = $db->getOne($countQuery);
         //print "Importing $count records<br/><br/>";
     }
 }