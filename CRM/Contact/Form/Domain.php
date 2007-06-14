<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

/**
 * This class is to build the form for adding Group
 */
class CRM_Contact_Form_Domain extends CRM_Core_Form {

    /**
     * the group id, used when editing a group
     *
     * @var int
     */
    protected $_id;

    /**
     * the variable, for storing the location array
     *
     * @var array
     */
    protected $_ids;

    /**
     * how many locationBlocks should we display?
     *
     * @var int
     * @const
     */
    const LOCATION_BLOCKS = 1;

    function preProcess( ) {
        
        CRM_Utils_System::setTitle(ts('CiviMail Domain Information'));
        $breadCrumbPath = CRM_Utils_System::url( 'civicrm/admin', 'reset=1' );
        $additionalBreadCrumb = "<a href=\"$breadCrumbPath\">" . ts('Administer CiviCRM') . '</a>';
        CRM_Utils_System::appendBreadCrumb( $additionalBreadCrumb );

        $this->_id = CRM_Core_Config::domainID();
        $this->_action = CRM_Utils_Request::retrieve( 'action', 'String',
                                                      $this, false, 'view' );
        
    }
    
    /*
     * This function sets the default values for the form.
     * the default values are retrieved from the database
     *
     * @access public
     * @return None
     */
    
    function setDefaultValues( ) {
        
        require_once 'CRM/Core/BAO/Domain.php';

        $defaults = array( );
        $params   = array( );
        $locParams = array();
        
        if ( isset( $this->_id ) ) {
            $params['id'] = $this->_id ;
            CRM_Core_BAO_Domain::retrieve( $params, $defaults );
            unset($params['id']);
            $locParams = $params + array('entity_id' => $this->_id, 'entity_table' => 'civicrm_domain');
            require_once 'CRM/Core/BAO/Location.php';
            CRM_Core_BAO_Location::getValues( $locParams, $defaults, $ids, 3);
            $this->_ids = $ids;
        }
        return $defaults;
    }
    
    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */

    public function buildQuickForm( ) {
        
        $this->add('text', 'name' , ts('Name:') , array('size' => 25));
        $this->add('text', 'description', ts('Description:'), array('size' => 25) );
        $this->add('text', 'contact_name', ts('Contact Name:'), array('size' => 25) );
        $this->add('text', 'email_domain', ts('Email Domain:'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_Email','email'));
        $this->addRule( "email_domain", ts('Email is not valid.'), 'domain' );
        $this->add('text', 'email_return_path', ts('Return-Path:'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_Email','email'));
        $this->addRule( "email_return_path", ts('Email is not valid.'), 'email' );
        
        //blocks to be displayed
        $this->assign( 'locationCount', self::LOCATION_BLOCKS + 1);       
        require_once 'CRM/Contact/Form/Location.php';
        $locationCompoments = array('Phone', 'Email');
        CRM_Contact_Form_Location::buildLocationBlock( $this, self::LOCATION_BLOCKS ,$locationCompoments);
        $this->assign( 'index' , 1 );
        $this->assign( 'blockCount'   , 1 );

        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Save'),
                                         'subName'   => 'view',
                                         'isDefault' => true   ),
                                 array ( 'type'       => 'cancel',
                                         'name'      => ts('Cancel') ) ) );
        
        if ($this->_action & CRM_Core_Action::VIEW ) { 
            $this->freeze();
        }        
        $this->assign('emailDomain',true);
    }

    /**
     * Process the form when submitted
     *
     * @return void
     * @access public
     */

    public function postProcess( ) {

        require_once 'CRM/Core/BAO/Domain.php';

        $params = array( );
        
        $params = $this->exportValues();
        $params['entity_id'] = $this->_id;
        $params['entity_table'] = CRM_Core_BAO_Domain::getTableName();
        $domain = CRM_Core_BAO_Domain::edit($params, $this->_id);

        $location = array();
        for ($locationId = 1; $locationId <= self::LOCATION_BLOCKS ; $locationId++) { // start of for loop for location
            $location[$locationId] = CRM_Core_BAO_Location::add($params, $this->_ids, $locationId);
        }
        
        CRM_Core_Session::setStatus( ts('The Domain "%1" has been saved.', array( 1 => $domain->name )) );
        
    }
    
}

?>
