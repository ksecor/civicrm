<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Quest/Form/App.php';
require_once 'CRM/Core/OptionGroup.php';

/**
 * This class generates form components for the amherst application
 * 
 */
class CRM_Quest_Form_MatchApp_Partner_Amherst_AmhApplicant extends CRM_Quest_Form_App
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
                  'quest'             => array( 'QuestBridge'			, 'Specify how'),
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

        require_once 'CRM/Quest/Partner/DAO/Amherst.php';
        $dao =& new CRM_Quest_Partner_DAO_Amherst( );
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
        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_Partner_DAO_Amherst');

        $this->assign_by_ref('fields',$fields);
        // add a checkbox and text box for each of the above
        foreach ( $this->_fields as $name => $titles ) {
            $extra = array('onchange' => "return showHideByValue('is_{$name}', '1', 'id_{$name}_show','block', 'radio', false);");
            $cb =& $this->addElement( 'checkbox', "is_{$name}", $titles[0], null, $extra );
            $cb->updateAttributes( array( 'id' => "is_{$name}" ) );
            $this->add( 'text', $name, $titles[1], $attributes[$name] );
        }

        $this->assign_by_ref('fields',$this->_fields);
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
         return ts('Applicant Information');
    }

    public function getRootTitle( ) {
        return ts( 'Amherst College' );
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
        
        $params = $this->controller->exportValues( $this->_name );
        require_once 'CRM/Quest/Partner/DAO/Amherst.php';
        $dao =& new CRM_Quest_Partner_DAO_Amherst( );
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
