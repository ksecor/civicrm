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
class CRM_Quest_Form_App_Essay extends CRM_Quest_Form_App
{

    protected $_essayID = null;

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
        $defaults = array( );

        require_once 'CRM/Quest/DAO/Essay.php';
        $dao = & new CRM_Quest_DAO_Essay();
        $dao->contact_id = $this->_contactID;
        if ( $dao->find(true) ) {
            $defaults['essay'] = $dao->essay;
            $this->_essayID = $dao->id;
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
        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_DAO_Essay');
        CRM_Core_Error::debug('Attr', $attributes);
        // primary method to access internet
        $this->add('textarea',
                   'essay',
                   ts( 'List and describe the factors in your life that have most shaped you (3000 characters max).' ),
                   array("onkeyup" => "countit();") + $attributes['essay'],
                   true);
        

        if ( ! ( $this->_action & CRM_Core_Action::VIEW ) ) {
            $this->addElement('text', 'word_count', ts( 'Current character count' ), 'readonly');
        }
        parent::buildQuickForm();


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

  public function postProcess() 
    {
        if ( ! ( $this->_action &  CRM_Core_Action::VIEW ) ) {
            $params = $this->controller->exportValues( $this->_name );
            
            require_once 'CRM/Quest/BAO/Essay.php';
            
            $params['contact_id'] =  $this->_contactID;
            
            $ids = array( 'id' => $this->_essayID );
            
            CRM_Quest_BAO_Essay::create( $params, $ids);
        }
        parent::postProcess( );
    }//end of function



}

?>
