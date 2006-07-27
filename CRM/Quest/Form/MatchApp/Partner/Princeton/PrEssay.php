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
  
   
     /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $opt=array(1=>ts('Tell us about a person who has influenced you in a significant way.'));
        $options = CRM_Core_OptionGroup::values( 'princeton_essay' );
        
        $options[1] = '"Princeton in the Nations Service" was the title of a speech given by Woodrow Wilson on the 150th anniversary of the University. It became the unofficial Princeton motto and was expanded for the Universitys 250th anniversary to "Princeton in the nations service and in the service of all nations". Woodrow Wilson, Princeton Class of 1879, served on the faculty and was Princetons president from 1902-1910.';
        $options[2] = '"The important thing is not to stop questioning. Curiosity has its own reason for existing. One cannot help but be in awe when one contemplates the mysteries of eternity, of life, of the marvelous structure of reality. It is enough if one tries to comprehend only a little of this mystery every day." Albert Einstein, Princeton resident 1933 - 1955';
        $options[3] = '"Some questions cannot be answered,/They become familiar weights in the hand,/ Round stones pulled from the pocket, unyielding and cool." Jane Hirshfield, poet, Princeton Class of 1973"';
        
        $this->addRadio( 'person_name',null, $opt, null, '<br /><br />' );
        
        $this->addRadio( 'princeton_essay', null, $options, null, '<br/><br />' );

        require_once 'CRM/Quest/BAO/Essay.php';
        $this->_essays = CRM_Quest_BAO_Essay::getFields( 'cm_partner_princeton_essay', $this->_contactID, $this->_contactID );
        CRM_Quest_BAO_Essay::buildForm( $this, $this->_essays );
    }
  /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle()
    {
         return ts(' Essay');
    }

}
?>