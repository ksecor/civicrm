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
 * 
 * 
 * @package CRM 
 * @author Donald A. Lobo <lobo@yahoo.com> 
 * @copyright Donald A. Lobo (c) 2005 
 * $Id$ 
 * 
 */ 

class CRM_Member_BAO_Query {

    static function buildSearchForm( &$form ) {
        
        require_once 'CRM/Member/PseudoConstant.php';
        
        foreach (CRM_Member_PseudoConstant::membershipType( ) as $ID => $Name) {
            $form->_membershipType =& $form->addElement('checkbox', "membership_type[$ID]", null,$Name);
        }
        foreach (CRM_Member_PseudoConstant::membershipStatus( ) as $sId => $sName) {
            $form->_membershipStatus =& $form->addElement('checkbox', "membership_status[$sId]", null,$sName);
        }

        $form->addElement( 'text', 'source', ts( 'Source' ) );
        $form->addElement('date', 'member_since', ts('Member From :'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('member_since', ts('Select a valid date.'), 'qfDate'); 
 
        // Date selects for date 
        $form->add('date', 'sign_up_from', ts('Sign up/Renew Date - From'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('sign_up_from', ts('Select a valid date.'), 'qfDate'); 
 
        $form->add('date', 'sign_up_to', ts('To'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('sign_up_to', ts('Select a valid date.'), 'qfDate'); 

        $form->add('date', 'end_date_from', ts('End Date - From'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('end_date_from', ts('Select a valid date.'), 'qfDate'); 
 
        $form->add('date', 'end_date_to', ts('To'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('end_date_to', ts('Select a valid date.'), 'qfDate'); 





    }

}

?>
