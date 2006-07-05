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
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */


/**
 * form helper class for an IM object 
 */
class CRM_Contact_Form_IM
{
    /**
     * build the form elements for an IM object
     *
     * @param CRM_Core_Form $form       reference to the form object
     * @param array         $location   the location object to store all the form elements in
     * @param int           $locationId the locationId we are dealing with
     * @param int           $count      the number of blocks to create
     *
     * @return void
     * @access public
     * @static
     */
    static function buildIMBlock(&$form, &$location, $locationId, $count) {
        for ($i = 1; $i <= $count; $i++) {
            $label = ($i == 1) ? ts('Instant Messenger (preferred)') : ts('Instant Messenger');

            CRM_Core_ShowHideBlocks::linksForArray( $form, $i, $count, "location[$locationId][im]", ts('another IM'), ts('hide this IM'));

            $location[$locationId]['im'][$i]['service_id'] = $form->addElement('select',
                                                                               "location[$locationId][im][$i][provider_id]",
                                                                               $label,
                                                                               array('' => ts('- select service -')) + CRM_Core_PseudoConstant::IMProvider()   );

            $location[$locationId]['im'][$i]['name'] = $form->addElement('text',
                                                                         "location[$locationId][im][$i][name]",
                                                                         null,
                                                                         CRM_Core_DAO::getAttribute('CRM_Core_DAO_IM',
                                                                                               'name'));
        }
    }

}


?>
