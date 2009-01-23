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
require_once 'CRM/Core/Form.php';
require_once 'CRM/Contribute/BAO/PCP.php';
/**
 * This class generates form components for processing a ontribution 
 * 
 */

class CRM_Contribute_Form_PCP_Campaign extends CRM_Core_Form
{
 
    public function preProcess()  
    {
        // we do not want to display recently viewed items, so turn off
        $this->assign('displayRecent' , false );

        $this->_pageId = CRM_Utils_Request::retrieve( 'id', 'Positive', $this, false );        
        $title = ts('Setup a Personal Campaign Page - Step 2');
        
        if( $this->_pageId ) {
            $title = ts('Edit Your Personal Campaign Page');
        }

        CRM_Utils_System::setTitle( $title );
        parent::preProcess( );
    }

    function setDefaultValues( ) 
    {
        require_once 'CRM/Contribute/DAO/PCP.php';
        $dafaults = array( );
        $dao =& new CRM_Contribute_DAO_PCP( );
        
        if( $this->_pageId ) {
            $dao->id = $this->_pageId;
            if ( $dao->find(true) ) {
                CRM_Core_DAO::storeValues( $dao, $defaults );
            }
        }
        
        if ( $this->get('action') & CRM_Core_Action::ADD ) {
            $defaults['is_active'] = 1;
        }
     
        $this->_contactID    = CRM_Utils_Array::value( 'contact_id', $defaults );
        $this->_contriPageId = CRM_Utils_Array::value( 'contribution_page_id', $defaults );
        return $defaults;
    }
    
    
    /** 
     * Function to build the form 
     * 
     * @return None 
     * @access public 
     */ 
    public function buildQuickForm( )  
    {
        $this->add('text', 'title', ts('Title'), null, true );
        $this->add('textarea', 'intro_text', ts('Welcome'), null, true );
        $this->add('text', 'goal_amount', ts('Your Goal'), null, true );
        $attributes = array( );
        if ( $this->get('action') & CRM_Core_Action::ADD ) {
            $attributes = array('value' => ts('Donate Now'), 'onClick' => 'select();');
        }

        $this->add('text', 'donate_link_text', ts('Donation Button'), $attributes); 
        $attrib = Array ('rows' => 8, 'cols' => 60 );
//        $this->addWysiwyg( 'page_text', ts('Your Message'), $attrib ); 
        $this->add('textarea', 'page_text', ts('Your Message'), null, false );
        
        $maxAttachments = 1; 
        require_once 'CRM/Core/BAO/File.php';
        CRM_Core_BAO_File::buildAttachment( $this, 'civicrm_pcp', $this->_pageId, $maxAttachments );
        
        $this->addElement( 'checkbox', 'is_thermometer', ts('Progress Bar') );
        $this->addElement( 'checkbox', 'is_honor_roll', ts('Honor Roll'), null);
        $this->addElement( 'checkbox', 'is_active', ts('Active') );

        $this->addButtons( array(
                                 array ( 'type'      => 'upload',
                                         'name'      => ts('Save'),
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );
        $this->addFormRule( array( 'CRM_Contribute_Form_PCP_Campaign', 'formRule' ), $this );
    }
    
    /**  
     * global form rule  
     *  
     * @param array $fields  the input form values  
     * @param array $files   the uploaded files if any  
     * @param array $options additional user data  
     *  
     * @return true if no errors, else array of errors  
     * @access public  
     * @static  
     */  
    static function formRule( &$fields, &$files, $self ) 
    {
        $errors = array();
        if ( $fields['goal_amount'] <= 0 || ! is_numeric($fields['goal_amount']) ) {
            $errors['goal_amount'] = ts('Goal Amount should be a numeric value greater than zero.');
        }
        if ( strlen($fields['donate_link_text']) >= 64 ){
            $errors['donate_link_text'] = ts('Button Text must be less than 64 characters.');
        }
        if ( isset($files['attachFile_1']) ) {
            list( $width, $height ) = getimagesize( $files['attachFile_1']['tmp_name'] );
            if ( $width > 360 || $height > 360 ) {
                $errors['attachFile_1'] = "Your picture or image file can not be larger than 360 x 360 pixels in size." . " The dimensions of the image you've selected is ". $width." x ". $height . ". Please shrink or crop the file or find another smaller image and try again.";
            }
        }       
        return $errors;
    }
    
    /** 
     * Function to process the form 
     * 
     * @access public 
     * @return None 
     */ 
    public function postProcess( )  
    {
        $params  = $this->controller->exportValues( );
        $checkBoxes = array( 'is_thermometer', 'is_honor_roll', 'is_active' );
        
        foreach( $checkBoxes as $key ) {
            if ( ! isset( $params[$key] ) ) {
                $params[$key] = 0;
            }
        }
        $session =& CRM_Core_Session::singleton( );
        $contactID = isset( $this->_contactID ) ? $this->_contactID : $session->get('userID');
        if( ! $contactID ) {
            $contactID = $this->get('contactID');
        }
        $params['contact_id']           = $contactID;
        $params['contribution_page_id'] = $this->get('contribution_page_id') ? $this->get('contribution_page_id') : $this->_contriPageId;
        
        $approval_needed = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_PCPBlock', 
                                                        $params['contribution_page_id'], 'is_approval_needed', 'entity_id' );
        $approvalMessage = null;
        if ( $this->get('action') & CRM_Core_Action::ADD ) {
            $params['status_id'] = $approval_needed ? 1 : 2;
            $approvalMessage     = $approval_needed ? ts('but requires administrator review before you can begin your fundraising efforts. You will receive an email confirmation shortly which includes a link to return to this page.') : ts('and is ready to use. Click the Tell Friends link below to being promoting your fundraising campaign.');
        }
        
        $params['id'] = $this->_pageId;
        
        require_once 'CRM/Contribute/BAO/PCP.php';
        $pcp = CRM_Contribute_BAO_PCP::add( $params );

        // add attachments as needed
        CRM_Core_BAO_File::formatAttachment( $params,
                                             $params,
                                             'civicrm_pcp',
                                             $pcp->id );

        $pageStatus = isset( $this->_pageId ) ? ts('updated') : ts('created');
        $statusId = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_PCP', $pcp->id, 'status_id' );
     
        //send notification of PCP create/update.
        $pcpParams   = array( 'entity_table' => 'civicrm_contribution_page', 'entity_id' => $pcp->contribution_page_id );
        $notifyParams = array( );
        $notifyStatus = "";
        CRM_Core_DAO::commonRetrieve('CRM_Contribute_DAO_PCPBlock', $pcpParams, $notifyParams, array('notify_email'));

        if ( $emails = CRM_Utils_Array::value('notify_email', $notifyParams) ) {
            $this->assign( 'pcpTitle', $pcp->title );
            
            if( $this->_pageId ) {
                $this->assign ( 'mode', 'Update');
            } else {
                $this->assign ( 'mode', 'Add');
            }
            require_once 'CRM/Core/OptionGroup.php';
            $pcpStatus = CRM_Core_OptionGroup::getLabel( 'pcp_status', $statusId );
            $this->assign( 'pcpStatus', $pcpStatus );
            
            $this->assign( 'pcpId', $pcp->id );
            
            $supporterUrl = CRM_Utils_System::url( "civicrm/contact/view",
                                                   "reset=1&cid={$pcp->contact_id}",
                                                   true, null, true,
                                                   true );
            $this->assign( 'supporterUrl', $supporterUrl );
            $supporterName = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', $pcp->contact_id, 'display_name' );
            $this->assign( 'supporterName', $supporterName );
           
            $contribPageUrl   = CRM_Utils_System::url( "civicrm/contribute/transact",
                                                       "reset=1&id={$pcp->contribution_page_id}",
                                                       true, null, true,
                                                       true );
            $this->assign( 'contribPageUrl', $contribPageUrl );
            $contribPageTitle = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_ContributionPage', $pcp->contribution_page_id, 'title' );
            $this->assign( 'contribPageTitle', $contribPageTitle );
            
            $managePCPUrl =  CRM_Utils_System::url( "civicrm/admin/pcp",
                                                    "reset=1",
                                                    true, null, true,
                                                    true );
            $this->assign( 'managePCPUrl', $managePCPUrl );
            
            $subject = ts('Personal Campaign Page Notification');
            
            $template =& CRM_Core_Smarty::singleton( );
            $message  = $template->fetch( 'CRM/Contribute/Form/PCPNotify.tpl' );
          
            //get the default domain email address.
            require_once 'CRM/Core/BAO/Domain.php';
            list( $domainEmailName, $domainEmailAddress ) = CRM_Core_BAO_Domain::getNameAndEmail( );
            
            if ( !$domainEmailAddress || $domainEmailAddress == 'info@FIXME.ORG') {
                CRM_Core_Error::fatal( ts( 'The site administrator needs to enter a valid \'FROM Email Address\' in Administer CiviCRM &raquo; Configure &raquo; Domain Information. The email address used may need to be a valid mail account with your email service provider.' ) );
            }
            
            $emailFrom = '"' . $domainEmailName . '" <' . $domainEmailAddress . '>';
            //if more than one email present for PCP notification ,
            //first email take it as To and other as CC and First email
            //address should be sent in users email receipt for
            //support purpose.
            $emailArray = explode(',' ,$emails );
            $to = $emailArray[0];
            unset( $emailArray[0] );
            $cc = implode(',', $emailArray );
            
            require_once 'Mail/mime.php';
            require_once 'CRM/Utils/Mail.php';
            if ( CRM_Utils_Mail::send( $emailFrom,
                                       "",
                                       $to,
                                       $subject,
                                       $message,
                                       $cc ) ) {
                $notifyStatus = ts(' A notification email has been sent to the site administrator.'); 
            }
        }
        
        CRM_Core_BAO_File::processAttachment( $params, 'civicrm_pcp', $pcp->id );

        // send email notification to supporter, if initial setup / add mode.
        if ( ! $this->_pageId ) {
            CRM_Contribute_BAO_PCP::sendStatusUpdate( $pcp->id, $statusId, true );
            if ( $approvalMessage && CRM_Utils_Array::value( 'status_id', $params ) == 1 ) {
                $notifyStatus .= ' You will receive a second email as soon as the review process is complete.';
            }
        }

        CRM_Core_Session::setStatus( ts( "Your Personal Campaign Page has been %1 %2 %3", 
                                         array(1 => $pageStatus, 2 => $approvalMessage, 3 => $notifyStatus)) );
        if ( ! $this->_pageId ) {
            $session->pushUserContext( CRM_Utils_System::url( 'civicrm/contribute/pcp/info', 'reset=1&id='.$pcp->id ) );
        } 
    }
}

?>
