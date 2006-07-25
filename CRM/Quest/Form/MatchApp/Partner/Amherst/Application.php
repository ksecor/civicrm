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
 * Amherst Appliction
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Quest/Form/App.php';
require_once 'CRM/Core/OptionGroup.php';

/**
 * This class generates form components for the amherst application
 * 
 */
class CRM_Quest_Form_MatchApp_Partner_Amherst_Application extends CRM_Quest_Form_App
{
    
    protected $_fields;

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        parent::preProcess();
        
        $this->_fields =
            array(
                  'publication'       => array( 'Amherst Publication'           , 'Publication Name'        ),
                  'representative'    => array( 'Amherst Representative'        , 'Representative Name'     ),
                  'campus_visit'      => array( 'Campus Visit'                  , 'Whom did you Meet?'      ),
                  'college_counselor' => array( 'College Counselor'             , 'Counselor Name'          ),
                  'website'           => array( 'Amherst College Website'       , 'Site URL'                ),
                  'guidebook'         => array( 'Guide Books/Magazines/Websites', 'Name(s)'                 ),
                  'siblings'          => array( 'Siblings, parents, or grandparents who attended', 'Name(s)'),
                  'other'             => array( 'Other'                         , 'Name(s)'                 )
                  );
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

        require_once 'CRM/Quest/DAO/Partner/Amherst.php';
        $dao =& new CRM_Quest_DAO_Partner_Amherst( );
        $dao->contact_id = $this->_contactID;
        if ( $dao->find( true ) ) {
            foreach ( $this->_fields as $name => $titles ) {
                $cond = "is_{$name}";
                if ( $dao->$cond ) {
                    $defaults[$cond] = 1;
                }
                $defaults[$name] = $dao->$name;
            }
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
        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_DAO_Partner_Amherst');

        $this->assign_by_ref('fields',$fields);
        // add a checkbox and text box for each of the above
        foreach ( $this->_fields as $name => $titles ) {
            $this->add( 'checkbox', "is_{$name}", $titles[0], null, true );
            $this->add( 'text', $name, $titles[1], $attributes[$name] );
        }

        parent::buildQuickForm( );
                
    }//end of function

    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle()
    {
         return ts('Recommendations');
    }

    /**
     * Function for validation
     *
     * @param array $params (ref.) an assoc array of name/value pairs
     *
     * @return mixed true or array of errors
     * @access public
     * @static
     */
    public function formRule(&$params) {
        return false;
    }

    /** 
     * process the form after the input has been submitted and validated 
     * 
     * @access public 
     * @return void 
     */ 
    public function postProcess() {
        if ( $this->_action &  CRM_Core_Action::VIEW ) {
            return;
        }

        require_once 'CRM/Quest/DAO/Partner/Amherst.php';
        $dao =& new CRM_Quest_DAO_Partner_Amherst( );
        $dao->contact_id = $this->_contactID;
        $dao->find( true );

        foreach ( $this->_fields as $name => $titles ) {
            $cond = "is_{$name}";
            $dao->$cond = CRM_Utils_Array::value( $cond, $params, false );
            $dao->$name = $params[$name];
        }

        $dao->save( );

        parent::postProcess( );
    } 
   
}

?>