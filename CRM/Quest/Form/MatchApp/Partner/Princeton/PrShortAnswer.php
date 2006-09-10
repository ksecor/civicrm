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
 * Princeton ShortAnswer
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Quest/Form/App.php';
require_once 'CRM/Core/OptionGroup.php';

/**
 * This class generates form components for the Princeton application
 * 
 */
class CRM_Quest_Form_MatchApp_Partner_Princeton_PrShortAnswer extends CRM_Quest_Form_App
{
    
    protected $_fields;
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
        
        $this->_fields =
            array(
                  'favorite_book' => ( 'Your favorite book:'),
                  'favorite_movie' => ( 'Your favorite movie:'),
                  'favorite_website' => ( 'Your favorite website:'),
                  'favorite_movie_line' => ( 'Your favorite line from a movie:'),
                  'favorite_recording' => ( 'Your favorite recording:'),
                  'favorite_keepsake' => ( 'Your favorite keepsake or memento:'),
                  'favorite_source_inspiration' => ( 'Your favorite source of inspiration:'),
                  'favorite_word' => ( 'Your favorite word:'),
                  'adjectives'=> ( 'Two adjectives your friends would use to describe you:')
                  );
        require_once 'CRM/Quest/BAO/Essay.php';
        $this->_essays = CRM_Quest_BAO_Essay::getFields( 'cm_partner_princeton_short_essay', $this->_contactID, $this->_contactID );
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
        foreach ( $this->_fields as $name => $titles ) {
            $this->add( 'text', $name, $titles, null);
        }
            require_once 'CRM/Quest/BAO/Essay.php';
            $this->_essays = CRM_Quest_BAO_Essay::getFields( 'cm_partner_princeton_short_essay', $this->_contactID, $this->_contactID );
            CRM_Quest_BAO_Essay::buildForm( $this, $this->_essays );
           
            $this->assign_by_ref('fields',$this->_fields);
            parent::buildQuickForm( );
    }
  /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle()
    {
         return ts('Short Answers');
    }

    public function getRootTitle( ) {
        return ts( 'Princeton University' );
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