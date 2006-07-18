<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
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
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

/** 
 *  this file contains functions for Household
 */


require_once 'CRM/Quest/DAO/Student.php';
require_once 'CRM/Quest/DAO/Essay.php';

class CRM_Quest_BAO_Essay extends CRM_Quest_DAO_Essay {
    
    /**
     * class constructor
     */
    function __construct( ) {
        parent::__construct( );
    }

    /**
     * function to add/update Essay Information
     *
     * @param array $params reference array contains the values submitted by the form
     * @param array $ids    reference array contains the id
     * 
     * @access public
     * @static 
     * @return object
     */
    static function add(&$params, &$ids) {
        $dao = & new CRM_Quest_DAO_Essay();
        $dao->copyValues($params);
        if( $ids['id'] ) {
            $dao->id = $ids['id'];
        }
        $essay = $dao->save();
        return $essay;
                        
    }

    /**
     * function to create/build essay-params and do the respective changes in database.
     *
     * @param array  $essays      the essays field reference
     * @param array  $params    reference array contains the values submitted by the form
     * @param int    $sourceID    person who wrote the essay
     * @param int    $targetID    person for whom the essay is
     * 
     * @access public
     * @static 
     * @return void
     */
    static function create( &$essays, &$params, $sourceID, $targetID ) {
        foreach ( $essays as $name => $essay ) {
            if ($params[$name]) {
                $essayParams = array( );
                $ids         = array( );

                $essayParams['source_contact_id'] = $sourceID;
                $essayParams['target_contact_id'] = $targetID;
                $essayParams['essay_type_id']     = $essay['typeID'];
                $essayParams['essay']             = $params[$name];
                if ( $essay['essayID'] ) {
                    $ids['id'] = $essay['essayID'];
                }
                CRM_Quest_BAO_Essay::add( $essayParams, $ids);
            }
        }
    }

    /**
     * function to return essay fields for a particular grouping.
     *
     * @param string $grouping    contains the grouping-name for the essay type
     * @param int    $sourceID    person who wrote the essay
     * @param int    $targetID    person for whom the essay is
     * 
     * @access public
     * @static 
     * @return array of essay fields
     */
    static function getFields( $grouping, $sourceID = 0, $targetID = 0) {
        require_once 'CRM/Quest/DAO/EssayType.php';
        $essays = array();
        $type =& new CRM_Quest_DAO_EssayType( );
        $type->grouping  = $grouping;
        $type->is_active = 1;
        $type->orderby( 'weight asc' );
        $type->find( );

        $params = null;
        if ( $sourceID && $targetID ) {
            $params = array( 'source_contact_id' => $sourceID,
                             'target_contact_id' => $targetID );
        }

        while ( $type->fetch( ) ) {
            $essays[$type->name] = array( 'typeID'     => $type->id,
                                          'name'       => $type->name,
                                          'label'      => $type->label,
                                          'attributes' => $type->attributes,
                                          'wordCount'  => $type->max_word_count,
                                          'required'   => $type->is_required );

            if ( $params ) {
                $params['essay_type_id'] = $type->id;
                $result = array( );
                CRM_Core_DAO::commonRetrieve('CRM_Quest_DAO_Essay', $params, $result);
                
                if ( $result['id'] ) {
                    $essays[$type->name]['essayID'] = $result['id'];
                    $essays[$type->name]['essay']   = $result['essay'];
                }
            }
        }
        return $essays;
    }

 
    /** 
     * function to set default values for essay fields 
     * 
     * @param array  $essays      the essays field reference
     * @param array  $defaults    reference array for storing default values. 
     *  
     * @access public 
     * @static  
     * @return void 
     */ 
    static function setDefaults( &$essays, &$defaults ) { 
        foreach ( $essays as $name => $essay ) { 
            if ( $essay['essay'] ) { 
                $defaults[$name] = $essay['essay']; 
            } 
        }     
    } 

}
    
?>