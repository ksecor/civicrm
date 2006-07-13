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
 * Personal Information Form Page
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
 * This class generates form components for relationship
 * 
 */
class CRM_Quest_Form_MatchApp_Essay extends CRM_Quest_Form_App
{
    protected $_grouping = null;

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

        require_once 'CRM/Quest/DAO/EssayType.php';

        $this->_essays = array( );
        $type =& new CRM_Quest_DAO_EssayType( );
        $type->grouping  = $this->_grouping;
        $type->is_active = 1;
        $type->orderby( 'weight asc' );

        $type->find( );
        while ( $type->fetch( ) ) {
            $this->_essays[] = array( 'id'         => $type->id,
                                      'name'       => $type->name,
                                      'label'      => $type->label,
                                      'attributes' => $type->attributes,
                                      'wordCount'  => $type->max_word_count,
                                      'required'   => $type->is_required );
        }
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

        require_once 'CRM/Quest/DAO/Essay.php';

        foreach ( $this->_essays as $name => $essay ) {
            $dao = & new CRM_Quest_DAO_Essay();
            $dao->source_contact_id = $this->_contactID;
            $dao->target_contact_id = $this->_contactID;
            $dao->essay_type_id     = $essay['id'];
            
            if ( $dao->find(true) ) {
                $this->_essays[$name]['essay'] = $dao->essay;
                $defaults["essay"][$name] = $dao->essay;
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
        foreach ( $this->_essays as $name => $essay ) {
            // primary method to access internet
            $this->add( 'textarea',
                        "essay[{$essay['name']}]",
                        $essay['label'],
                        $essay['attributes'],
                        $essay['required'] );
            
            if ( ! ( $this->_action & CRM_Core_Action::VIEW ) ) {
                $this->addElement('text', "word_count[{$essay['name']}]", ts( 'Current word count' ), 'readonly');
            }
        }

        $this->assign_by_ref( 'essays', $this->_essays );

        parent::buildQuickForm();
    }//end of function

  public function postProcess() 
    {
        if ( ! ( $this->_action &  CRM_Core_Action::VIEW ) ) {
            $params = $this->controller->exportValues( $this->_name );
            
            require_once 'CRM/Quest/BAO/Essay.php';
            
            $params['contact_id'] =  $this->_contactID;
            
            $ids = array( 'id' => $this->_essayID );
            
            CRM_Quest_BAO_Essay::create( $params, $ids);
        }
        parent::postProcess( );
    }//end of function



}

?>
