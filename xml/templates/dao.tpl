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

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Core/DAO.php';

class {$table.className} extends CRM_DAO {ldelim}

     /**
      * static instance to hold the table name
      *
      * @var string
      * @static
      */
      static $_tableName = '{$table.name}';

     /**
      * static instance to hold the field values
      *
      * @var array
      * @static
      */
      static $_fields;

     /**
      * static instance to hold the FK relationships
      *
      * @var string
      * @static
      */
      static $_links;

     /**
      * static instance to hold the values that can
      * be imported / apu
      *
      * @var array
      * @static
      */
      static $_import;

{foreach from=$table.fields item=field}
    /**
{if $field.comment}
     * {$field.comment}
{/if}
     *
     * @var {$field.phpType}
     */
    public ${$field.name};

{/foreach} {* table.fields *}	

    /**
     * class constructor
     *
     * @access public
     * @return {$table.name}
     */
    function __construct( ) {ldelim}
        parent::__construct( );
    {rdelim}

{if $table.foreignKey}
    /**
     * return foreign links
     *
     * @access public
     * @return array
     */
    function &links( ) {ldelim}
        if ( ! isset( self::$_links ) ) {ldelim}
	     self::$_links = array(
{foreach from=$table.foreignKey item=foreign}
                                   '{$foreign.name}' => '{$foreign.table}:{$foreign.key}',
{/foreach}
                             );
        {rdelim}
        return self::$_links;
    {rdelim}
{/if} {* table.foreignKey *}

      /**
       * returns all the column names of this table
       *
       * @access public
       * @return array
       */
      function &fields( ) {ldelim}
          if ( ! isset( self::$_fields ) ) {ldelim}
               self::$_fields = array (
{foreach from=$table.fields item=field}
                                            '{$field.name}' => array( 
                                                                      'type'      => {$field.crmType},
{if $field.title}
                                                                      'title'     => '{$field.title}',
{/if}
{if $field.required}
					                              'required'  => {$field.required},
{/if} {* field.required *}
{if $field.length}
								      'maxlength' => {$field.length},
{/if} {* field.length *}
{if $field.size}
								      'size'      => {$field.size},
{/if} {* field.size *}
{if $field.rows}
								      'rows'      => {$field.rows},
{/if} {* field.rows *}
{if $field.cols}
								      'cols'      => {$field.cols},
{/if} {* field.cols *}
{if $field.import}
								      'import'    => {$field.import},
{/if} {* field.import *}
                                                                    ), 
{/foreach} {* table.fields *}
                                      );
          {rdelim}
          return self::$_fields;   
      {rdelim}

      /**
       * returns the names of this table
       *
       * @access public
       * @return string
       */
      function getTableName( ) {ldelim}
          return self::$_tableName;
      {rdelim}

      /**
       * returns the list of fields that can be imported
       *
       * @access public
       * return array
       */
       function &import( $prefix = false ) {ldelim}
          if ( ! isset( self::$_import ) ) {ldelim}
               self::$_import = array ( );
               $fields =& self::fields( );
               foreach ( $fields as $name => &$field ) {ldelim}
                 if ( CRM_Array::value( 'import', $field ) ) {ldelim}
                   if ( $prefix ) {ldelim}
                     self::$_import['{$table.objectName}.' . $name] =& $field;
                   {rdelim} else {ldelim}
                     self::$_import[$name] =& $field;
                   {rdelim}
                 {rdelim}
               {rdelim}
               {if $table.foreignKey}
                  {foreach from=$table.foreignKey item=foreign}
                     {if $foreign.import}
                        self::$_import = array_merge( self::$_import,
						      {$foreign.className}::import( true ) );
                     {/if}
                  {/foreach}
               {/if}
                
          {rdelim}
          return self::$_import;
      {rdelim}

{rdelim}

?>