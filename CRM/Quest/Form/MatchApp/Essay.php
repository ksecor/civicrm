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
 * Personal Information Form Page
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Quest/Form/App.php';
require_once 'CRM/Core/OptionGroup.php';
require_once 'CRM/Quest/BAO/Essay.php';

/**
 * This class generates form components for relationship
 * 
 */
class CRM_Quest_Form_MatchApp_Essay extends CRM_Quest_Form_App
{
    protected $_grouping = null;

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

        $this->_essays = CRM_Quest_BAO_Essay::getFields( $this->_grouping, $this->_contactID, $this->_contactID );
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
        $defaults          = array( );
        $defaults['essay'] = array( );

        require_once "CRM/Quest/BAO/Essay.php";
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

        require_once "CRM/Quest/BAO/Essay.php";
        CRM_Quest_BAO_Essay::buildForm( $this, $this->_essays );

        parent::buildQuickForm();
    }//end of function

    public function postProcess() 
    {
        if ( ! ( $this->getAction( ) &  CRM_Core_Action::VIEW ) ) {
            $params = $this->controller->exportValues( $this->_name );

            CRM_Quest_BAO_Essay::create( $this->_essays, $params['essay'], 
                                         $this->_contactID, $this->_contactID );

            parent::postProcess( );
        }
    }//end of function

}

?>
