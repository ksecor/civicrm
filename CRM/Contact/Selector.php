<?php
/*
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

require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/Selector/Base.php';
require_once 'CRM/Core/Selector/API.php';

require_once 'CRM/Utils/Pager.php';
require_once 'CRM/Utils/Sort.php';

require_once 'CRM/Contact/BAO/Contact.php';


/**
 * This class is used to retrieve and display a range of
 * contacts that match the given criteria (specifically for
 * results of advanced search options.
 *
 */
class CRM_Contact_Selector extends CRM_Core_Selector_Base implements CRM_Core_Selector_API 
{
    /**
     * This defines two actions- View and Edit.
     *
     * @var array
     */
    static $_links = array(
                           CRM_Core_Action::VIEW   => array(
                                                       'name'     => 'View',
                                                       'url'      => 'civicrm/contact/view',
                                                       'qs'       => 'reset=1&cid=%%id%%',
                                                       'title'    => 'View Contact Details',
                                                       ),
                           CRM_Core_Action::UPDATE => array(
                                                       'name'     => 'Edit',
                                                       'url'      => 'civicrm/contact/edit',
                                                       'qs'       => 'reset=1&cid=%%id%%',
                                                       'title'    => 'Edit Contact Details',
                                                       ),
                           );

    /* we use desc to remind us what that column is, name is used in the tpl */
    static $_columnHeaders = array(
                                   array('desc' => 'Select'),
                                   array('desc' => 'Contact Type'),
                                   array(
                                         'name'      => 'Name',
                                         'sort'      => 'sort_name',
                                         'direction' => CRM_Utils_Sort::ASCENDING,
                                         ),
                                   array('name' => 'Address'),
                                   array(
                                         'name'      => 'City',
                                         'sort'      => 'city',
                                         'direction' => CRM_Utils_Sort::DONTCARE,
                                         ),
                                   array(
                                         'name'      => 'State',
                                         'sort'      => 'state',
                                         'direction' => CRM_Utils_Sort::DONTCARE,
                                         ),
                                   array(
                                         'name'      => 'Postal',
                                         'sort'      => 'postal_code',
                                         'direction' => CRM_Utils_Sort::DONTCARE,
                                         ),
                                   array(
                                         'name'      => 'Country',
                                         'sort'      => 'country',
                                         'direction' => CRM_Utils_Sort::DONTCARE,
                                         ),
                                   array(
                                         'name'      => 'Email',
                                         'sort'      => 'email',
                                         'direction' => CRM_Utils_Sort::DONTCARE,
                                         ),
                                   array('name' => 'Phone'),
                                   array('desc' => 'Actions'),
                                   );

    static $_properties = array('contact_id', 'contact_type', 'sort_name', 'street_address',
                                'city', 'state', 'country', 'postal_code',
                                'email', 'phone' );

    /**
     * This caches the content for the display system.
     *
     * @var string
     * @access protected
     */
    protected $_contact;

    /**
     * formValues is the array returned by exportValues called on
     * the HTML_QuickForm_Controller for that page.
     *
     * @var array
     * @access protected
     */
    protected $_formValues;

    /**
     * represent the type of selector
     *
     * @var int
     * @access protected
     */
    protected $_mode;


    /**
     * Class constructor
     *
     * @param array $formValues array of parameters for query
     * @param int   $mode - mode of search basic or advanced.
     *
     * @return CRM_Contact_AdvancedSelector
     * @access public
     */
    function __construct(&$formValues, $mode = CRM_Core_Form::MODE_NONE) 
    {
        //object of BAO_Contact_Individual for fetching the records from db
        $this->_contact = new CRM_Contact_BAO_Contact();

        // submitted form values
        $this->_formValues =& $formValues;

        // type of selector
        $this->_mode = $mode;

    }//end of constructor


    /**
     * This method returns the links that are given for each search row.
     * currently the links added for each row are 
     * 
     * - View
     * - Edit
     *
     * @param none
     *
     * @return array
     * @access public
     *
     */
    function &links() 
    {
        return CRM_Contact_Selector::$_links;
    } //end of function

