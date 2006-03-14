<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
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
class CRM_Quest_Form_App_Household extends CRM_Quest_Form_App
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
        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_DAO_Person');

        for ( $i = 1; $i <= 2; $i++ ) {
            $this->addElement( 'text',
                               'member_count_' . $i,
                               ts( 'How many people live with you in your current household?' ),
                               $attributes['member_count'] );

            for ( $j = 1; $j <= 2; $j++ ) {
                $this->addSelect( 'select', "relationship",
                                   ts( 'Relationship' ),
                                  "_$i_$j" );
                $this->addElement( 'text', "first_name_$i_$j",
                                   ts('First Name'),
                                   $attributes['first_name'] );
                $this->addElement( 'text', "last_name_$i_$j",
                                   ts('Last Name'),
                                   $attributes['last_name'] );

                if ( $i == 2 ) {
                    $this->addElement( 'checkbox', "same_$i_$j", null, null );
                }
            }

            $this->addSelect( "years_lived",
                               ts( 'How long have you lived in this household?' ),
                              "_$i" );
        }

        $this->addElement('textarea',
                          'household_note',
                          ts( 'List and describe the factors in your life that have most shaped you (1500 characters max).' ),
                          CRM_Core_DAO::getAttribute( 'CRM_Quest_DAO_Student', 'household_note' ) );

    }//end of function

}

?>

