<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';


class CRM_Contact_Form_StateCountry extends CRM_Core_Form
{
    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    static function stateCountryBuildForm(&$form, &$location, $locationId)
    {
        
        $form->addElement('text', "location[$locationId][address][state]", ts('State / Province'), 'onkeyup="getState(this,event, false);"  onblur="getState(this,event, false);" autocomplete="off"' );
        
        $form->addElement('hidden', "location[$locationId][address][state_province_id]", "", "id=location[$locationId][address][state_province_id]");
        $form->addElement('select', "location[$locationId][address][country_id]", ts('Country'), array('' => ts('- select -')), 'onblur="getState(this,event, true);"');

    }
       
 }

?>
