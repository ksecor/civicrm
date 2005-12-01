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
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/


/**
 * This class is for displaying alphabetical bar
 *
 */


class CRM_Utils_PagerAToZ 
{

    /**
     * returns the alphabetic array for sorting by character
     *
     * @param array  $query           The query object
     * @param string $sortByCharacter The character that we are potentially sorting on
     *
     * @return string                 The html formatted string
     * @access public
     * @static
     */
    static function getAToZBar ( &$query, $sortByCharacter ) 
    {
        $AToZBar = self::createLinks( $query, $sortByCharacter );
        return $AToZBar;
    }
    
    /**
     * Function to return the all the static characters
     * 
     * @return array $staticAlphabets is a array of static characters
     * @access private
     * @static
     */
    
    static function getStaticCharacters () 
    {
        $staticAlphabets = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        return $staticAlphabets;
    }

    /**
     * Function to return the all the dynamic characters
     * 
     * @return array $dynamicAlphabets is a array of dynamic characters
     * @access private
     * @static
     */
    static function getDynamicCharacters (&$query) 
    {
        $result =& $query->searchQuery( null, null, null, false, false, true );
        $dynamicAlphabets = array( );
        while ($result->fetch()) { 
            $dynamicAlphabets[] = $result->sort_name;
        }

        return $dynamicAlphabets;
    }

    /**
     * create the links 
     *
     * @param array  $query          The form values for search
     * @param string $sortByCharacter The character that we are potentially sorting on
     *
     * @return array with links
     * @access private
     * @static
     */
    function createLinks ( &$query, $sortByCharacter ) 
    {
        $AToZBar          = self::getStaticCharacters();
        $dynamicAlphabets = self::getDynamicCharacters($query);

        $AToZBar = array_merge ( $AToZBar, $dynamicAlphabets );
        $AToZBar = array_unique( $AToZBar );
        
        //get the current path
        $path = CRM_Utils_System::currentPath() ;

        $aToZBar = array( );
        foreach ( $AToZBar as $key => $link ) {
            if ( ! $link ) {
                continue;
            }

            $element = array( );
            if ( in_array( $link, $dynamicAlphabets ) ) {
                $klass = '';
                if ( $link == $sortByCharacter ) {
                    $element['class'] = "active";
                    $klass = 'class="active"';
                }
                $element['item']  = sprintf('<a href="%s" %s>%s</a>',
                                            CRM_Utils_System::url( $path, "q=$path&force=1&sortByCharacter=$link" ),
                                            $klass,
                                            $link );
            } else {
                $element['item'] = $link;
            }
            $aToZBar[] = $element;
        }
        
        $url = sprintf('<a href="%s">%s</a>',
                       CRM_Utils_System::url( $path, "q=$path&force=1&sortByCharacter=1" ),
                       'All' );
        $aToZBar[] = array( 'item' => $url );
        return $aToZBar;
    }
    
}

?>
