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
 * This class generates form components generic to IM provider
 * 
 */
class CRM_Admin_Form_IMProvider extends CRM_Form
{
    
    /**
     * The IM Provider id, used when editing IM Provider
     *
     * @var int
     */
    protected $_IMProviderId;

    /**
     * class constructor
     *
     * @param string $name        Name of the form.
     * @param string $state       The state object associated with this form
     * @param int     $mode       The mode of the form
     *
     * @return CRM_Admin_Form_IMProvider
     * @access public
     */
    function __construct($name, $state, $mode = self::MODE_NONE) {
        parent::__construct($name, $state, $mode);
    }

    function preProcess( ) {
        $this->_IMProviderId    = $this->get( 'IMProviderId' );
    }

    /**
     * This function sets the default values for the form. IMProvider that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) {
        $defaults = array( );
        $params   = array( );

        if ( $this->_mode & self::MODE_UPDATE ) {
            if ( isset( $this->_IMProviderId ) ) {
                $IMProvider = new CRM_DAO_IMProvider();
                
                $IMProvider->id = $this->_IMProviderId;
                $IMProvider->find(true);
                
                $defaults['name'] = $IMProvider->name;
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
                   CRM_DAO::getAttribute( 'CRM_DAO_IMProvider', 'name' ) );
             
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
        $IMProvider               = new CRM_DAO_IMProvider( );
        $IMProvider->name         = $params['name'];

        if ($this->_mode & self::MODE_UPDATE ) {
            $IMProvider->id = $this->_IMProviderId;
        }

        $IMProvider->save( );

        CRM_Session::setStatus( 'The IM Provider ' . $IMProvider->name . ' has been saved.' );
    }//end of function


}

?>
