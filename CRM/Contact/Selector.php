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
require_once 'CRM/Core/Pager.php';
require_once 'CRM/Core/Sort.php';
require_once 'CRM/Core/Selector/Base.php';
require_once 'CRM/Core/Selector/API.php';

require_once 'CRM/Contact/BAO/Contact.php';


/**
 * This class is used to retrieve and display a range of
 * contacts that match the given criteria (specifically for
 * results of advanced search options.
 *
 */
class CRM_Contact_Selector extends CRM_Selector_Base implements CRM_Selector_API 
{

    /**
     * This defines two actions- View and Edit.
     *
     * @var array
     */
    static $_links = array(
                           CRM_Action::VIEW   => array(
                                                       'name'     => 'View',
                                                       'url'      => '/civicrm/contact/view',
                                                       'qs'       => 'reset=1&cid=%%id%%',
                                                       'title'    => 'View Contact Details',
                                                       ),
                           CRM_Action::UPDATE => array(
                                                       'name'     => 'Edit',
                                                       'url'      => '/civicrm/contact/edit',
                                                       'qs'       => 'reset=1&cid=%%id%%',
                                                       'menuName' => 'Edit Contact Details',
                                                       ),
                           );

    static $_columnHeaders = array(
                                   array('name' => ''),
                                   array('name' => ''),
                                   array(
                                         'name'      => 'Name',
                                             'sort'      => 'sort_name',
                                             'direction' => CRM_Sort::ASCENDING,
                                             ),
                                       array('name' => 'Address'),
                                       array(
                                             'name'      => 'City',
                                             'sort'      => 'city',
                                             'direction' => CRM_Sort::DONTCARE,
                                             ),
                                       array(
                                             'name'      => 'State',
                                             'sort'      => 'state',
                                             'direction' => CRM_Sort::DONTCARE,
                                             ),
                                       array(
                                             'name'      => 'Postal',
                                             'sort'      => 'postal_code',
                                             'direction' => CRM_Sort::DONTCARE,
                                             ),
                                       array(
                                             'name'      => 'Country',
                                             'sort'      => 'country',
                                             'direction' => CRM_Sort::DONTCARE,
                                             ),
                                       array(
                                             'name'      => 'Email',
                                             'sort'      => 'email',
                                             'direction' => CRM_Sort::DONTCARE,
                                             ),
                                       array('name' => 'Phone'),
                                       array('name' => ''),
                                       );
    


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
    protected $_type;


    /**
     * Class constructor
     *
     * @param array $formValues array of parameters for query
     * @param int   $type - type of search basic or advanced.
     *
     * @return CRM_Contact_AdvancedSelector
     * @access public
     */
    function __construct(&$formValues, $type=CRM_Form::MODE_NONE) 
    {
        //object of BAO_Contact_Individual for fetching the records from db
        $this->_contact = new CRM_Contact_BAO_Contact();

        // submitted form values
        $this->_formValues =& $formValues;

        // type of selector
        $this->_type = $type;

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
        $params['rowCount']     = CRM_Pager::ROWCOUNT;

        $params['buttonTop']    = 'PagerTopButton';
        $params['buttonBottom'] = 'PagerBottomButton';
    }//end of function

    /**
     * getter for headers for each column of the displayed form.
     *
     * @param 
     * @return array (reference)
     * @access public
     */
    function &getColumnHeaders($action = null) 
    {
        return self::$_columnHeaders;
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
        switch ($this->_type) {
        case CRM_Form::MODE_BASIC:
            return $this->_contact->basicSearchQuery($this->_formValues, 0, 0, null, TRUE);
        case CRM_Form::MODE_ADVANCED:
            return $this->_contact->advancedSearchQuery($this->_formValues, 0, 0, null, TRUE);
        }
        return 0;
    }


