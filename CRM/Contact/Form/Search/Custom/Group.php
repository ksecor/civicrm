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

require_once 'CRM/Contact/Form/Search/Custom/Base.php';

class CRM_Contact_Form_Search_Custom_Group
   extends    CRM_Contact_Form_Search_Custom_Base
   implements CRM_Contact_Form_Search_Interface {

    function __construct( &$formValues ) {
        parent::__construct( $formValues );

        $this->_columns = array( ts('Contact Id')   => 'contact_id'  ,
                                 ts('Contact Type') => 'contact_type',
                                 ts('Name')         => 'sort_name',
                                 ts('Group Name')   => 'name' );
    }

     function __destruct( ) {
        if ($this->_tableName ) {
            
            $sql = "DROP TEMPORARY TABLE I_{$this->_tableName}";
            CRM_Core_DAO::executeQuery( $sql, CRM_Core_DAO::$_nullArray ) ;

            if ( is_array( $this->_excludeGroups ) ){
                $sql = "DROP TEMPORARY TABLE X_{$this->_tableName}";
                CRM_Core_DAO::executeQuery( $sql, CRM_Core_DAO::$_nullArray ) ; }
        }
    }


    function buildForm( &$form ) {
        $groups         =& CRM_Core_PseudoConstant::group( );

        $inG =& $form->addElement('advmultiselect', 'includeGroups', 
                                  ts('Include Group(s)') . ' ', $groups,
                                  array('size'  => 5,
                                        'style' => 'width:240px',
                                        'class' => 'advmultiselect')
                                  );
        
        $outG =& $form->addElement('advmultiselect', 'excludeGroups', 
                                   ts('Exclude Group(s)') . ' ', $groups,
                                   array('size'  => 5,
                                         'style' => 'width:240px',
                                         'class' => 'advmultiselect')
                                   );  
        
        $inG->setButtonAttributes('add',  array('value' => ts('Add >>')));;
        $outG->setButtonAttributes('add', array('value' => ts('Add >>')));;
        $inG->setButtonAttributes('remove',  array('value' => ts('<< Remove')));;
        $outG->setButtonAttributes('remove', array('value' => ts('<< Remove')));;
        
        /**
         * if you are using the standard template, this array tells the template what elements
         * are part of the search criteria
         */
        $form->assign( 'elements', array( 'includeGroups', 'excludeGroups' ) );
    }

    function all( $offset = 0, $rowcount = 0, $sort = null,
                  $includeContactIDs = false ) {

        //creation of temporary tablle

        $this->_includeGroups   = CRM_Utils_Array::value( 'includeGroups', $this->_formValues );
               
        $this->_excludeGroups   = CRM_Utils_Array::value( 'excludeGroups', $this->_formValues );      
  
        $selectClause = "
                      DISTINCT( contact_a.id)  as contact_id  ,
                      contact_a.contact_type as contact_type,
                      contact_a.sort_name    as sort_name,
                      g.name                 as name
                      ";
        return $this->sql( $selectClause,
                           $offset, $rowcount, $sort,
                           $includeContactIDs, null );

    }
    
    function from( ) {

      if ( ! empty( $this->_includeGroups ) ) { 
            $iGroups = implode( ',', $this->_includeGroups );
        } else {
            //if no group selected search for all groups 
            require_once 'CRM/Contact/DAO/Group.php';
            $group = new CRM_Contact_DAO_Group( );
            $group->is_active = 1;
            $group->find();
            while( $group->fetch( ) ) {
                $allGroups[] = $group->id;
            }
            $iGroups = implode( ',',$allGroups );
           
        }
        if ( is_array( $this->_excludeGroups ) ) {
            $xGroups = implode( ',', $this->_excludeGroups );
        } else {
            $xGroups = 0;
        }
        $randomNum = md5( uniqid( ) );
        $this->_tableName = "civicrm_temp_custom_{$randomNum}";
        
        $sql = "CREATE TEMPORARY TABLE X_{$this->_tableName} ( contact_id int primary key) ENGINE=HEAP";                 
        
        CRM_Core_DAO::executeQuery( $sql, CRM_Core_DAO::$_nullArray );
        if( $xGroups != 0 ) {
        $excludeGroup = 
            "INSERT INTO  X_{$this->_tableName} ( contact_id )
                  SELECT  DISTINCT civicrm_group_contact.contact_id
                  FROM civicrm_group_contact, civicrm_contact                    
                  WHERE 
                     civicrm_contact.id = civicrm_group_contact.contact_id AND 
                     civicrm_group_contact.status = 'Added' AND
                     civicrm_group_contact.group_id IN( {$xGroups})";
        
        CRM_Core_DAO::executeQuery( $excludeGroup, CRM_Core_DAO::$_nullArray );
        }
       
        $sql = "CREATE TEMPORARY TABLE I_{$this->_tableName} ( contact_id int primary key) ENGINE=HEAP";
       
        CRM_Core_DAO::executeQuery( $sql, CRM_Core_DAO::$_nullArray );

        $includeGroup = 
            "INSERT INTO I_{$this->_tableName} (contact_id)
                 SELECT DISTINCT     civicrm_contact.id as contact_id
                 FROM                civicrm_contact
                    INNER JOIN       civicrm_group_contact
                            ON       civicrm_group_contact.contact_id = civicrm_contact.id";
        if( $xGroups != 0 ) {
             $includeGroup .= " LEFT JOIN        X_{$this->_tableName}
                                       ON       civicrm_contact.id = X_{$this->_tableName}.contact_id";
        }
        $includeGroup .= " WHERE           
                                     civicrm_group_contact.status = 'Added'  AND
                                     civicrm_group_contact.group_id IN($iGroups)";
        if ( $xGroups != 0 ) {
            $includeGroup .=" AND  X_{$this->_tableName}.contact_id IS null";
        }

        CRM_Core_DAO::executeQuery( $includeGroup, CRM_Core_DAO::$_nullArray );

        return "
                FROM   civicrm_group g, civicrm_group_contact gc , civicrm_contact contact_a
                INNER JOIN I_{$this->_tableName} temptable ON ( contact_a.id = temptable.contact_id )";
    }

    function where( $includeContactIDs = false ) {
        $clauses = array( );
 
        $clauses[] = "g.id = gc.group_id";
        $clauses[] = " contact_a.id = gc.contact_id ";
        return implode( ' AND ', $clauses );
    }

    function templateFile( ) {
        return 'CRM/Contact/Form/Search/Custom/Sample.tpl';
    }

}


