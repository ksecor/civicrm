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
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

//require_once 'CRM/Core/Error.php'; 
//require_once 'CRM/Core/DAO.php'; 
//require_once 'CRM/Core/PseudoConstant.php'; 

/**
 * This class is for search widget using JPSpan.
 *
 */
class CRM_Contact_Server_Search
{
    /**
     * This function is to get the contact name / email  based on the search criteria
     * @param string $fragment this is the search string
     *
     * @return contact name / email  depending on search criteria
     * @access public
     */
    function getSearchResult($fragment='') 
    {
        $fraglen = strlen($fragment);
        $searchValues = array();
        $searchRows = array();
        $searchValues['sort_name'] = $fragment;         
        
        require_once 'CRM/Contact/BAO/Query.php';
        $contactBAO  =& new CRM_Contact_BAO_Query($searchValues);

        $searchResult = $contactBAO->searchQuery(0, 50, null, false );
        while($searchResult->fetch()) {
            $searchRows[] = $searchResult->sort_name;    
        }
        
        for ( $i = $fraglen; $i > 0; $i-- ) {
            $matches = preg_grep('/^'.substr($fragment,0,$i).'/i', $searchRows);
            
            if ( count($matches) > 0 ) {
                return array_shift($matches);
            }
        }
        return '';
    }
}
?>
