<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Contribute/DAO/ContributionPage.php';

class CRM_Contribute_BAO_ContributionPage extends CRM_Contribute_DAO_ContributionPage {

    /**
     * takes an associative array and creates a contribution page object
     *
     * @param array $params (reference ) an assoc array of name/value pairs
     *
     * @return object CRM_Contribute_DAO_ContributionPage object 
     * @access public
     * @static
     */
    public static function &create(&$params) {
        $dao =& new CRM_Contribute_DAO_ContributionPage( );
        $dao->copyValues( $params );
        $dao->save( );
        return $dao;
    }

    static function setValues( $id, &$values ) {
        $params = array('id' => $id);

        CRM_Core_DAO::commonRetrieve( 'CRM_Contribute_DAO_ContributionPage', $params, $values );

        // get the amounts and the label
        require_once 'CRM/Core/BAO/CustomOption.php';  
        CRM_Core_BAO_CustomOption::getAssoc( 'civicrm_contribution_page', $id, $values );

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
     * Process that send e-mails
     *
     * @return void
     * @access public
     */
    static function sendMail( $contactID, &$values ) {
        if ( $values['is_email_receipt'] ) {
            $template =& CRM_Core_Smarty::singleton( );

            require_once 'CRM/Contact/BAO/Contact.php';
            list( $displayName, $email ) = CRM_Contact_BAO_Contact::getEmailDetails( $contactID );
            self::buildCustomDisplay( $values['custom_pre_id'] , 'customPre' , $contactID, $template );
            self::buildCustomDisplay( $values['custom_post_id'], 'customPost', $contactID, $template );

            $subject = trim( $template->fetch( 'CRM/Contribute/Form/Contribution/ReceiptSubject.tpl' ) );
            $message = $template->fetch( 'CRM/Contribute/Form/Contribution/ReceiptMessage.tpl' );
           
            $receiptFrom = '"' . $values['receipt_from_name'] . '" <' . $values['receipt_from_email'] . '>';
            require_once 'CRM/Utils/Mail.php';
            CRM_Utils_Mail::send( $receiptFrom,
                                   $displayName,
                                   $email,
                                   $subject,
                                   $message,
                                   $values['cc_receipt'],
                                   $values['bcc_receipt']
                                   );
            
        }
    }
    
    /**  
     * Function to add the custom fields
     *  
     * @return None  
     * @access public  
     */ 
    function buildCustomDisplay( $gid, $name, $cid, &$template ) {
        if ( $gid ) {
           $values = array( );
           $groupTitle = null;
           $fields = CRM_Core_BAO_UFGroup::getFields( $gid, false, CRM_Core_Action::VIEW );
           CRM_Core_BAO_UFGroup::getValues( $cid, $fields, $values , false );
           foreach( $fields as $v  ) {
               $groupTitle = $v["groupTitle"];
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

?>