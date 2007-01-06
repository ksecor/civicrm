<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                |
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
 | Foundation at info[AT]civicrm[DOT]org. If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Event/Form/ManageEvent.php';
require_once 'CRM/Core/SelectValues.php';

/**
 * This class generates form components for processing Event  
 * 
 */
class CRM_Event_Form_ManageEvent_Location extends CRM_Event_Form_ManageEvent
{

    /**
     * how many locationBlocks should we display?
     *
     * @var int
     * @const
     */
    const LOCATION_BLOCKS = 1;
    
    /**
     * the variable, for storing the location array
     *
     * @var array
     */
    protected $_ids;

    /**
     * This function sets the default values for the form. Note that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {    
        $defaults = array( );
        $params   = array( );
        if ( isset( $this->_id ) ) {
            $params = array( 'entity_id' => $this->_id ,'entity_table' => 'civicrm_event');
            require_once 'CRM/Core/BAO/Location.php';
            $location = CRM_Core_BAO_Location::getValues($params, $defaults, $ids, self::LOCATION_BLOCKS);
            $this->_ids = $ids;
        }
       
        if ( ! empty( $params ) ) {
            $this->setShowHide( $params, true );
        } else {
            $this->setShowHide( $defaults, false );
        }

        return $defaults;
    }

    /**
     * Fix what blocks to show/hide based on the default values set
     *
     * @param array   $defaults the array of default values
     * @param boolean $force    should we set show hide based on input defaults
     *
     * @return void
     */
    function setShowHide( &$defaults, $force ) {
        $this->_showHide =& new CRM_Core_ShowHideBlocks( array(),'') ;
        // first do the defaults showing
        $config =& CRM_Core_Config::singleton( );
        CRM_Contact_Form_Location::setShowHideDefaults( $this->_showHide, self::LOCATION_BLOCKS );
        
        $this->_showHide->addToTemplate( );
    }
    
    /** 
     *  function to build location block 
     * 
     * @return None 
     * @access public 
     */ 
    public function buildQuickForm( )  
    { 
        $this->assign( 'locationCount', self::LOCATION_BLOCKS + 1);
        
        require_once 'CRM/Contact/Form/Location.php';
        CRM_Contact_Form_Location::buildLocationBlock( $this, self::LOCATION_BLOCKS );
        $this->assign( 'blockCount'   , CRM_Contact_Form_Location::BLOCKS + 1 );
        $this->addButtons(array(
                                array ( 'type'      => 'back',
                                        'name'      => ts('<< Previous') ),
                                array ( 'type'      => 'next',
                                        'name'      => ts('Save'),
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;',
                                        'isDefault' => true   ),
                                array ( 'type'      => 'cancel',
                                        'name'      => ts('Cancel') ),
                                )
                          );
    }
    
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        $params = array( );
        $params = $this->exportValues( );

        $params['entity_table'] = 'civicrm_event';
        $params['entity_id']    = $this->_id;

        require_once 'CRM/Core/BAO/Location.php';
        CRM_Core_BAO_Location::add($params, $this->_ids, self::LOCATION_BLOCKS);
        CRM_Core_Session::setStatus( ts('The Event Location has been saved.' ));
        
    }//end of function

    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle( ) 
    {
        return ts('Event Location');
    }
}
?>
