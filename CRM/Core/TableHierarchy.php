<?php 
/* 
 +--------------------------------------------------------------------+ 
 | CiviCRM version 1.3                                                | 
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
                         'civicrm_individual_prefix'    => '05',
                         'civicrm_individual_suffix'    => '06',
                         'civicrm_gender'               => '07',
                         'civicrm_location'             => '08',
                         'civicrm_location_type'        => '09',
                         'civicrm_address'              => '10',
                         'civicrm_state_province'       => '11',
                         'civicrm_country'              => '12',
                         'civicrm_email'                => '13',
                         'civicrm_phone'                => '14',
                         'civicrm_im'                   => '15',
                         'civicrm_im_provider'          => '16',
                         'civicrm_group'                => '17',
                         'civicrm_group_contact'        => '18',
                         'civicrm_subscription_history' => '19',
                         'civicrm_entity_tag'           => '20',
                         'civicrm_note'                 => '21',
                         'civicrm_activity_history'     => '22',
                         'civicrm_custom_value'         => '23',
                         'civicrm_contribution'         => '24',
                         'civicrm_contribution_type'    => '25',
                         );

}

?>