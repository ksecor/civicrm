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
 * $Id: Selector.php 1204 2005-05-27 19:32:55Z lobo $
 *
 */

$GLOBALS['_CRM_HISTORY_SELECTOR']['_actionLinks'] = '';
$GLOBALS['_CRM_HISTORY_SELECTOR']['_columnHeaders'] = '';
$GLOBALS['_CRM_HISTORY_SELECTOR']['_properties'] =  array('activity_type', 'activity_summary', 'activity_date');

require_once 'CRM/Core/Selector/Base.php';
require_once 'CRM/Core/BAO/History.php';
require_once 'CRM/Core/Config.php';
require_once 'CRM/Core/Error.php';
require_once 'CRM/Core/PseudoConstant.php';
require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/Selector/Base.php';
require_once 'CRM/Core/Selector/API.php';

require_once 'CRM/Utils/Pager.php';
require_once 'CRM/Utils/Sort.php';

require_once 'CRM/Contact/BAO/Contact.php';


/**
 * This class is used to retrieve and display history.
 *
 */
class CRM_History_Selector extends CRM_Core_Selector_Base {
    /**
     * This defines two actions- View and Edit.
     *
     * @var array
     * @static
     */
    

    /**
     * we use desc to remind us what that column is, name is used in the tpl
     *
     * @var array
     * @static
     */
    

    /**
     * Properties of contact we're interested in displaying
     * @var array
     * @static
     */
    

    /**
     * contactId - contact id of contact whose history are displayed
     *
     * @var int
     * @access protected
     */
    var $_history;

    /**
     * Class constructor
     *
     * @param int $contactId - contact whose history we want to display
     *
     * @return CRM_History_Selector
     * @access public
     */
    function CRM_History_Selector($contactId) 
    {
        $this->_contactId = $contactId;
    }


