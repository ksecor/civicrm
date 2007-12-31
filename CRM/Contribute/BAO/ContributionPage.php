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

require_once 'CRM/Contribute/DAO/ContributionPage.php';

/**
 * This class contains Contribution Page related functions.
 */
class CRM_Contribute_BAO_ContributionPage extends CRM_Contribute_DAO_ContributionPage 
{
    /**
     * takes an associative array and creates a contribution page object
     *
     * @param array $params (reference ) an assoc array of name/value pairs
     *
     * @return object CRM_Contribute_DAO_ContributionPage object 
     * @access public
     * @static
     */
    public static function &create(&$params) 
    {
        $dao =& new CRM_Contribute_DAO_ContributionPage( );
        $dao->copyValues( $params );
        $dao->save( );
        return $dao;
    }

    static function setValues( $id, &$values ) 
    {
        $params = array('id' => $id);

        CRM_Core_DAO::commonRetrieve( 'CRM_Contribute_DAO_ContributionPage', $params, $values );

        // get the amounts and the label
        require_once 'CRM/Core/OptionGroup.php';  
        CRM_Core_OptionGroup::getAssoc( "civicrm_contribution_page.amount.{$id}", $values );

        // get the profile ids
        require_once 'CRM/Core/BAO/UFJoin.php'; 
        $ufJoinParams = array( 'entity_table' => 'civicrm_contribution_page',   
                               'entity_id'    => $id,   
                               'weight'       => 1 ); 
        $values['custom_pre_id'] = CRM_Core_BAO_UFJoin::findUFGroupId( $ufJoinParams ); 
        
        $ufJoinParams['weight'] = 2; 
        $values['custom_post_id'] = CRM_Core_BAO_UFJoin::findUFGroupId( $ufJoinParams );
    }

    /**
     * Function to send the emails
     * 
     * @param int   $contactID        contact id 
     * @param array $values           associated array of fields
     *
     * @return void
     * @access public
     * @static
     */
    static function sendMail( $contactID, &$values ) 
    { 
        require_once "CRM/Core/BAO/UFField.php";

        $gIds = array( );
        $params = array( );
        if ( isset( $values['custom_pre_id'] ) ) {
            $preProfileType = CRM_Core_BAO_UFField::getProfileType( $values['custom_pre_id'] );
            if ( $preProfileType == 'Membership' ) {
                $params['custom_pre_id'] = array( array( 'member_id', '=', $values['membership_id'], 0, 0 ) );
            } else if ( $preProfileType == 'Contribution' ) {
                $params['custom_pre_id'] = array( array( 'contribution_id', '=', $values['contribution_id'], 0, 0 ) );
            }
            
            $gIds['custom_pre_id'] = $values['custom_pre_id'];
        }

        if ( isset( $values['custom_post_id'] ) ) {
            $postProfileType = CRM_Core_BAO_UFField::getProfileType( $values['custom_post_id'] );
            if ( $postProfileType == 'Membership' ) {
                $params['custom_post_id'] = array( array( 'member_id', '=', $values['membership_id'], 0, 0 ) );
            } else if ( $postProfileType == 'Contribution' ) {
                $params['custom_post_id'] = array( array( 'contribution_id', '=', $values['contribution_id'], 0, 0 ) );
            }

            $gIds['custom_post_id'] = $values['custom_post_id'];
        }
        
        //send notification email if field values are set (CRM-1941)
        require_once 'CRM/Core/BAO/UFGroup.php';
        foreach ( $gIds as $key => $gId ) {
            $email = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_UFGroup', $gId, 'notify' );
            if ( $email ) {
                $val = CRM_Core_BAO_UFGroup::checkFieldsEmptyValues( $gId, $contactID, $params[$key] );
                CRM_Core_BAO_UFGroup::commonSendMail($contactID, $val); 
            }
        }

        if ( $values['is_email_receipt'] ) {
            $template =& CRM_Core_Smarty::singleton( );

            require_once 'CRM/Contact/BAO/Contact.php';
            list( $displayName, $email ) = CRM_Contact_BAO_Contact::getEmailDetails( $contactID );
            self::buildCustomDisplay( $values['custom_pre_id'] , 'customPre' , $contactID, $template, $params['custom_pre_id'] );
            self::buildCustomDisplay( $values['custom_post_id'], 'customPost', $contactID, $template, $params['custom_post_id'] );

            // set email in the template here
            $template->assign( 'email', $email );

            $subject = trim( $template->fetch( 'CRM/Contribute/Form/Contribution/ReceiptSubject.tpl' ) );
            $message = $template->fetch( 'CRM/Contribute/Form/Contribution/ReceiptMessage.tpl' );
            
            $receiptFrom = '"' . CRM_Utils_Array::value('receipt_from_name',$values) . '" <' . $values['receipt_from_email'] . '>';
            require_once 'CRM/Utils/Mail.php';
            CRM_Utils_Mail::send( $receiptFrom,
                                  $displayName,
                                  $email,
                                  $subject,
                                  $message,
                                  CRM_Utils_Array::value( 'cc_receipt' , $values ),
                                  CRM_Utils_Array::value( 'bcc_receipt', $values )
                                  );
        }
    }
    
