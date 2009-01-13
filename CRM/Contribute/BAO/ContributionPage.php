<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 * @copyright CiviCRM LLC (c) 2004-2009
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
        $values['amount'] = array( );
        CRM_Core_OptionGroup::getAssoc( "civicrm_contribution_page.amount.{$id}", $values['amount'], true );

        // get the profile ids
        require_once 'CRM/Core/BAO/UFJoin.php'; 
        $ufJoinParams = array( 'entity_table' => 'civicrm_contribution_page',   
                               'entity_id'    => $id );   
        list( $values['custom_pre_id'],
              $values['custom_post_id'] ) = CRM_Core_BAO_UFJoin::getUFGroupIds( $ufJoinParams ); 

        // add an accounting code also
        if ( $values['contribution_type_id'] ) {
            $values['accountingCode'] = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_ContributionType',
                                                                     $values['contribution_type_id'],
                                                                     'accounting_code' );
        }
    }


    /**
     * Function to send the emails
     * 
     * @param int     $contactID         contact id 
     * @param array   $values            associated array of fields
     * @param boolean $isTest            if in test mode
     * @param boolean $returnMessageText return the message text instead of sending the mail
     *
     * @return void
     * @access public
     * @static
     */
    static function sendMail( $contactID, &$values, $isTest = false, $returnMessageText = false ) 
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
        
        if ( ! $returnMessageText ) {
            //send notification email if field values are set (CRM-1941)
            require_once 'CRM/Core/BAO/UFGroup.php';
            foreach ( $gIds as $key => $gId ) {
                $email = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_UFGroup', $gId, 'notify' );
                if ( $email ) {
                    $val = CRM_Core_BAO_UFGroup::checkFieldsEmptyValues( $gId, $contactID, $params[$key] );
                    CRM_Core_BAO_UFGroup::commonSendMail($contactID, $val); 
                }
            }
        }

        if ( $values['is_email_receipt'] || $values['onbehalf_dupe_alert'] ) {
            $template =& CRM_Core_Smarty::singleton( );

            // get the billing location type
            $locationTypes =& CRM_Core_PseudoConstant::locationType( );
            $billingLocationTypeId = array_search( 'Billing',  $locationTypes );

            require_once 'CRM/Contact/BAO/Contact/Location.php';
            list( $displayName, $email ) = CRM_Contact_BAO_Contact_Location::getEmailDetails( $contactID, false, $billingLocationTypeId );
            if ( $isTest &&
                 ! empty( $params['custom_pre_id'] ) ) {
                $params['custom_pre_id'][] = array( 'contribution_test', '=', 1, 0, 0 );
            }

            if ( $isTest &&
                 ! empty( $params['custom_post_id'] ) ) {
                $params['custom_post_id'][] = array( 'contribution_test', '=', 1, 0, 0 );
            }
            
            //for display profile need to get individual contact id,  
            //hence get it from related_contact if on behalf of org true CRM-3767.
            $cid = CRM_Utils_Array::value( 'related_contact', $values, $contactID );
            
            self::buildCustomDisplay( CRM_Utils_Array::value( 'custom_pre_id',
                                                              $values ),
                                      'customPre',
                                      $cid,
                                      $template  ,
                                      $params['custom_pre_id'] );
            self::buildCustomDisplay( CRM_Utils_Array::value( 'custom_post_id',
                                                              $values ),
                                      'customPost',
                                      $cid,
                                      $template   ,
                                      $params['custom_post_id'] );
            
            // set email in the template here
            $template->assign( 'email', $email );

            // cc to related contacts of contributor OR the one who
            // signs up. Is used for cases like - on behalf of
            // contribution / signup ..etc  
            if ( array_key_exists('related_contact', $values) ) {
                list( $ccDisplayName, $ccEmail ) = 
                    CRM_Contact_BAO_Contact_Location::getEmailDetails( $values['related_contact'] );
                $ccMailId = '"' . $ccDisplayName . '" <' . $ccEmail . '>';
                
                $values['cc_receipt'] = CRM_Utils_Array::value( 'cc_receipt' , $values ) ? 
                    ($values['cc_receipt'] . ',' . $ccMailId) : $ccMailId;
                
                // reset primary-email in the template
                $template->assign( 'email', $ccEmail );
                
                $template->assign('onBehalfName',    $displayName);
                $template->assign('onBehalfEmail',   $email);
            }
            
            $subject = trim( $template->fetch( 'CRM/Contribute/Form/Contribution/ReceiptSubject.tpl' ) );
            $message = $template->fetch( 'CRM/Contribute/Form/Contribution/ReceiptMessage.tpl' );
            if ( $returnMessageText ) {
                return array( 'subject' => $subject,
                              'body'    => $message,
                              'to'      => $displayName );
            }

            $receiptFrom = '"' . CRM_Utils_Array::value('receipt_from_name',$values) . '" <' . $values['receipt_from_email'] . '>';

            require_once 'CRM/Utils/Mail.php';

            if ( $values['is_email_receipt'] ) {
                CRM_Utils_Mail::send( $receiptFrom,
                                      $displayName,
                                      $email,
                                      $subject,
                                      $message,
                                      CRM_Utils_Array::value( 'cc_receipt' , $values ),
                                      CRM_Utils_Array::value( 'bcc_receipt', $values )
                                      );
            }

            // send duplicate alert, if dupe match found during on-behalf-of processing.
            if ( $values['onbehalf_dupe_alert'] ) {
                $systemFrom = '"Automatically Generated" <' . $values['receipt_from_email'] . '>';
                $template->assign('onBehalfID', $contactID);
                
                $emailTemplate  = 'CRM/Contribute/Form/Contribution/DuplicateAlertMessage.tpl';
                
                $template->assign( 'returnContent', 'subject' );
                $subject = $template->fetch( $emailTemplate );
                
                $template->assign( 'receiptMessage', $message );

                $template->assign( 'returnContent', 'textMessage' );
                $message = $template->fetch( $emailTemplate );
                
                CRM_Utils_Mail::send( $systemFrom,
                                      CRM_Utils_Array::value('receipt_from_name',$values),
                                      $values['receipt_from_email'],
                                      $subject,
                                      $message );
            }
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

                foreach ( $fields as $k => $v  ) {
                    if ( ! $groupTitle ) { 
                        $groupTitle = $v["groupTitle"];
                    }
                    // suppress all file fields from display
                    if ( CRM_Utils_Array::value( 'data_type', $v, '' ) == 'File' ) {
                        unset( $fields[$k] );
                    }
                }

                if ( $groupTitle ) {
                    $template->assign( $name."_grouptitle", $groupTitle );
                }

                CRM_Core_BAO_UFGroup::getValues( $cid, $fields, $values , false, $params );

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
        $copyPledgeBlock =& CRM_Core_DAO::copyGeneric( 'CRM_Pledge_DAO_PledgeBlock', 
                                                       array( 'entity_id'    => $id,
                                                              'entity_table' => 'civicrm_contribution_page'),
                                                       array( 'entity_id'    => $copy->id ) );
                
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
        
        
        //copy option group and values 
        require_once "CRM/Core/BAO/OptionGroup.php";
        $copy->default_amount_id = CRM_Core_BAO_OptionGroup::copyValue('contribution', 
                                                                       $id, 
                                                                       $copy->id, 
                                                                       CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_ContributionPage', 
                                                                                                    $id, 
                                                                                                    'default_amount_id' ) );
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
        
        $copy->save( );
        
        require_once 'CRM/Utils/Hook.php';
        CRM_Utils_Hook::copy( 'ContributionPage', $copy );

        return $copy;
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

