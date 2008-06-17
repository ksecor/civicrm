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

class CRM_PledgeBank_BAO_Query 
{
    
    static function &getFields( ) 
    {
        $fields = array( );
        require_once 'CRM/PledgeBank/DAO/Pledge.php';
        $fields = array_merge( $fields, CRM_PledgeBank_DAO_Pledge::import( ) );
        return $fields;
    }

    /** 
     * build select for PledgeBank
     * 
     * @return void  
     * @access public  
     */
    static function select( &$query ) 
    {
        if ( ( $query->_mode & CRM_Contact_BAO_Query::MODE_PLEDGEBANK ) ||
             CRM_Utils_Array::value( 'signer_id', $query->_returnProperties ) ) {
            $query->_select['signer_id'] = "civicrm_pledgesigner.id as signer_id";
            $query->_element['signer_id'] = 1;
            $query->_tables['civicrm_pledgesigner'] = $query->_whereTables['civicrm_pledgesigner'] = 1;
        }
    }

    static function where( &$query ) 
    {
        $grouping = null;
        foreach ( array_keys( $query->_params ) as $id ) {
            if ( substr( $query->_params[$id][0], 0, 7) == 'pledge_' ||
                 substr( $query->_params[$id][0], 0, 7) == 'signer_') {
                if ( $query->_mode == CRM_Contact_BAO_QUERY::MODE_CONTACTS ) {
                    $query->_useDistinct = true;
                }
                self::whereClauseSingle( $query->_params[$id], $query );
            }
        }
    }
    
  
    static function whereClauseSingle( &$values, &$query ) 
    {
        list( $name, $op, $value, $grouping, $wildcard ) = $values;
        switch( $name ) {

        case 'signer_id':
            $query->_where[$grouping][] = "civicrm_pledgesigner.id $op $value";
            $query->_tables['civicrm_pledgesigner'] = $query->_whereTables['civicrm_pledgesigner'] = 1;
            return;
        }
    }

    static function from( $name, $mode, $side ) 
    {
        $from = null;
        switch ( $name ) {
        
        case 'civicrm_pledgesigner':
            if ( $mode & CRM_Contact_BAO_Query::MODE_PLEDGEBANK ) {
                $from = " INNER JOIN civicrm_pledgesigner ON civicrm_pledgesigner.contact_id = contact_a.id ";
            }  else {
                $from = " $side JOIN civicrm_pledgesigner ON civicrm_pledgesigner.contact_id = contact_a.id ";
            }
            break;
        }
        return $from;
    }

    /**
     * getter for the qill object
     *
     * @return string
     * @access public
     */
    function qill( ) {
        return (isset($this->_qill)) ? $this->_qill : "";
    }
   
    static function defaultReturnProperties( $mode ) 
    {
        $properties = null;
        if ( $mode & CRM_Contact_BAO_Query::MODE_PLEDGEBANK ) {
            $properties = array(  
                                'contact_type'  => 1, 
                                'sort_name'     => 1, 
                                'display_name'  => 1,
                                'signer_id'     => 1 );
        }
        return $properties;
    }

    static function buildSearchForm( &$form ) 
    {

        $form->assign( 'validPledgeBank', true );
    }
    
    static function searchAction( &$row, $id ) 
    {
    }

    static function tableNames( &$tables ) 
    {

    }
  
}


