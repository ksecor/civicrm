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
 * Amherst Essay
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
 * This class generates form components for the amherst essay
 * 
 */
class CRM_Quest_Form_MatchApp_Partner_Amherst_AmhAthletics extends CRM_Quest_Form_App
{

    protected $_essays;

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        parent::preProcess();
        require_once 'CRM/Quest/BAO/Essay.php';
        $this->_essays = CRM_Quest_BAO_Essay::getFields( 'cm_partner_amherst_athletic', $this->_contactID, $this->_contactID );
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

        require_once 'CRM/Quest/BAO/Extracurricular.php';
        CRM_Quest_BAO_Extracurricular::setDefaults( $this->_contactID, 'Amherst', $defaults );

        require_once 'CRM/Quest/Partner/DAO/Amherst.php';
        $dao =& new CRM_Quest_Partner_DAO_Amherst( );
        $dao->contact_id = $this->_contactID;
        if ( $dao->find( true ) ) {
            $defaults['height'] = $dao->height;
            $defaults['weight'] = $dao->weight;
        }

        $defaults['essay'] = array( );

        CRM_Quest_BAO_Essay::setDefaults( $this->_essays, $defaults['essay'] );
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

        require_once 'CRM/Quest/BAO/Extracurricular.php';
        CRM_Quest_BAO_Extracurricular::buildForm( $this, 'Amherst' );

        $this->add( 'text', 'height', ts( 'Height' ), $attributes['height'] );
        $this->add( 'text', 'weight', ts( 'Weight' ), $attributes['weight'] );

        CRM_Quest_BAO_Essay::buildForm( $this, $this->_essays, false );

        $this->addFormRule( array( 'CRM_Quest_Form_MatchApp_Partner_Amherst_AmhAthletics',
                                   'formRule' ),
                            'Amherst' );


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
         return ts('Athletics Supplement');
    }

    public function getRootTitle( ) {
        return ts( 'Amherst College' );
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
    public function formRule(&$params, $options) {
        return CRM_Quest_BAO_Extracurricular::formRule( $params, $options );
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

        require_once 'CRM/Quest/BAO/Extracurricular.php';

        require_once 'CRM/Quest/Partner/DAO/Amherst.php';
        $dao =& new CRM_Quest_Partner_DAO_Amherst( );
        $dao->contact_id = $this->_contactID;
        $dao->find( true );
        $dao->copyValues($params);
        $dao->save();

        CRM_Quest_BAO_Extracurricular::process( $this->_contactID, 'Amherst', $params );

        CRM_Quest_BAO_Essay::create( $this->_essays, $params['essay'],
                                     $this->_contactID, $this->_contactID ); 

        parent::postProcess( );
    } 
   
}

?>