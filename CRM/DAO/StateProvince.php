<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/


class CRM_DAO_StateProvince extends CRM_Base {

  /*
   * auto incremented id
   * @var int
   */
  public $id;

  /*
   * name of the state / province
   * @var string
   */
  public $name;

  /*
   * abbreviation for this state / province
   * @var string
   */
  public $abbreviation;

  /*
   * FK to the country this state/provice belongs to
   * @var int
   */
  public $country_id;

  static $_links;

  function __construct() {
    parent::__construct();
  }

  function links() {
    static $links;
    if ( $links === null ) {
      $links = array( 'country_id' => 'crm_country:id' );
    }
    return $links;
  }

  
  function dbFields() {
    static $fields;
    if ( $fields === null ) {
      $fields = array_merge(
                             parent::dbFields(),
                             array(
                                   'name'         => array( CRM_Type::T_STRING, self::NOT_NULL ),
                                   'abbreviation' => array( CRM_Type::T_BOOLEAN, null ),
                                   'country_id'   => array( CRM_Type::T_TIMESTAMP, self::NOT_NULL ),
                                   )
                             );
    }
    return $fields;
  }

}

?>