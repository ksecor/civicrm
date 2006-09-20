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
 * Amherst Essay
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
 * This class generates form components for the amherst essay
 * 
 */
class CRM_Quest_Form_MatchApp_Partner_Amherst_AmhEssay extends CRM_Quest_Form_App
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
        $this->_essays = CRM_Quest_BAO_Essay::getFields( 'cm_partner_amherst_essay', $this->_contactID, $this->_contactID );
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
        $defaults = array( );

       require_once 'CRM/Quest/Partner/DAO/Amherst.php';
        $dao =& new CRM_Quest_Partner_DAO_Amherst( );
        $dao->contact_id = $this->_contactID;
        if ( $dao->find( true ) ) {
            $defaults['amherst_essay_id'] = $dao->amherst_essay_id;
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
        
        $options = CRM_Core_OptionGroup::values( 'amherst_essay' );

        $options[1] = '"It seems to me incumbent upon this and other schools\’ graduates to recognize their responsibility to the public interest…unless the graduates of this college…are willing to put back into our society those talents, the broad sympathy, the understanding, the compassion… then obviously the presuppositions upon which our democracy are based are bound to be fallible." John F. Kennedy, at the ground breaking for the Amherst College Frost Library, October 26, 1963'; 

        $options[2] = '"The world as revealed by science is far more beautiful, and far more interesting, than we had any right to expect. Science is valuable because of the view of the universe that it gives." George Greenstein, Sidney Dillon Professor of Astronomy, Amherst College';

        $options[3] = '"Stereotyped beliefs have the power to become self-fulfilling prophesies for behavior." From Men and Women in Interaction, Reconsidering the Differences by Elizabeth Aries, Professor of Psychology, Amherst College';
        
        $options[4] = '"Justice seems to require us to take the perspective of an impartial observer…Often this perspective seems to clash with the perspective that most of us take in our daily lives…where ties of love, commitment, friendship and professional responsibilities seem not only to permit, but to demand, that we treat people unequally." From Amherst, Summer 2001, Jyl Gentzler, Professor of Philosophy, Amherst College';

        $options[5] = '"Only in the mystery novel are we delivered final and unquestionable solutions. The joke to me is that fiction gives you a truth that reality can’t deliver." Scott Turow, lawyer, author, Amherst College Trustee, Amherst Class of 1970';

        $options[6] = '"Young as she is, the stuff<br />Of her life is a great cargo, and some of it heavy:<br />I wish her a lucky passage."<br />From The Writer by Richard Wilbur, Amherst Class of 1942, 1987 Poet Laureate of the United State';

        $this->addRadio( 'amherst_essay_id', ts('Please select an Essay Quotation.'), $options, null, '<br/><br />', true );

        CRM_Quest_BAO_Essay::buildForm( $this, $this->_essays );

        parent::buildQuickForm( );
                
    }//end of function

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
        return ts( 'Amherst College' );
    }

    /** 
     * process the form after the input has been submitted and validated 
     * 
     * @access public 
     * @return void 
     */ 
    public function postProcess() {
        if ( $this->_action &  CRM_Core_Action::VIEW ) {
            return;
        }

        $params = $this->controller->exportValues( $this->_name );

        require_once 'CRM/Quest/Partner/DAO/Amherst.php';
        $dao =& new CRM_Quest_Partner_DAO_Amherst( );
        $dao->contact_id = $this->_contactID;
        $dao->find( true );
        $dao->amherst_essay_id = $params['amherst_essay_id'];
        $dao->save( );

        CRM_Quest_BAO_Essay::create( $this->_essays, $params['essay'],
                                     $this->_contactID, $this->_contactID ); 

        parent::postProcess( );
    } 
   
}

?>