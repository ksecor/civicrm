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
 * This class is for displaying alphabetical bar
 *
 */


class CRM_Utils_PagerAToZ 
{

    function __construct( ) 
    {
    }
    
    function getAToZBar ( &$params ) 
    {
        $AToZBar = self::createLinks($params);
        return $AToZBar;
    }
    
    /**
     * Function to return the all the static characters
     * 
     * @return array $staticAlphabets is a array of static characters
     * @access public
     */
    
    function getStaticCharacters () 
    {
        $staticAlphabets = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        return $staticAlphabets;
    }

    /**
     * Function to return the all the dynamic characters
     * 
     * @return array $dynamicAlphabets is a array of dynamic characters
     * @access public
     */
    function getDynamicCharacters (&$params) 
    {
        $contact =& new CRM_Contact_BAO_Contact();
        $result = $contact->searchQuery($params, null, null, null, false, null, true);
        while ($result->fetch()) { 
            $dynamicAlphabets[] = $result->sort_name;
        }

        return $dynamicAlphabets;
    }

    /**
     * create the links 
     *
     * @param $linkCharacters  array of alphabets whose link has to created
     * @param $allCharacters  array of alphabets whose link has to created
     * 
     * @return array with links
     */
    function createLinks (&$params) 
    {
        $AToZBar = self::getStaticCharacters();
        $dynamicAlphabets = self::getDynamicCharacters($params);

        if (is_array($dynamicAlphabets)) {
            $AToZBar = array_merge($AToZBar, $dynamicAlphabets);
            $AToZBar = array_unique($AToZBar);
        }
        
        //get the current path
        $path = CRM_Utils_System::currentPath() ;

        foreach ( $AToZBar as $key => $link ) {
            if (!$link) {
                continue;
            }
            if (in_array ($link, $dynamicAlphabets)) {
                $url[] = sprintf('<a href="%s" >%s</a>', CRM_Utils_System::url( $path, 'q='.$path.'&force=1&sortByCharacter='.$link), $link );
            } else {
                $url[] = $link;
            }
        }
        
        $url[] = sprintf('<a href="%s" >%s</a>', CRM_Utils_System::url( $path, 'q='.$path.'&force=1&sortByCharacter='), 'All' );
        
        return $url;
    }
    
}

?>
