<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

/**
 * This class generates form components for processing Event  
 * 
 */
class CRM_Event_Form_ManageEvent extends CRM_Core_Form
{
    /**
     * the id of the event we are proceessing
     *
     * @var int
     * @protected
     */
    public $_id;

    /**
     * is this the first page?
     *
     * @var boolean 
     * @access protected  
     */  
    protected $_first = false;
 
    /** 
     * are we in single form mode or wizard mode?
     * 
     * @var boolean
     * @access protected 
     */ 
    protected $_single;
    
    /**
     * are we actually managing an event template?
     * @var boolean
     */
    protected $_isTemplate = false;

    /**
     * pre-populate fields based on this template event_id
     * @var integer
     */
    protected $_templateId;
    
    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    function preProcess( ) 
    {
        $this->_action = CRM_Utils_Request::retrieve('action', 'String', $this, false);

        $this->_id = CRM_Utils_Request::retrieve( 'id', 'Positive', $this );

        $this->_single = $this->get( 'single' );

        $this->_isTemplate = (bool) CRM_Utils_Request::retrieve('is_template', 'Boolean', $this);
        if (!$this->_isTemplate and $this->_id) {
            $this->_isTemplate = (bool) CRM_Core_DAO::getFieldValue('CRM_Event_DAO_Event', $this->_id, 'is_template');
        }
        $this->assign('isTemplate', $this->_isTemplate);

        $this->_templateId = (int) CRM_Utils_Request::retrieve('template_id', 'Integer', $this);

        if ($this->_isTemplate) {
            $breadCrumb = array(array('title' => ts('Event Templates'),
                                      'url'   => CRM_Utils_System::url('civicrm/admin/eventTemplate', 'reset=1')));
        } elseif ($this->_id) {
            $breadCrumb = array( array('title' => ts('Configure Event'),
                                       'url'   => CRM_Utils_System::url( CRM_Utils_System::currentPath( ), "action=update&reset=1&id={$this->_id}" )) );
        }
        CRM_Utils_System::appendBreadCrumb($breadCrumb);
        
    }
    
    /**
     * This function sets the default values for the form. For edit/view mode
     * the default values are retrieved from the database
     *
     * @access public
     * @return None
     */
    function setDefaultValues( )
    {
        $defaults = array( );
        if ( isset( $this->_id ) ) {
            $params = array( 'id' => $this->_id );
            require_once 'CRM/Event/BAO/Event.php';
            CRM_Event_BAO_Event::retrieve($params, $defaults);
        } elseif ($this->_templateId) {
            $params = array('id' => $this->_templateId);
            require_once 'CRM/Event/BAO/Event.php';
            CRM_Event_BAO_Event::retrieve($params, $defaults);
            $defaults['is_template'] = $this->_isTemplate;
            $defaults['template_id'] = $defaults['id'];
            unset($defaults['id']);
        } else {
            $defaults['is_active'] = 1;
            $defaults['style']     = 'Inline';
        }

        return $defaults;
    }

    /** 
     * Function to build the form 
     * 
     * @return None 
     * @access public 
     */ 
    public function buildQuickForm( )  
    { 
        $className = CRM_Utils_System::getClassName($this);
        $session = & CRM_Core_Session::singleton( );
        
        $buttons = array( );
        if ( $this->_single ) {

            // make this form an upload since we dont know if the custom data injected dynamically
            // is of type file etc $uploadNames = $this->get( 'uploadNames' );
            $this->addButtons(array(
                                    array ( 'type'      => 'upload',
                                            'name'      => ts('Save'),
                                            'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                            'isDefault' => true   ),
                                    array ( 'type'      => 'cancel',
                                            'name'      => ts('Cancel') ),
                                    )
                              );
        } else {
            $buttons = array( );
            if ( ! $this->_first ) {
                $buttons[] =  array ( 'type'      => 'back', 
                                      'name'      => ts('<< Previous'), 
                                      'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' );
            }
            $buttons[] = array ( 'type'      => 'upload',
                                 'name'      => ts('Continue >>'),
                                 'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                 'isDefault' => true   );
            $buttons[] = array ( 'type'      => 'cancel',
                                 'name'      => ts('Cancel') );
            
            $this->addButtons( $buttons );

        }

        $this->add('hidden', 'is_template', $this->_isTemplate);
        if ($this->_templateId and !isset($this->_elementIndex['template_id'])) {
            $this->add('hidden', 'template_id', $this->_templateId);
        }
    }
}

