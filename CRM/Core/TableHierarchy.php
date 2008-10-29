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
                         'civicrm_county'               => '10',
                         'civicrm_state_province'       => '11',
                         'civicrm_country'              => '12',
                         'civicrm_email'                => '13',
                         'civicrm_phone'                => '14',
                         'civicrm_im'                   => '15',
                         'civicrm_im_provider'          => '16',
                         'civicrm_location_type'        => '17',
                         'civicrm_group_contact'        => '18',
                         'civicrm_group'                => '19',
                         'civicrm_subscription_history' => '20',
                         'civicrm_entity_tag'           => '21',
                         'civicrm_note'                 => '22',
                         'civicrm_contribution'         => '23',
                         'civicrm_contribution_type'    => '24',
                         'civicrm_participant'          => '25',
                         'civicrm_event'                => '26',
                         'civicrm_worldregion'          => '27',
                         'civicrm_case_contact'         => '28',
                         'civicrm_case'                 => '29',
                         'case_relationship'            => '30',
                         'case_relation_type'           => '31'
                         );

    static function &info( ) {
        return self::$info;
    }

}