    /**
     * getter for all the database values to be displayed on the form while listing
     *
     * @param int      $action   the type of action links
     * @param int      $offset   the offset for the query
     * @param int      $rowCount the number of rows to return
     * @param CRM_Sort $sort     the sort object
     *
     * @return array (reference)
     * @access public
     */
    function &getRows($action, $offset, $rowCount, $sort)
    {
        static $properties = array( 'contact_id', 'sort_name', 'street_address',
                                    'city', 'state', 'country', 'postal_code',
                                    'email', 'phone' );
        
        $config = CRM_Config::singleton( );
        
        // note the formvalues were given by CRM_Contact_Form_Search to us 
        // and contain the search criteria (parameters)
        switch ($this->_type) {
        case CRM_Form::MODE_BASIC:
            $result = $this->_contact->basicSearchQuery($this->_formValues, $offset, $rowCount, $sort);
            break;
        case CRM_Form::MODE_ADVANCED:
            $result = $this->_contact->advancedSearchQuery($this->_formValues, $offset, $rowCount, $sort);
            break;
        }

        // process the result of the query
        $rows = array( );

        while ($result->fetch()) {
            $row = array();

            $row['checkbox'] = CRM_Form::CB_PREFIX . $result->contact_id;

            // the columns we are interested in
            foreach ($properties as $property) {
                $row[$property] = $result->$property;
            }
            $row['action'] = CRM_Action::formLink( self::$_links, null, array( 'id' => $result->contact_id ) );
            $row['edit']   = CRM_System::url( 'civicrm/contact/edit', 'reset=1&cid=' . $result->contact_id );
            $row['view']   = CRM_System::url( 'civicrm/contact/view', 'reset=1&cid=' . $result->contact_id );
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
            $rows[] = $row;
        }
        return $rows;
    }
    
    
    /**
     * Given the current formValues, gets the query in local
     * language
     *
     * @param array reference $formValues submitted formValues
     * @param int $type the type of form
     *
     * @return string string representing the query in local language
     * @access public
     */
  
    public static function getQILL(&$fv, $type)
    {
        // query in local language
        $qill = "";
        $dontCare = " <i>dont care</i>";

        // regex patterns
        $patternOr = "/(.*) or$/";
        $patternAnd = "/(.*) and$/";
        $replacement = "$1";

        switch ($type) {
        case CRM_Form::MODE_BASIC:
            $qill .= " all";
            if ($fv['contact_type'] && ($fv['contact_type'] != 'any')) {
                $qill .= " " . $fv['contact_type'] . "s";
            } else {
                $qill .= " contacts";
            }

            // check for group restriction
            if ($fv['group'] && ($fv['group'] != 'any')) {
                CRM_PseudoConstant::populateGroup();
                $qill .= " belonging to the group \"" . CRM_PseudoConstant::$group[$fv['group']] . "\" and";
            }
            
            // check for category restriction
            if ($fv['category'] && ($fv['category'] != 'any')) {
                CRM_PseudoConstant::populateCategory();
                $qill .= " categorized as \"" . CRM_PseudoConstant::$category[$fv['category']] . "\" and";
            }
            
            // check for last name, as of now only working with sort name
            if ($fv['sort_name']) {
                $andArray['sort_name'] = " LOWER(crm_contact.sort_name) LIKE '%". strtolower(addslashes($fv['sort_name'])) ."%'";
                $qill .= " whose name is like \"" . $fv['sort_name'] . "\" and";
            }
            $qill = preg_replace($patternAnd, $replacement, $qill);
            break;

        case CRM_Form::MODE_ADVANCED:
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
                CRM_PseudoConstant::populateGroup();
                $qill .= "<li>Belonging to Group -";
                foreach ($fv['cb_group']  as $k => $v) {
                    $qill .= " \"" . CRM_PseudoConstant::$group[$k] . "\" or";
                }
                $qill = preg_replace($patternOr, $replacement, $qill);
                $qill .= "</li>";
            }

            // check for category restriction
            if ($fv['cb_category']) {
                CRM_PseudoConstant::populateCategory();
                $qill .= "<li>Categorized as -";
                foreach ($fv['cb_category'] as $k => $v) {
                    $qill .= " \"" . CRM_PseudoConstant::$category[$k] . "\" or";
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
                CRM_PseudoConstant::populateStateProvince();
                $qill .= "<li>State - \"" . CRM_PseudoConstant::$stateProvince[$fv['state_province']] . "\"</li>";
            }
            
            // country
            if ($fv['country']) {
                CRM_PseudoConstant::populateCountry();
                $qill .= "<li>Country - \"" . CRM_PseudoConstant::$country[$fv['country']] . "\"</li>";
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
                CRM_PseudoConstant::populateLocationType();        
                $qill .= "<li>Location type -";
                if (!$fv['cb_location_type']['any']) {
                    foreach ($fv['cb_location_type']  as $k => $v) {
                        $qill .= " " . CRM_PseudoConstant::$locationType[$k] . " or";
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
            break;
        }
        return $qill;
    }


    public function getMyQILL() {
        return self::getQILL($this->_formValues, $this->_type);
    }


    function getExportColumnHeaders($action, $type = 'csv')
    {
    }

    function getExportRows($action, $type = 'csv')
    {
    }

    function getExportFileName($action, $type = 'csv')
    {
    }


}//end of class

?>