    /**
     * This method returns the action links that are given for each search row.
     * currently the action links added for each row are 
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
     function &actionLinks() 
    {

        if (!isset($GLOBALS['_CRM_HISTORY_SELECTOR']['_actionLinks'])) {
            $GLOBALS['_CRM_HISTORY_SELECTOR']['_actionLinks'] = array(
                                  CRM_CORE_ACTION_VIEW   => array(
                                                                   'name'     => ts('View'),
                                                                   'url'      => 'civicrm/contact/view',
                                                                   'qs'       => 'reset=1&cid=%%id%%',
                                                                   'title'    => ts('View Contact Details'),
                                                                  ),
                                  CRM_CORE_ACTION_UPDATE => array(
                                                                   'name'     => ts('Edit'),
                                                                   'url'      => 'civicrm/contact/edit',
                                                                   'qs'       => 'reset=1&cid=%%id%%',
                                                                   'title'    => ts('Edit Contact Details'),
                                                                  ),
                                 );
        }
        return $GLOBALS['_CRM_HISTORY_SELECTOR']['_actionLinks'];
    }


    /**
     * getter for array of the parameters required for creating pager.
     *
     * @param 
     * @access public
     */
    function getPagerParams($action, &$params) 
    {
        $params['status']       = "History %%StatusMessage%%";
        $params['csvString']    = null;
        $params['rowCount']     = CRM_UTILS_PAGER_ROWCOUNT;

        $params['buttonTop']    = 'PagerTopButton';
        $params['buttonBottom'] = 'PagerBottomButton';
    }


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
        if ($output==CRM_CORE_SELECTOR_CONTROLLER_EXPORT || $output==CRM_CORE_SELECTOR_CONTROLLER_SCREEN) {
            $csvHeaders = array( 'Activity Type', 'Description', 'Activity Date');
            foreach (CRM_History_Selector::_getColumnHeaders() as $column ) {
                if (array_key_exists( 'name', $column ) ) {
                    $csvHeaders[] = $column['name'];
                }
            }
            return $csvHeaders;
        } else {
            return CRM_History_Selector::_getColumnHeaders();
        }
    }


    /**
     * Returns total number of rows for the query.
     *
     * @param string $action - action being performed
     * @return int Total number of rows 
     * @access public
     */
    function getTotalCount($action)
    {
        return CRM_Core_BAO_History::getNumHistory($this->_contactId);
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
        $config = CRM_Core_Config::singleton();

        $rows = CRM_Core_BAO_History::getHistory($this->_contactId, $offset, $rowCount, $sort);

        CRM_Core_Error::debug_var('rows', $rows);

//         while ($result->fetch()) {
//             $row = array();

//             // the columns we are interested in
//             foreach (self::$_properties as $property) {
//                 $row[$property] = $result->$property;
//             }

//             if ( $output != CRM_Core_Selector_Controller::EXPORT && $output != CRM_Core_Selector_Controller::SCREEN ) {
//                 $row['checkbox'] = CRM_Core_Form::CB_PREFIX . $result->contact_id;
//                 $row['action'] = CRM_Core_Action::formLink( self::actionLinks(), null, array( 'id' => $result->contact_id ) );
//                 $contact_type  = '<img src="' . $config->resourceBase . 'i/contact_';
//                 switch ($result->contact_type) {
//                 case 'Individual' :
//                     $contact_type .= 'ind.gif" alt="Individual">';
//                     break;
//                 case 'Household' :
//                     $contact_type .= 'house.png" alt="Household" height="16" width="16">';
//                     break;
//                 case 'Organization' :
//                     $contact_type .= 'org.gif" alt="Organization" height="16" width="18">';
//                     break;
                    
//                 }
//                 $row['contact_type'] = $contact_type;
//             }

//             $rows[] = $row;
//         }
        return $rows;
    }
    
    
    /**
     * Given the current formValues, gets the query in local
     * language
     *
     * @param array reference $formValues submitted formValues
     *
     * @return array $qill which contains an array of strings
     * @access public
     */
  
      function getQILL(&$fv)
    {
        // query in local language
        $qill = array( );
        $dontCare = ' dont care';

        // regex patterns
        $patternOr = "/(.*) or$/";
        $patternAnd = "/(.*) and$/";
        $replacement = "$1";

        // check for last name, as of now only working with sort name
        if ($fv['sort_name']) {
            $qill[] = 'Name like - "' . $fv['sort_name'] . '"';
        }
        
        // contact type
        $str = 'Contact Type -';
        if ($fv['cb_contact_type']) {
            foreach ($fv['cb_contact_type']  as $k => $v) {
                $str .= " {$k}s or";
            }            
            $str = preg_replace($patternOr, $replacement, $str);
        } else {
            $str .= ' All';
        }
        $qill[] = $str;
        
        // check for group restriction
        if ($fv['cb_group']) {
            $group =& CRM_Core_PseudoConstant::group();
            $str = 'Member of Group -';
            foreach ($fv['cb_group']  as $k => $v) {
                $str .= ' "' . $group[$k] . '" or';
            }
            $str = preg_replace($patternOr, $replacement, $str);
            $qill[] = $str;
        }
        
        // check for tag restriction
        if ($fv['cb_tag']) {
            $tag =& CRM_Core_PseudoConstant::tag();
            $str = 'Tagged as -';
            foreach ($fv['cb_tag'] as $k => $v) {
                $str .= ' "' . $tag[$k] . '" or';
            }
            $str = preg_replace($patternOr, $replacement, $str);
            $qill[] = $str;
        }
        
        // street_name
        if ($fv['street_name']) {
            $qill[] = 'Street Name like - "' . $fv['street_name'] . '"';
        }
        
        // city_name
        if ($fv['city']) {
            $qill[] = 'City Name like - "' . $fv['city'] . '"';
        }
        
        // state
        if ($fv['state_province']) {
            $states =& CRM_Core_PseudoConstant::stateProvince();
            $qill[] = 'State - "' . $states[$fv['state_province']] . '"';
        }
        
        // country
        if ($fv['country']) {
            $country =& CRM_Core_PseudoConstant::country();
            $qill[] = 'Country - "' . $country[$fv['country']] . '"';
        }

        // postal code processing
        if ($fv['postal_code'] || $fv['postal_code_low'] || $fv['postal_code_high']) {
            $str = 'Postal code -';

            // postal code = value
            if ($fv['postal_code']) {
                $str .= ' "' . $fv['postal_code'] . '" or';
            }
                
            // postal code between 2 values
            if ($fv['postal_code_low'] && $fv['postal_code_high']) {
                $str .= ' between "' . $fv['postal_code_low'] . '" and "' . $fv['postal_code_high'] . '"';
            } elseif ($fv['postal_code_low']) {
                $str .= ' greater than "' . $fv['postal_code_low'] . '"';
            } elseif ($fv['postal_code_high']) {
                $str .= ' less than "' . $fv['postal_code_high'] . '"';
            }            
            // remove the trailing "or"
            $str    = preg_replace($patternOr, $replacement, $str);
            $qill[] = $str;
        }

        // location type processing
        if ($fv['cb_location_type']) {
            $locationType =& CRM_Core_PseudoConstant::locationType();        
            $str = 'Location type -';
            if (!$fv['cb_location_type']['any']) {
                foreach ($fv['cb_location_type']  as $k => $v) {
                    $str .= ' ' . $locationType[$k] . ' or';
                }
                $str = preg_replace($patternOr, $replacement, $str);
            } else {
                $str .= ' Any';
            }
            $qill[] = $str;
        }
        
        // primary location processing
        if ($fv['cb_primary_location']) {
            $qill[] = 'Primary Location only ? - Yes';
        }
            
        return $qill;
    }

    
    /**
     * Wrapper function for getting Qill.
     *
     * Calls the static function getQILL to get query in local language
     *
     * @param none
     * @return contents of static function.
     * @access public
     */
     function getMyQILL() {
        return CRM_History_Selector::getQILL($this->_formValues);
    }


    /**
     * name of export file.
     *
     * @param string $output type of output
     * @return string name of the file
     */
    function getExportFileName( $output = 'csv') {
        return 'CiviCRM Contact Search';
    }

    /**
     * get colunmn headers for search selector
     *
     *
     * @param none
     * @return array $_columnHeaders
     * @access private
     */
      function &_getColumnHeaders() 
    {
        if ( ! isset( $GLOBALS['_CRM_HISTORY_SELECTOR']['_columnHeaders'] ) )
        {
            $GLOBALS['_CRM_HISTORY_SELECTOR']['_columnHeaders'] = array(
                                          array('desc' => ts('Select') ),
                                          array('desc' => ts('Contact Type') ),
                                          array(
                                                'name'      => ts('Name'),
                                                'sort'      => 'sort_name',
                                                'direction' => CRM_UTILS_SORT_ASCENDING,
                                         ),
                                    array('name' => ts('Address') ),
                                    array(
                                          'name'      => ts('City'),
                                          'sort'      => 'city',
                                          'direction' => CRM_UTILS_SORT_DONTCARE,
                                         ),
                                    array(
                                          'name'      => ts('State'),
                                          'sort'      => 'state',
                                          'direction' => CRM_UTILS_SORT_DONTCARE,
                                         ),
                                    array(
                                          'name'      => ts('Postal'),
                                          'sort'      => 'postal_code',
                                          'direction' => CRM_UTILS_SORT_DONTCARE,
                                         ),
                                    array(
                                          'name'      => ts('Country'),
                                          'sort'      => 'country',
                                          'direction' => CRM_UTILS_SORT_DONTCARE,
                                         ),
                                    array(
                                          'name'      => ts('Email'),
                                          'sort'      => 'email',
                                          'direction' => CRM_UTILS_SORT_DONTCARE,
                                         ),
                                    array('name' => ts('Phone') ),
                                    array('desc' => ts('Actions') ),
                                    );
        }
        return $GLOBALS['_CRM_HISTORY_SELECTOR']['_columnHeaders'];
    }
}
?>