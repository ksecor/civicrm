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
class CRM_Quest_Form_Recommender_Submit extends CRM_Quest_Form_App
{
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
    $this->add( 'checkbox', "is_partner_share", null, null, true );
        
    parent::buildQuickForm();
}//end of function


/**
* process the form after the input has been submitted and validated
 *
 * @access public
 * @return void
 */
public function postProcess() 
{
    // make sure that all forms are valid at this stage
    // if not jump to that page
    $this->controller->checkApplication( );
    
    parent::postProcess( );
}//end of function


/**
* Return a descriptive name for the page, used in wizard header
 *
 * @Return string
 * @access public
 */
public function getTitle()
{
    return ts('Submit Recommendation');
}
}

?>