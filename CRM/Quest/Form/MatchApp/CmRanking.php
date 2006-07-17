<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
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
 * College Match Ranking Information Form Page
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Quest/Form/App.php';
require_once 'CRM/Quest/BAO/Student.php'; 
require_once 'CRM/Core/OptionGroup.php';

/**
 * This class generates form components for relationship
 * 
 */
class CRM_Quest_Form_MatchApp_CmRanking extends CRM_Quest_Form_App
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
      
        $defaults = array();
        require_once 'CRM/Quest/DAO/PartnerRanking.php';
        $dao = & new CRM_Quest_DAO_PartnerRanking();
        $dao->s_forward = '0';
        $dao->contact_id = $this->contact_id;
        $dao->find();
        while( $dao->fetch() ){
            $defaults['college_ranking_'.$dao->partner_id] = $dao->ranking_id ;
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
        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_DAO_Student');
       
        require_once "CRM/Quest/BAO/Partner.php";
        $partners = CRM_Quest_BAO_Partner::getPartners();
       
        foreach ( $partners as $k => $v) {
            $this->addElement('select','college_ranking_'.$k, ts( 'Ranking' ),array("" => "")+CRM_Core_OptionGroup::values('college_ranking'));
        }
        $this->assign( 'collegeType', $partners);
        parent::buildQuickForm( );
    }

 /**
      * process the form after the input has been submitted and validated
      *
      * @access public
      * @return void
      */
    public function postProcess() 
    {
        self::preProcess();
        if ( ! ( $this->_action &  CRM_Core_Action::VIEW ) ) {
            $params = $this->controller->exportValues( $this->_name );

            //delete all renking before Inserting new one 
            require_once 'CRM/Quest/DAO/PartnerRanking.php';
            $dao = & new CRM_Quest_DAO_PartnerRanking();
            $dao->is_forward = '0';
            $dao->contact_id = $this->contact_id;
            $dao->delete();
            
            foreach ( $params as $key=>$value ) {
                if ( $value ) {
                    $ranking = array();
                    $ranking['contact_id'] = $this->_contactID;
                    $temp = explode('_', $key);
                    $ranking['partner_id'] = $temp[2];
                    $ranking['ranking_id'] = $value;
                    $dao = & new CRM_Quest_DAO_PartnerRanking();
                    $dao->copyValues( $ranking );
                    $dao->save();
                }
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
        return ts('College Match Ranking');
    }

}

?>