    /**
     * getter for array of the parameters required for creating pager.
     *
     * @param 
     * @access public
     */
    function getPagerParams($action, &$params) 
    {
        $params['status']       = "Contact %%StatusMessage%%";
        $params['csvString']    = null;
        $params['rowCount']     = CRM_Utils_Pager::ROWCOUNT;

        $params['buttonTop']    = 'PagerTopButton';
        $params['buttonBottom'] = 'PagerBottomButton';
    }//end of function

    /**
     * returns the column headers as an array of tuples:
     * (name, sortName (key to the sort array))
     *
     * @param string $action the action being performed
     * @param enum   $output what should the result set include (web/email/csv)
     *
     * @return array the column headers that need to be displayed
     * @access public
     */
    function &getColumnHeaders($action = null, $output = null) 
    {
        if ( $output == CRM_Core_Selector_Controller::EXPORT || $output == CRM_Core_Selector_Controller::SCREEN ) {
            $csvHeaders = array( 'Contact Id', 'Contact Type' );
            foreach ( self::$_columnHeaders as $column ) {
                if ( array_key_exists( 'name', $column ) ) {
                    $csvHeaders[] = $column['name'];
                }
            }
            return $csvHeaders;
        } else {
            return self::$_columnHeaders;
        }
    }


    /**
     * Returns total number of rows for the query.
     *
     * @param 
     * @return int Total number of rows 
     * @access public
     */
    function getTotalCount($action)
    {
        return $this->_contact->advancedSearchQuery($this->_formValues, 0, 0, null, true);
    }


    /**
     * returns all the rows in the given offset and rowCount
     *
     * @param enum   $action   the action being performed
     * @param int    $offset   the row number to start from
     * @param int    $rowCount the number of rows to return
     * @param string $sort     the sql string that describes the sort order
     * @param enum   $output   what should the result set include (web/email/csv)
     *
     * @return int   the total number of rows for this action
     */
    function &getRows($action, $offset, $rowCount, $sort, $output = null) {
        $config = CRM_Core_Config::singleton( );

        if ( ( $output == CRM_Core_Selector_Controller::EXPORT || $output == CRM_Core_Selector_Controller::SCREEN ) &&
             $this->_formValues['radio_ts'] == 'ts_sel' ) {
            $includeContactIds = true;
        } else {
            $includeContactIds = false;
        }

        // note the formvalues were given by CRM_Contact_Form_Search to us 
        // and contain the search criteria (parameters)
        // note that the default mode is basic
        $result = $this->_contact->advancedSearchQuery($this->_formValues, $offset, $rowCount, $sort, false, $includeContactIds );

        // process the result of the query
        $rows = array( );

        while ($result->fetch()) {
            $row = array();

            // the columns we are interested in
            foreach (self::$_properties as $property) {
                $row[$property] = $result->$property;
            }

            if ( $output != CRM_Core_Selector_Controller::EXPORT && $output != CRM_Core_Selector_Controller::SCREEN ) {
                $row['checkbox'] = CRM_Core_Form::CB_PREFIX . $result->contact_id;
                $row['action'] = CRM_Core_Action::formLink( self::$_links, null, array( 'id' => $result->contact_id ) );
                $contact_type  = '<img src="' . $config->resourceBase . 'i/contact_';
                switch ($result->contact_type) {
                case 'Individual' :
                    $contact_type .= 'ind.png" alt="Individual">';
                    break;
                case 'Household' :
                    $contact_type .= 'house.png" alt="Household" height="16" width="16">';
                    break;
                case 'Organization' :
                    $contact_type .= 'org.gif" alt="Organization" height="16" width="18">';
                    break;
                    
                }
                $row['contact_type'] = $contact_type;
            }

            $rows[] = $row;
        }
        return $rows;
    }
    
    
    /**
     * Given the current formValues, gets the query in local
     * language
     *
     * @param array reference $formValues submitted formValues
     *
     * @return string string representing the query in local language
     * @access public
     */
  
