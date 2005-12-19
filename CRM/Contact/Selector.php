<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/Selector/Base.php';
require_once 'CRM/Core/Selector/API.php';

require_once 'CRM/Utils/Pager.php';
require_once 'CRM/Utils/Sort.php';

require_once 'CRM/Contact/BAO/Contact.php';
require_once 'CRM/Contact/BAO/Query.php';

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
     * @static
     */
    static $_links = null;

    /**
     * we use desc to remind us what that column is, name is used in the tpl
     *
     * @var array
     * @static
     */
    static $_columnHeaders;

    /**
     * Properties of contact we're interested in displaying
     * @var array
     * @static
     */
    static $_properties = array('contact_id', 'contact_type', 'sort_name', 'street_address',
                                'city', 'state_province', 'postal_code', 'country',
                                'email', 'phone', 'status' );

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
    public $_formValues;

    /**
     * represent the type of selector
     *
     * @var int
     * @access protected
     */
    protected $_action;

    protected $_query;

    /**
     * Class constructor
     *
     * @param array $formValues array of parameters for query
     * @param int   $action - action of search basic or advanced.
     *
     * @return CRM_Contact_Selector
     * @access public
     */
    function __construct(&$formValues, $action = CRM_Core_Action::NONE) 
    {
        //object of BAO_Contact_Individual for fetching the records from db
        $this->_contact =& new CRM_Contact_BAO_Contact();

        // submitted form values
        $this->_formValues =& $formValues;

        // type of selector
        $this->_action = $action;

        $this->_query =& new CRM_Contact_BAO_Query( $this->_formValues );
    }//end of constructor


    /**
     * This method returns the links that are given for each search row.
     * currently the links added for each row are 
     * 
     * - View
     * - Edit
     *
     * @return array
     * @access public
     *
     */
    static function &links()
    {

        if (!(self::$_links)) {
            self::$_links = array(
                                  CRM_Core_Action::VIEW   => array(
                                                                   'name'     => ts('View'),
                                                                   'url'      => 'civicrm/contact/view',
                                                                   'qs'       => 'reset=1&cid=%%id%%',
                                                                   'title'    => ts('View Contact Details'),
                                                                  ),
                                  CRM_Core_Action::UPDATE => array(
                                                                   'name'     => ts('Edit'),
                                                                   'url'      => 'civicrm/contact/view',
                                                                   'qs'       => 'reset=1&action=update&cid=%%id%%',
                                                                   'title'    => ts('Edit Contact Details'),
                                                                  ),
                                  );

            $config = CRM_Core_Config::singleton( );
            if ( $config->mapAPIKey && $config->mapProvider) {
                self::$_links[CRM_Core_Action::MAP] = array(
                                                            'name'     => ts('Map'),
                                                            'url'      => 'civicrm/contact/search/map',
                                                            'qs'       => 'reset=1&cid=%%id%%',
                                                            'title'    => ts('Map Contact'),
                                                            );
            }
        }
        return self::$_links;
    } //end of function


    /**
     * getter for array of the parameters required for creating pager.
     *
     * @param 
     * @access public
     */
    function getPagerParams($action, &$params) 
    {
        $params['status']       = ts('Contact %%StatusMessage%%');
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
            $csvHeaders = array( ts('Contact Id'), ts('Contact Type') );
            foreach ( self::_getColumnHeaders() as $column ) {
                if ( array_key_exists( 'name', $column ) ) {
                    $csvHeaders[] = $column['name'];
                }
            }
            return $csvHeaders;
        } else {
            return self::_getColumnHeaders();
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
        return $this->_query->searchQuery( 0, 0, null, true );
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
        $config =& CRM_Core_Config::singleton( );

        if ( ( $output == CRM_Core_Selector_Controller::EXPORT || $output == CRM_Core_Selector_Controller::SCREEN ) &&
             $this->_formValues['radio_ts'] == 'ts_sel' ) {
            $includeContactIds = true;
        } else {
            $includeContactIds = false;
        }

        // note the formvalues were given by CRM_Contact_Form_Search to us 
        // and contain the search criteria (parameters)
        // note that the default action is basic
        $result = $this->_query->searchQuery($offset, $rowCount, $sort,
                                             false, $includeContactIds );

        // process the result of the query
        $rows = array( );

        $mask = CRM_Core_Action::mask( CRM_Core_Permission::getPermission( ) );
        
        $mapMask = 4095; // mask value to hide map link if there are not lat/long

        $gc = CRM_Core_SelectValues::groupContactStatus();

        /* Dirty session hack to get at the context */
        $session =& CRM_Core_Session::singleton();
        $context = $session->get('context', 'CRM_Contact_Controller_Search');

        // CRM_Core_Error::debug( 'p', self::$_properties );

        while ($result->fetch()) {
            $row = array();

            // the columns we are interested in
            foreach (self::$_properties as $property) {
                if ( $property == 'status' ) {
                    continue;
                }
                $row[$property] = $result->$property;
            }

            if (!empty ($result->postal_code_suffix)) {
                $row['postal_code'] .= "-" . $result->postal_code_suffix;
            }
            
            
            if ($output != CRM_Core_Selector_Controller::EXPORT ||
                $context == 'smog') {
                if (empty($result->status)) {
                    $row['status'] = ts('Smart');
                } else {
                    $row['status'] = $gc[$result->status];
                }
            }
            
            if ( $output != CRM_Core_Selector_Controller::EXPORT && $output != CRM_Core_Selector_Controller::SCREEN ) {
                $row['checkbox'] = CRM_Core_Form::CB_PREFIX . $result->contact_id;
                
                $locations = array();
                require_once 'CRM/Contact/BAO/Contact.php';
                $locations =& CRM_Contact_BAO_Contact::getMapInfo( array($result->contact_id) );

                if ( empty( $locations ) ) {
                    $row['action']   = CRM_Core_Action::formLink( self::links(), $mapMask, array( 'id' => $result->contact_id ) );
                } else {
                    $row['action']   = CRM_Core_Action::formLink( self::links(), $mask, array( 'id' => $result->contact_id ) );
                }
                
                $contact_type    = '<img src="' . $config->resourceBase . 'i/contact_';
                switch ($result->contact_type) {
                case 'Individual' :
                    $contact_type .= 'ind.gif" alt="' . ts('Individual') . '" />';
                    break;
                case 'Household' :
                    $contact_type .= 'house.png" alt="' . ts('Household') . '" height="16" width="16" />';
                    break;
                case 'Organization' :
                    $contact_type .= 'org.gif" alt="' . ts('Organization') . '" height="16" width="18" />';
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
     * @param  array(reference)   $formValues   submitted formValues
     *
     * @return array              $qill         which contains an array of strings
     * @access public
     */
  
    // the current internationalisation is bad, but should more or less work
    // for most of "European" languages
    public function getQILL( )
    {
        return $this->_query->qill( );
    }

    /**
     * name of export file.
     *
     * @param string $output type of output
     * @return string name of the file
     */
    function getExportFileName( $output = 'csv') {
        return ts('CiviCRM Contact Search');
    }

    /**
     * get colunmn headers for search selector
     *
     *
     * @return array $_columnHeaders
     * @access private
     */
    private static function &_getColumnHeaders() 
    {
        if ( ! isset( self::$_columnHeaders ) )
        {
            self::$_columnHeaders = array(
                                          array('desc' => ts('Contact Type') ),
                                          array(
                                                'name'      => ts('Name'),
                                                'sort'      => 'sort_name',
                                                'direction' => CRM_Utils_Sort::ASCENDING,
                                                ),
                                          array('name' => ts('Address') ),
                                          array(
                                                'name'      => ts('City'),
                                                'sort'      => 'city',
                                                'direction' => CRM_Utils_Sort::DONTCARE,
                                                ),
                                          array(
                                                'name'      => ts('State'),
                                                'sort'      => 'state_province',
                                                'direction' => CRM_Utils_Sort::DONTCARE,
                                                ),
                                          array(
                                                'name'      => ts('Postal'),
                                                'sort'      => 'postal_code',
                                                'direction' => CRM_Utils_Sort::DONTCARE,
                                                ),
                                          array(
                                                'name'      => ts('Country'),
                                                'sort'      => 'country',
                                              'direction' => CRM_Utils_Sort::DONTCARE,
                                                ),
                                          array(
                                                'name'      => ts('Email'),
                                                'sort'      => 'email',
                                                'direction' => CRM_Utils_Sort::DONTCARE,
                                                ),
                                          array('name' => ts('Phone') ),
                                          array('desc' => ts('Actions') ),
                                          );
        }
        return self::$_columnHeaders;
    }
    
    function &getQuery( ) {
        return $this->_query;
    }

}//end of class

?>
