<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Core/SelectValues.php';
require_once 'CRM/Quest/Form/App.php';

/**
 * This class generates form components for relationship
 * 
 */
class CRM_Quest_Form_SchoolSearch extends CRM_Quest_Form_App
{
    /**
     * max number of schools we will display
     */
    const MAX_SCHOOLS = 50;
          
    function preProcess( ) 
    {
        // Assign schoolIndex from URL to the template (controls which school fieldset to populate)
        $this->_schoolIndex = CRM_Utils_Request::retrieve('schoolIndex', 'Positive',
                                                  $this);
        $this->assign('schoolIndex',$this->_schoolIndex);
        $this->assign('displayRecent',0);
    }

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $this->addElement('text', 'school_name'      , ts('School Name'    ) );
        $this->addElement('text', 'postal_code'      , ts('Postal Code'    ) );
        $this->addElement('text', 'city'             , ts('City'           ) );

        $this->addElement('select', 'state_province_id'   , ts('State / Province' ),
                          array('' => ts('- select -')) + CRM_Core_PseudoConstant::stateProvince());

        $searchRows            = $this->get( 'searchRows'    );
        $searchCount           = $this->get( 'searchCount'   );
        $searchDone            = $this->get( 'searchDone'    );
        $stateProvince         = $this->get( 'stateProvince' );

        $this->assign( 'searchCount'          , $searchCount   );
        $this->assign( 'searchDone'           , $searchDone    );
        $this->assign( 'searchRows'           , $searchRows    );
        $this->assign( 'stateProvince'        , $stateProvince );

        if ( $searchDone ) {
            $searchBtn = ts('Search Again');
        } else {
            $searchBtn = ts('Find Your School');
        }
        $this->addElement( 'submit', $this->getButtonName('refresh'), $searchBtn, array( 'class' => 'form-submit' ) );
        $this->addElement( 'submit', $this->getButtonName('cancel' ), ts('Cancel'), array( 'class' => 'form-submit' ) );
    }

    /**
     *  This function is called when the form is submitted 
     *
     * @access public
     * @return None
     */
    function postProcess( ) {
        // store the submitted values in an array
        $params = $this->controller->exportValues( $this->_name );

        $this->set( 'searchDone', 1 );

        // create the select clause
        $clause = array( 1 );

        if ( ! empty( $params['school_name'] ) ) {
            $clause[] = "LOWER(school_name) LIKE '%" . strtolower( addslashes( $params['school_name'] ) ) . "%'";
        }

        if ( ! empty( $params['postal_code'] ) ) {
            $clause[] = "postal_code LIKE '" . strtolower( addslashes( $params['postal_code'] ) ) . "%'";
        }

        if ( ! empty( $params['city'] ) ) {
            $clause[] = "LOWER(city) LIKE '%" . strtolower( addslashes( $params['city'] ) ) . "%'";
        }

        $this->set( 'stateProvince', '' );
        if ( ! empty( $params['state_province_id'] ) ) {
            $clause[] = 
                "state_province_id = " .
                CRM_Utils_Type::escape( $params['state_province_id'], 'Integer' );
            $this->set( 'stateProvince', CRM_Core_PseudoConstant::stateProvince( $params['state_province_id'] ) );
        }

        $whereClause = implode( ' AND ', $clause );

        $args = array( 'code', 'school_name', 'street_address', 'city', 'postal_code',
                       'state_province', 'state_province_id', 'country_id', 
                       'school_type' );
        $select = implode( ',', $args );
        $query = "
SELECT   $select
FROM     quest_ceeb
WHERE    $whereClause
ORDER BY school_name
";
        $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        $searchCount = $dao->N;
        $this->set( 'searchCount', $searchCount );
        if ( $searchCount <= self::MAX_SCHOOLS ) {
            $searchRows = array( );
            while ( $dao->fetch( ) ) {
                $row = array( );
                foreach ( $args as $arg ) {
                    $row[$arg] = $dao->$arg;
                }
                $searchRows[] = $row;
            }
            $this->set( 'searchRows' , $searchRows );
        } else {
            // resetting the session variables if many records are found
            $this->set( 'searchRows' , null );
        }
    }
    
}

?>
