<?php 

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/ 
 
  /** 
   * Class representing the table relationships
   * 
   * @package CRM 
   * @copyright CiviCRM LLC (c) 2004-2007 
   * $Id$ 
   * 
   */ 

class CRM_Core_TableHierarchy {

    /**
     * This array defines weights for table, which are used to sort array of table in from clause
     * @var array
     * @static
     */
    static $info = array(
                         'civicrm_contact'              => '01',
                         'civicrm_individual'           => '02',
                         'civicrm_household'            => '03',
                         'civicrm_organization'         => '04',
                         'quest_student'                => '05',
                         'quest_student_summary'        => '05',
                         'civicrm_individual_prefix'    => '06',
                         'civicrm_individual_suffix'    => '07',
                         'civicrm_gender'               => '08',
                         'civicrm_address'              => '09',
                         'civicrm_location_type'        => '10',
                         'civicrm_county'               => '12',
                         'civicrm_state_province'       => '13',
                         'civicrm_country'              => '14',
                         'civicrm_email'                => '15',
                         'civicrm_phone'                => '16',
                         'civicrm_im'                   => '17',
                         'civicrm_im_provider'          => '18',
                         'civicrm_group_contact'        => '19',
                         'civicrm_group'                => '20',
                         'civicrm_subscription_history' => '21',
                         'civicrm_entity_tag'           => '22',
                         'civicrm_note'                 => '23',
                         'civicrm_activity_history'     => '24',
                         'civicrm_custom_value'         => '25',
                         'civicrm_contribution'         => '26',
                         'civicrm_contribution_type'    => '27',
                         'civicrm_participant'          => '28',
                         'civicrm_event'                => '29',
                         );

    static function &info( ) {
        return self::$info;
    }

}

?>
