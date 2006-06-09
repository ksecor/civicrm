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
   * Class representing the table relationships
   * 
   * @package CRM 
   * @author Donald A. Lobo <lobo@yahoo.com> 
   * @copyright Donald A. Lobo (c) 2005 
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
                         'civicrm_individual_prefix'    => '06',
                         'civicrm_individual_suffix'    => '07',
                         'civicrm_gender'               => '08',
                         'civicrm_location'             => '09',
                         'civicrm_location_type'        => '10',
                         'civicrm_address'              => '11',
                         'civicrm_state_province'       => '12',
                         'civicrm_country'              => '13',
                         'civicrm_email'                => '14',
                         'civicrm_phone'                => '15',
                         'civicrm_im'                   => '16',
                         'civicrm_im_provider'          => '17',
                         'civicrm_group_contact'        => '18',
                         'civicrm_group'                => '19',
                         'civicrm_subscription_history' => '20',
                         'civicrm_entity_tag'           => '21',
                         'civicrm_note'                 => '22',
                         'civicrm_activity_history'     => '23',
                         'civicrm_custom_value'         => '24',
                         'civicrm_contribution'         => '25',
                         'civicrm_contribution_type'    => '26',
                         );

    static function &info( ) {
        return self::$info;
    }

}

?>