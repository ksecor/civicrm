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
 * Amherst Appliction
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Quest/Form/App.php';
require_once 'CRM/Core/OptionGroup.php';

/**
 * This class generates form components for the amherst application
 * 
 */
class CRM_Quest_Form_MatchApp_Partner_Princeton_PrEssay extends CRM_Quest_Form_App
{
  
    protected $_essays;

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {       
        parent::preProcess();
        require_once 'CRM/Quest/BAO/Essay.php';
        $this->_essays = CRM_Quest_BAO_Essay::getFields( 'cm_partner_princeton_essay', $this->_contactID, $this->_contactID );
    }
    
    
    function setDefaultValues( ) 
    {
        $defaults = array( );
        
        require_once 'CRM/Quest/Partner/DAO/Princeton.php';
        $dao =& new CRM_Quest_Partner_DAO_Princeton( );
        $dao->contact_id = $this->_contactID;
        if( $dao->find( true ) ) {
            CRM_Core_DAO::storeValues( $dao ,$defaults );
        }
        
        
        $defaults['essay'] = array( );
        
        CRM_Quest_BAO_Essay::setDefaults( $this->_essays, $defaults['essay'] );
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
        
      
        $options[1]='Tell us about a person who has influenced you in a significant way.';
        $options[2] = '"Princeton in the Nation\'s Service" was the title of a speech given by Woodrow Wilson on the 150th anniversary of the University. It became the unofficial Princeton motto and was expanded for the University\'s 250th anniversary to "Princeton in the nation\'s service and in the service of all nations". Woodrow Wilson, Princeton Class of 1879, served on the faculty and was Princeton\'s president from 1902-1910.';
        $options[3] = '"The important thing is not to stop questioning. Curiosity has its own reason for existing. One cannot help but be in awe when one contemplates the mysteries of eternity, of life, of the marvelous structure of reality. It is enough if one tries to comprehend only a little of this mystery every day." Albert Einstein, Princeton resident 1933 - 1955';
        $options[4] = '"Some questions cannot be answered,/They become familiar weights in the hand,/ Round stones pulled from the pocket, unyielding and cool." Jane Hirshfield, poet, Princeton Class of 1973"';
        
       
        
        $this->addRadio( 'essay_theme', null, $options, null, '<br/><br />' );

        require_once 'CRM/Quest/BAO/Essay.php';
        $this->_essays = CRM_Quest_BAO_Essay::getFields( 'cm_partner_princeton_essay', $this->_contactID, $this->_contactID );
        CRM_Quest_BAO_Essay::buildForm( $this, $this->_essays );
        parent::buildQuickForm();
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


    public function postProcess() {
        if ( $this->_action &  CRM_Core_Action::VIEW ) {
            return;
        }
        $params = $this->controller->exportValues( $this->_name );
        require_once 'CRM/Quest/Partner/DAO/Princeton.php';
        $dao =& new CRM_Quest_Partner_DAO_Princeton( );
        $dao->contact_id = $this->_contactID;
        $dao->find( true );
        $dao->copyValues($params);
        $dao->save( );
       
        CRM_Quest_BAO_Essay::create( $this->_essays, $params['essay'],
                                     $this->_contactID, $this->_contactID ); 
        
        parent::postProcess( );
    } 
    

}
?>