    /**  
     * Function to add the custom fields for contribution page (ie profile)
     * 
     * @param int    $gid            uf group id
     * @param string $name 
     * @param int    $cid            contact id
     * @param array  $params         params to build component whereclause
     *   
     * @return void  
     * @access public
     * @static  
     */ 
    function buildCustomDisplay( $gid, $name, $cid, &$template, &$params ) 
    {
        if ( $gid ) {
            require_once 'CRM/Core/BAO/UFGroup.php';
            if ( CRM_Core_BAO_UFGroup::filterUFGroups($gid, $cid) ){
                $values = array( );
                $groupTitle = null;
                $fields = CRM_Core_BAO_UFGroup::getFields( $gid, false, CRM_Core_Action::VIEW );
                
                CRM_Core_BAO_UFGroup::getValues( $cid, $fields, $values , false, $params );
                foreach( $fields as $v  ) {
                    if ( ! $groupTitle ) { 
                        $groupTitle = $v["groupTitle"];
                    } else {
                        break;
                    }
                }

                if ( $groupTitle ) {
                    $template->assign( $name."_grouptitle", $groupTitle );
                }

                if ( count( $values ) ) {
                    $template->assign( $name, $values );
                }
            }
        }
    }
  
    /**
     * This function is to make a copy of a contribution page, including
     * all the blocks in the page
     *
     * @param int $id the contribution page id to copy
     *
     * @return the copy object 
     * @access public
     * @static
     */
    static function copy( $id ) 
    {
        $fieldsToPrefix = array( 'title' => ts( 'Copy of ' ) );

        $copy =& CRM_Core_DAO::copyGeneric( 'CRM_Contribute_DAO_ContributionPage', 
                                            array( 'id' => $id ), 
                                            null, 
                                            $fieldsToPrefix );
        
        //copying all the blocks pertaining to the contribution page
        $copyMembershipBlock =& CRM_Core_DAO::copyGeneric( 'CRM_Member_DAO_MembershipBlock', 
                                                           array( 'entity_id'    => $id,
                                                                  'entity_table' => 'civicrm_contribution_page'),
                                                           array( 'entity_id'    => $copy->id ) );
        
        $copyUFJoin =& CRM_Core_DAO::copyGeneric( 'CRM_Core_DAO_UFJoin', 
                                                  array( 'entity_id'    => $id,
                                                         'entity_table' => 'civicrm_contribution_page'),
                                                  array( 'entity_id'    => $copy->id ) );

        $copyWidget =& CRM_Core_DAO::copyGeneric( 'CRM_Contribute_DAO_Widget', 
                                                  array( 'contribution_page_id' => $id ),
                                                  array( 'contribution_page_id' => $copy->id ) );
        
        
        $copyOptionGroup =& CRM_Core_DAO::copyGeneric( 'CRM_Core_DAO_OptionGroup', 
                                                       array( 'name' => 'civicrm_contribution_page.amount.' .$id ),
                                                       array( 'name' => 'civicrm_contribution_page.amount.' .$copy->id ) );
        $optionGroupId =CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionGroup', 
                                                     'civicrm_contribution_page.amount.' .$id, 
                                                     'id', 
                                                     'name' );
        $copyOptionValue =& CRM_Core_DAO::copyGeneric( 'CRM_Core_DAO_OptionValue', 
                                                       array( 'option_group_id' => $optionGroupId ),
                                                       array( 'option_group_id' => $copyOptionGroup->id ) );
        
