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

require_once 'CRM/Form.php';

/**
 * This class generates form components generic to note
 * 
 * It delegates the work to lower level subclasses and integrates the changes
 * back in. It also uses a lot of functionality with the CRM API's, so any change
 * made here could potentially affect the API etc. Be careful, be aware, use unit tests.
 *
 */
class CRM_LocationType_Form_LocationType extends CRM_Form
{
    
    /**
     * The note id, used when editing the note
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
     * @return CRM_LocationType_Form_LocationType
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
                $locType = new CRM_Contact_DAO_LocationType();
                
                $locType->id = $this->_locationTypeId;
                $locType->find(true);
                
                $defaults['name'] = $locType->name;
                $defaults['description'] = $locType->description;
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

        $this->add('text', 'name', 'Name:');
        $this->add('textarea', 'description', 'Description:', array('rows' => 4, 'cols' => '82',));    
        
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
        $locType               = new CRM_Contact_DAO_LocationType( );
        $locType->domain_id    = 1;
        $locType->name         = $params['name'];
        $locType->description  = $params['description'];

        if ($this->_mode & self::MODE_UPDATE ) {
            $locType->id = $this->_locationTypeId;
        }else {
            $locType->is_active    = 1;        
        }

        $locType->save( );

        $session = CRM_Session::singleton( );

        $session->setStatus( " Location Type has been saved." );
    }//end of function


}

?>
