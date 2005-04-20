<?php
/**
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

/**
 * This class generates form components for Location Type
 * 
 */
class CRM_Admin_Form_LocationType extends CRM_Form
{
    
    /**
     * The location type id, used when editing location type
     *
     * @var int
     */
    protected $_locationTypeId;

    /**
     * class constructor
     *
     * @param string $name        Name of the form.
     * @param string $state       The state object associated with this form
     * @param int     $mode       The mode of the form
     *
     * @return CRM_Admin_Form_LocationType
     * @access public
     */
    function __construct($name, $state, $mode = self::MODE_NONE) {
        parent::__construct($name, $state, $mode);
    }

    function preProcess( ) {
        $this->_locationTypeId    = $this->get( 'locationTypeId' );
    }

    /**
     * This function sets the default values for the form. LocationType that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) {
        $defaults = array( );
        $params   = array( );


        if ( $this->_mode & self::MODE_UPDATE ) {
            if ( isset( $this->_locationTypeId ) ) {
                $locationType = new CRM_Contact_DAO_LocationType();
                
                $locationType->id = $this->_locationTypeId;
                $locationType->find(true);
                
                $defaults['name'] = $locationType->name;
                $defaults['description'] = $locationType->description;
            }
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
        $this->add('text', 'name'       , 'Name'       ,
                   CRM_DAO::getAttribute( 'CRM_Contact_DAO_LocationType', 'name' ) );
        $this->add('text', 'description', 'Description', 
                   CRM_DAO::getAttribute( 'CRM_Contact_DAO_LocationType', 'description' ) );
        
        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => 'Save',
                                         'isDefault' => true   ),
                                 array ( 'type'       => 'cancel',
                                         'name'      => 'Cancel' ),
                                 )
                           );
        
    }

       
    /**
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        // store the submitted values in an array
        $params = $this->exportValues();

        // action is taken depending upon the mode
        $locationType               = new CRM_Contact_DAO_LocationType( );
        $locationType->domain_id    = 1;
        $locationType->name         = $params['name'];
        $locationType->description  = $params['description'];

        if ($this->_mode & self::MODE_UPDATE ) {
            $locationType->id = $this->_locationTypeId;
        }else {
            $locationType->is_active    = 1;        
        }

        $locationType->save( );

        CRM_Session::setStatus( 'The Location Type ' . $locationType->name . ' has been saved.' );
    }//end of function


}

?>
