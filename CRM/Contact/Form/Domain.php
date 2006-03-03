<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
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
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/ShowHideBlocks.php';

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
        
        $this->_id = CRM_Core_Config::domainID();
        $this->_action = CRM_Utils_Request::retrieve( 'action', $this, false, 'view' );
        
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
        $this->add('text', 'email_return_path', ts('Send Emails RETURN-PATH:'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_Email','email'));
        $this->addRule( "email_return_path", ts('Email is not valid.'), 'email' );
        
        $this->assign( 'locationCount', self::LOCATION_BLOCKS + 1 );
        $location =& CRM_Contact_Form_Location::buildLocationBlock( $this, self::LOCATION_BLOCKS );
        
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
        $params['domain_id'] = $this->_id;
        
        $domain = CRM_Core_BAO_Domain::edit($params, $this->_id);

        $location = array();
        for ($locationId = 1; $locationId <= self::LOCATION_BLOCKS ; $locationId++) { // start of for loop for location
            $location[$locationId] = CRM_Core_BAO_Location::add($params, $this->_ids, $locationId);
        }

        CRM_Core_Session::setStatus( ts('The Domain "%1" has been saved.', array( 1 => $domain->name )) );
        
    }
    
}

?>