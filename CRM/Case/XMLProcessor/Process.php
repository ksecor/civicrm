<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2008
 * $Id$
 *
 */

class CRM_Case_XMLProcessor_Process {

    function run( $caseType,
                  &$params ) {
        $xml = $this->getXML( $caseType );

        if ( $xml === false ) {
            return false;
        }

        $this->process( $xml, $params );
    }

    function retrieve( $caseType ) {
        // trim all spaces from $caseType
        $caseType = CRM_Utils_String::munge( $caseType, '', 0 );

        // ensure that the file exists
        $fileName = implode( DIRECTORY_SEPARATOR,
                             array( dirname( __FILE__ ),
                                    'xml',
                                    'configuration',
                                    "$caseType.xml" ) );
        if ( ! file_exists( $fileName ) ) {
            return false;
        }

        // read xml file
        $dom = DomDocument::load( $fileName );
        $dom->xinclude( );
        $xml = simplexml_import_dom( $dom );
        
        return $xml;
    }

    function process( $xml,
                      &$params ) {
        $standardTimeLine = CRM_Utils_Array::value( 'standardTimeLine', $params );
        $activitySetName  = CRM_Utils_Array::value( 'activitySetName' , $params );
        $offsetDate       = CRM_Utils_Array::value( 'offsetDate'      , $params );

        if ( ! $offsetDate ) {
            $offsetDate = time( );
        }

        foreach ( $xml->ActivitySets as $activitySetsXML ) {
            foreach ( $activitySetsXML->ActivitySet as $activitySetXML ) {
                if ( $standardTimeLine ) {
                    $timeline = (boolean ) $activitySetXML->timeline;
                    if ( $timeline ) {
                        return $this->processStandardTimeLine( $activitySetXML );
                    }
                } else if ( $activitySetName ) {
                    $name = (string ) $activitySetXML->keyname;
                    if ( $name == $activitySetName ) {
                        return $this->processActivitySetReport( $activitySetXML );
                    }
                }
            }
        }
    }

    function processStandardTimeLine( $activitySetXML ) {
        
    }

    function createRelationships( $relationshipTypeXML ) {
    }

}
