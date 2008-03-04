<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
                                 ts('State')        => 'state_province' );
    }

    function buildForm( &$form ) {
        $groups         =& CRM_Core_PseudoConstant::group( );

        require_once 'CRM/Core/QuickForm/GroupMultiSelect.php';
        $inGroupsSelect =& new CRM_Core_QuickForm_GroupMultiSelect( 'includeGroups',
                                                                    ts('Include Group(s)') . ' ', $groups,
                                                                    array( 'size'  => 5,
                                                                           'style' => 'width:240px',
                                                                           'class' => 'advmultiselect' )
                                                                    );
        $inG =& $form->addElement( $inGroupsSelect );
        
        $outGroupsSelect =& new CRM_Core_QuickForm_GroupMultiSelect( 'excludeGroups',
                                                                     ts('Exclude Group(s)') . ' ', $groups,
                                                                     array( 'size'  => 5,
                                                                            'style' => 'width:240px',
                                                                            'class' => 'advmultiselect' )
                                                                     );
        $outG =& $form->addElement($outGroupsSelect);
        
        $inG->setButtonAttributes('add', array('value' => ts('Add >>')));;
        $outG->setButtonAttributes('add', array('value' => ts('Add >>')));;
        $inG->setButtonAttributes('remove', array('value' => ts('<< Remove')));;
        $outG->setButtonAttributes('remove', array('value' => ts('<< Remove')));;

        /**
         * if you are using the standard template, this array tells the template what elements
         * are part of the search criteria
         */
        $form->assign( 'elements', array( 'includeGroups', 'excludeGroups' ) );
    }

    function all( $offset = 0, $rowcount = 0, $sort = null,
                  $includeContactIDs = false ) {

        $includeGroups   = CRM_Utils_Array::value( 'includeGroups',
                                          $this->_formValues );

        $excludeGroups   = CRM_Utils_Array::value( 'excludeGroups',
                                          $this->_formValues );

        //creation of temporary tablle

        
        $selectClause = "
contact_a.id           as contact_id  ,
contact_a.contact_type as contact_type,
contact_a.sort_name    as sort_name,
state_province.name    as state_province
";
        return $this->sql( $selectClause,
                           $offset, $rowcount, $sort,
                           $includeContactIDs, null );

    }
    
    function from( ) {
        return "
FROM      civicrm_contact contact_a
LEFT JOIN civicrm_address address ON ( address.contact_id       = contact_a.id AND
                                       address.is_primary       = 1 )
LEFT JOIN civicrm_email           ON ( civicrm_email.contact_id = contact_a.id AND
                                       civicrm_email.is_primary = 1 )
LEFT JOIN civicrm_state_province state_province ON state_province.id = address.state_province_id
";
    }

    function where( $includeContactIDs = false ) {
        $params = array( );
        $where  = "contact_a.contact_type   = 'Household'";

        $count  = 1;
        $clause = array( );
        $name   = CRM_Utils_Array::value( 'household_name',
                                          $this->_formValues );
        if ( $name != null ) {
            if ( strpos( $name, '%' ) === false ) {
                $name = "%{$name}%";
            }
            $params[$count] = array( $name, 'String' );
            $clause[] = "contact_a.household_name LIKE %{$count}";
            $count++;
        }

        $state = CRM_Utils_Array::value( 'state_province_id',
                                         $this->_formValues );
        if ( $state ) {
            $params[$count] = array( $state, 'Integer' );
            $clause[] = "state_province.id = %{$count}";
        }

        if ( ! empty( $clause ) ) {
            $where .= ' AND ' . implode( ' AND ', $clause );
        }

        return $this->whereClause( $where, $params );
    }

    function templateFile( ) {
        return 'CRM/Contact/Form/Search/Custom/Sample.tpl';
    }

}