    public static function getQILL(&$fv)
    {
        // query in local language
        $qill = "";
        $dontCare = " <i>dont care</i>";

        // regex patterns
        $patternOr = "/(.*) or$/";
        $patternAnd = "/(.*) and$/";
        $replacement = "$1";

        // check for contact type restriction
        $qill .= "<ul>";
        
        // contact type
        $qill .= "<li>Contact Type -";
        if ($fv['cb_contact_type']) {
            foreach ($fv['cb_contact_type']  as $k => $v) {
                $qill .= " {$k}s or";
            }            
            $qill = preg_replace($patternOr, $replacement, $qill);
        } else {
            $qill .= " All";
        }
        $qill .= "</li>";
        
        // check for group restriction
        if ($fv['cb_group']) {
            $group =& CRM_Core_PseudoConstant::group();
            $qill .= "<li>Belonging to Group -";
            foreach ($fv['cb_group']  as $k => $v) {
                $qill .= " \"" . $group[$k] . "\" or";
            }
            $qill = preg_replace($patternOr, $replacement, $qill);
            $qill .= "</li>";
        }
        
        // check for category restriction
        if ($fv['cb_category']) {
            $category =& CRM_Core_PseudoConstant::category();
            $qill .= "<li>Categorized as -";
            foreach ($fv['cb_category'] as $k => $v) {
                $qill .= " \"" . $category[$k] . "\" or";
            }
            $qill = preg_replace($patternOr, $replacement, $qill);
            $qill .= "</li>";
        }
        
        // check for last name, as of now only working with sort name
        if ($fv['sort_name']) {
            $qill .= "<li>Name like - \"" . $fv['sort_name'] . "\"</li>";
        }
        
        // street_name
        if ($fv['street_name']) {
            $qill .= "<li>Street Name like - \"" . $fv['street_name'] . "\"</li>";
        }
        
        // city_name
        if ($fv['city']) {
            $qill .= "<li>City Name like - \"" . $fv['city'] . "\"</li>";
        }
        
        // state
        if ($fv['state_province']) {
            $states =& CRM_Core_PseudoConstant::stateProvince();
            $qill .= "<li>State - \"" . $states[$fv['state_province']] . "\"</li>";
        }
        
        // country
        if ($fv['country']) {
            $country =& CRM_Core_PseudoConstant::country();
            $qill .= "<li>Country - \"" . $country[$fv['country']] . "\"</li>";
        }

        // postal code processing
        if ($fv['postal_code'] || $fv['postal_code_low'] || $fv['postal_code_high']) {
            $qill .= "<li>Postal code -";

            // postal code = value
            if ($fv['postal_code']) {
                $qill .= " \"" . $fv['postal_code'] . "\" or";
            }
                
            // postal code between 2 values
            if ($fv['postal_code_low'] && $fv['postal_code_high']) {
                $qill .= " between \"" . $fv['postal_code_low'] . "\" and \"" . $fv['postal_code_high'] . "\"";
            } elseif ($fv['postal_code_low']) {
                $qill .= " greater than \"" . $fv['postal_code_low'] . "\"";
            } elseif ($fv['postal_code_high']) {
                $qill .= " less than \"" . $fv['postal_code_high'] . "\"";
            }            
            // remove the trailing "or"
            $qill = preg_replace($patternOr, $replacement, $qill);
            $qill .= "</li>";
        }

        // location type processing
        if ($fv['cb_location_type']) {
            $locationType =& CRM_Core_PseudoConstant::locationType();        
            $qill .= "<li>Location type -";
            if (!$fv['cb_location_type']['any']) {
                foreach ($fv['cb_location_type']  as $k => $v) {
                    $qill .= " " . $locationType[$k] . " or";
                }
                $qill = preg_replace($patternOr, $replacement, $qill);
            } else {
                $qill .= " Any";
            }
            $qill .= "</li>";
        }
        
        // primary location processing
        if ($fv['cb_primary_location']) {
            $qill .= "<li>Primary Location only ? - Yes</li>";
        }
            
        // ending tag for unordered list
        $qill .= "</ul>";
        return $qill;
    }


    public function getMyQILL() {
        return self::getQILL($this->_formValues);
    }


    function getExportFileName( $output = 'csv') {
        return 'CiviCRM Contact Search';
    }

}//end of class

?>