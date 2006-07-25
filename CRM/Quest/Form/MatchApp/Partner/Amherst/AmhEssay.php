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
 * Amherst Essay
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

       require_once 'CRM/Quest/DAO/Partner/Amherst.php';
        $dao =& new CRM_Quest_DAO_Partner_Amherst( );
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

        $options[1] = '"The women\'s movement or movements can no longer think about strategies for empowerment without appreciating the importance of the religious right, and the extend to which the agendas of the religuous right intersect with thouse of feminists in peculiar and often unpredictable ways." Amrita Basu, Dominic J. Paino 1995 Professor of Political Science and Women\'s and Gender Studies at Amherst College';
        $options[2] = '"I like science -- but only a little. What I love with all my heart is the universe. The world as revealed by science is far more beautiful, and far more interesting, than we had any right to expect. Science is valuable because of the view of the uinverse that it gives." Goerge Greenstein, Sidney Dillon Professor of Astronomy, Amherst College';

        $options[3] = '"When we fear pluralism as the enemy, succumb ot the allure of a seeming emotional unity or seek to enforce a meretricios purity of culure over rationalism and diversity, we turn cruel, whether or not we are conscious of it." From an Opening Convocation Address, September 6, 2004 by Anthony W. Marx, President, Amherst College';
        
        $options[5] = '"I\'m not a machine. I feel and believe, I have options, Some of them are interesting. I could, if you\'d let me, talk and talk ..." From Infinite Jest by David Foster Wallace, Amherst Class of 1985, Roy Edward Disney Professor in Creative Writing, Pomona College';

        $options[6] = '"Young as she is, the stuff Of her life is a great cargo, and some of it heavy: I wish her a lucky passage." From The Writer by Richard Wilbur, Amherst Class of 1942, 1987 Poet Laureate of the United States';

        $this->addRadio( 'amherst_essay', null, $options, null, '<br/>' );

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

        require_once 'CRM/Quest/DAO/Partner/Amherst.php';
        $dao =& new CRM_Quest_DAO_Partner_Amherst( );
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