        $copyTellFriend =& CRM_Core_DAO::copyGeneric( 'CRM_Friend_DAO_Friend', 
                                                      array( 'entity_id'    => $id,
                                                             'entity_table' => 'civicrm_contribution_page'),
                                                      array( 'entity_id'    => $copy->id ) );
        
        $copyPremium =& CRM_Core_DAO::copyGeneric( 'CRM_Contribute_DAO_Premium', 
                                                   array( 'entity_id'    => $id,
                                                          'entity_table' => 'civicrm_contribution_page'), 
                                                   array( 'entity_id'    => $copy->id ) );
        $premiumQuery = "        
SELECT id
FROM civicrm_premiums
WHERE entity_table = 'civicrm_contribution_page'
      AND entity_id ={$id}";
        
        $premiumDao = CRM_Core_DAO::executeQuery( $premiumQuery, CRM_Core_DAO::$_nullArray );
        while ( $premiumDao->fetch( ) ) {
            if ( $premiumDao->id ) {
                $copyPremiumProduct =& CRM_Core_DAO::copyGeneric( 'CRM_Contribute_DAO_PremiumsProduct', 
                                                                  array( 'premiums_id' => $premiumDao->id ), 
                                                                  array( 'premiums_id' => $copyPremium->id ) );
            }
        }
        
        $query = "
SELECT second.id default_amount_id 
FROM civicrm_option_value first, civicrm_option_value second
WHERE second.option_group_id =%1
AND first.option_group_id =%2
AND first.weight = second.weight
AND first.id =%3
";
        
        $params = array( 
                        1 => array( $copyOptionGroup->id, 'Int' ), 
                        2 => array( $optionGroupId, 'Int' ), 
                        3 => array( CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_ContributionPage', 
                                                                 $id, 'default_amount_id' ), 'Int' ) );
        $dao = CRM_Core_DAO::executeQuery( $query, $params );
        
        while ( $dao->fetch( ) ) {
            $copy->default_amount_id = $dao->default_amount_id;
        }
        $copy->save( );
        
        return $copy;
    }

    /**
     * This function is to make a shallow copy of an object
     * and all the fields in the object
     * @param $daoName     DAO name in which to copy
     * @param $oldId       id on the basis we need to copy     
     * @param $newId       id in which to copy  
     * @param $tableField  table field to be matched before copying  
     *
     * @return $ids        array of ids copied from and copied to the particular table 
     * @access public
     */
    static function &copyObjects( $daoName, $oldId, $newId, $tableField ) 
    {
        require_once(str_replace('_', DIRECTORY_SEPARATOR, $daoName) . ".php");
        eval( '$object   =& new ' . $daoName . '( );' );
        $object->$tableField =  $oldId;
        if ( $tableField == 'entity_id' ) {
            $object->entity_table = 'civicrm_contribution_page';
        }
        
        $object->find( );
        
        $ids = array( );
        while( $object->fetch( ) ) {
            $ids[] = $object->id;
            $object->$tableField  = $newId;
            $object->id           = null;
            $object->save( );
        }
        $ids[] = $object->id;
        return $ids;
    }

    /**
     * Function to check if contribution page contains payment
     * processor that supports recurring payment
     *
     * @param int $contributionPageId Contribution Page Id
     * 
     * @return boolean true if payment processor supports recurring
     *                 else false
     *
     * @access public
     * @static
     */
    static function checkRecurPaymentProcessor( $contributionPageId ) 
    {
        $sql = "
  SELECT pp.is_recur
  FROM   civicrm_contribution_page  cp,
         civicrm_payment_processor  pp
  WHERE  cp.payment_processor_id = pp.id
    AND  cp.id = {$contributionPageId}
";
        
        if ( $recurring =& CRM_Core_DAO::singleValueQuery( $sql, CRM_Core_DAO::$_nullArray ) ) {
            return true;
        }
        return false;
    }
}
?>
