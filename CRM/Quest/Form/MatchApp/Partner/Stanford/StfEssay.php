<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
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
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                      |
 +--------------------------------------------------------------------+
*/


/**
 * Personal Information Form Page
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Quest/Form/MatchApp/Essay.php';

/**
 * This class generates form components for relationship
 * 
 */
class CRM_Quest_Form_MatchApp_Partner_Stanford_StfEssay extends CRM_Quest_Form_MatchApp_Essay
{
     static $dontCare;
    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        $this->_grouping = 'cm_partner_stanford_essay';
        parent::preProcess();

        self::$dontCare = false;
        $attachments =& crm_get_files_by_entity( $this->_contactID);
        $attach = array();
        if ( ! is_a( $attachments, CRM_Core_Error ) ) {
            foreach($attachments as $key=>$value ) {
                if ($value['file_type_id'] == 6 ) {
                   self::$dontCare = true;
                }
            }
        }
    }

    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_DAO_Student' );
        $radioAttributeArray = array('onclick' => "return showHideByValue('personalStat_quests','1','id_upload_photo','block','radio',false);");
 
        $personalStatQuests = array( 'Choose a photograph of something important to you and explain its significance. (You must upload a photograph if you answer this prompt.)',
                                'As you reflect on your life thus far, what has someone said, written or expressed in some fashion that is especially meaningful to you? Why? '
                                );
        $this->addRadio( 'personalStat_quests', null, $personalStatQuests , $radioAttributeArray, '<br/>' );

        //file upload
        $this->addElement('file', 'uploadFile', ts( 'Upload photograph:' ), null );
        
        $this->addFormRule(array('CRM_Quest_Form_MatchApp_Partner_Stanford_StfEssay', 'formRule'));

        parent::buildQuickForm();
        
    }//end of function
    
    /**
     * Function for validation
     *
     * @param array $params (ref.) an assoc array of name/value pairs
     *
     * @return mixed true or array of errors
     * @access public
     */
    public function formRule(&$params, &$files ) {
        $errors = array( );

        if ( $params['personalStat_quests'] == 0 && !self::$dontCare) {
            // ensure that there is a file upload
            if ( empty( $files['uploadFile']['tmp_name'] ) )  {
                $errors['uploadFile'] = ts( 'Please upload a photo' );
            }
        }
        return empty($errors) ? true : $errors;
    }

    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle()
    {
        return ts('Essay');
    }

    public function getRootTitle( ) {
        return ts( 'Stanford University' );
    }

}

?>
