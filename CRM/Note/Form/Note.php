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
class CRM_Note_Form_Note extends CRM_Form
{
    /**
     * The table name, used when editing/creating a note
     *
     * @var string
     */
    protected $_tableName;

    /**
     * The table id, used when editing/creating a note
     *
     * @var int
     */
    protected $_tableId;
    
    /**
     * The note id, used when editing the note
     *
     * @var int
     */
    protected $_noteId;

    /**
     * class constructor
     *
     * @param string $name        Name of the form.
     * @param string $state       The state object associated with this form
     * @param int     $mode       The mode of the form
     *
     * @return CRM_Note_Form_Note
     * @access public
     */
    function __construct($name, $state, $mode = self::MODE_NONE) {
        parent::__construct($name, $state, $mode);
    }

    function preProcess( ) {
        $this->_tableName = $this->get( 'tableName' );
        $this->_tableId   = $this->get( 'tableId'   );
        $this->_noteId    = $this->get( 'noteId'    );
    }

    /**
     * This function sets the default values for the form. Note that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) {
        $defaults = array( );
        $params   = array( );

        if ( $this->_mode & self::MODE_UPDATE ) {
            if ( isset( $this->_noteId ) ) {
                $defaults['note'] = CRM_BAO_Note::getNoteText( $this->_noteId );
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
        $note = $this->add('textarea', 'note', 'Notes', array('rows' => 4, 'cols' => '82',));    
        $this->addDefaultButtons( array(
                                        array ( 'type'      => 'next',
                                                'name'      => 'Save',
                                                'isDefault' => true   ),
                                        array ( 'type'      => 'reset',
                                                'name'      => 'Reset'),
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
        $note                = new CRM_DAO_Note( );
        $note->note          = $params['note'];
        $note->contact_id    = 1;
        $note->modified_date = date("Ymd");

        if ($this->_mode & self::MODE_UPDATE ) {
            $note->id = $this->_noteId;
        } else {
            $note->table_name = $this->_tableName;
            $note->table_id   = $this->_tableId;
        }
        $note->save( );

        $session = CRM_Session::singleton( );

        $session->setStatus( "Your Note has been saved." );
    }//end of function


}

?>
