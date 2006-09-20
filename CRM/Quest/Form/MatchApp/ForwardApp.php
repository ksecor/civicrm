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

require_once 'CRM/Quest/Form/App.php';
require_once 'CRM/Core/OptionGroup.php';

/**
 * This class generates form components for relationship
 * 
 */
class CRM_Quest_Form_MatchApp_ForwardApp extends CRM_Quest_Form_App
{
    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        parent::preProcess();
    }
    
    /**
     * This function sets the default values for the form. Relationship that in edit/view action
     * the default values are retrieved from the database
     * 
     * @access public
     * @return void
     */
    function setDefaultValues( ) 
    {
        $partner_s=CRM_Quest_BAO_Partner::getPartners('Scholarship');
        $defaults = array();
        require_once 'CRM/Quest/DAO/PartnerRanking.php';
        $dao = & new CRM_Quest_DAO_PartnerRanking();
        $dao->is_forward = '1';
        $dao->contact_id = $this->_contactID;
        $dao->find();
        while( $dao->fetch() ){
            if (array_key_exists($dao->partner_id ,$partner_s )) {
                $defaults['scholarship_addmission_'.$dao->partner_id] = 1 ;
            } else {
                $defaults['regular_addmission_'.$dao->partner_id] = 1 ;
                
            }
        }
        return $defaults;
    }
  
    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm( ) 
    {
        require_once "CRM/Quest/DAO/Partner.php";
        require_once 'CRM/Quest/BAO/Partner.php';

        $partner=CRM_Quest_BAO_Partner::getPartners();
        foreach ( $partner as $key => $v ) {
            $this->addElement( 'checkbox','regular_addmission_'.$key,$v, null);
            $collegeLink =& new CRM_Quest_DAO_Partner();
            $collegeLink->name = $v;
            $collegeLink->find(true);
            $link[$key] = $collegeLink->url_learn;
            unset($collegeLink);
        }
        $this->assign('partner' , $partner);
        $partner_s=CRM_Quest_BAO_Partner::getPartners('Scholarship');

     
        foreach ( $partner_s as $key => $v ) {
            $this->addElement( 'checkbox','scholarship_addmission_'.$key,$v, null);
        
            $collegeLink =& new CRM_Quest_DAO_Partner();
            $collegeLink->name = $v;
            $collegeLink->find(true);
            $scholarshipLink[$key] = $collegeLink->url_learn;
            unset($collegeLink);
        }
       
        $this->assign( 'url_link', $link);
        $this->assign( 'scholarshipUrl_link', $scholarshipLink);
        $this->assign('partner_s' , $partner_s);

        parent::buildQuickForm( );

    }//end of function
    /**
      * process the form after the input has been submitted and validated
      *
      * @access public
      * @return void
      */
    public function postProcess() 
    {

        //delete all renking before Inserting new one 
        if ( ! ( $this->_action &  CRM_Core_Action::VIEW ) ) {
            $params = $this->controller->exportValues( $this->_name );
            
            //Regular Admissions
            $partners = CRM_Quest_BAO_Partner::getPartners();
            foreach ( $partners as $key=>$value ) {
                $dao = & new CRM_Quest_DAO_PartnerRanking();
                $ranking = array();
                $ranking['contact_id'] = $this->_contactID;
                $ranking['partner_id'] = $key;
                $ranking['is_forward'] = $params['regular_addmission_'.$key];
                $dao->partner_id = $key;
                $dao->contact_id = $this->_contactID;
                $dao->find(true);
                $dao->copyValues( $ranking );
                $dao->save();
            }

            //Forward Application for Scholarships
            $partners = CRM_Quest_BAO_Partner::getPartners('Scholarship');
            foreach ( $partners as $key=>$value ) {
                $dao = & new CRM_Quest_DAO_PartnerRanking();
                $ranking = array();
                $ranking['contact_id'] = $this->_contactID;
                $ranking['partner_id'] = $key;
                $ranking['is_forward'] = $params['scholarship_addmission_'.$key];
                $dao->partner_id = $key;
                $dao->contact_id = $this->_contactID;
                $dao->find(true);
                $dao->copyValues( $ranking );
                $dao->save();
            }


        }
        parent::postProcess( );
    }
    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle()
    {
        return ts('Application Forwarding ');
    }

}

?>
