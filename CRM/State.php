<?php
/**
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


require_once 'CRM/Error.php';

class CRM_State {

    protected $_name;
    protected $_displayName;

    // this is a combination "OR" of the STATE_* constants defined below
    protected $_type;

    protected $_back;
    protected $_next;

    protected $_stateMachine;

    //
    // this is like an enum, primarily since a state could have multiple
    // values
    //
    const
    INITIAL     =  1,
        // changed name since final is a reserved word in php5
        SFINAL      =  2,
        SEQ         =  4,
        BACK_BRANCH =  8,
        NEXT_BRANCH = 16,
        PRESENT     = 32,
        COND        = 64;

    function CRM_State( $name, $displayName, $type, $back, $next, $stateMachine ) {
        $this->_name        = $name;
        $this->_displayName = $displayName;
        $this->_type        = $type;
        $this->_back        = $back;
        $this->_next        = $next;
    
        $this->_stateMachine = $stateMachine;
    }

    function getBackState( &$page ) {
        if ( $this->_type & CRM_State::INITIAL ) {
            $page->handle('display');
        } elseif ( $this->_type & CRM_State::SEQ ) {
            $back =& $page->controller->getPage($this->_back);
            $back->handle('jump');
        } elseif ( $this->_type & CRM_State::BACK_BRANCH ) {
            $backName = $this->_back; 
            if ( is_array( $backName ) ) { 
                $back =& $page->controller->getPage( call_user_func( $backName, $page ) ); 
            } else if ( $this->_stateMachine->isValidStateName($backName )) { 
                $back =& $page->controller->getPage( $backName ); 
            } else { 
                CRM_Error::fatal( '', 'CRM-STM-8001', 'Fatal Error in back branch of code' ); 
            } 
            $back->handle('jump'); 
        } else { 
            // assume sequential fall through case 
            $back =& $page->controller->getPage($this->_back); 
            return $back->handle('jump'); 
        }
    }

    function getNextState( &$page ) {
        // CRM_Utils::debug( "State", $this );
        if ( $this->_type & CRM_State::SFINAL ) {
            $page->handle('process');
        } elseif ( $this->_type & CRM_State::SEQ ) {
            $next =& $page->controller->getPage($this->_next);
            // CRM_Utils::debug( "Next State", $next );
            return $next->handle('jump');
        } elseif ( $this->_type & CRM_State::NEXT_BRANCH ) {
            $nextName = $this->_next; 
            if ( is_array( $nextName ) ) { 
                $next =& $page->controller->getPage( call_user_func( $nextName, $page ) ); 
            } else if ( $this->_stateMachine->isValidStateName($nextName )) { 
                $next =& $page->controller->getPage( $nextName ); 
            } else { 
                CRM_Error::fatal( '', 'CRM-STM-8002', 'Fatal Error in next branch of code' ); 
            } 
            $next->handle('jump'); 
        } else { 
            // assume sequential fall through case 
            $next =& $page->controller->getPage($this->_next); 
            return $next->handle('jump'); 
        } 
    }

    function getNextStateName( ) {
        // CRM_Utils::debug( "State", $this );
        if ( $this->_type & CRM_State::SFINAL ) {
            return null;
        } elseif ( $this->_type & CRM_State::SEQ ) {
            return $this->_stateMachine->find( $this->_next );
        } elseif ( $this->_type & CRM_State::NEXT_BRANCH ) {
            $nextName = $this->_next; 
            if ( is_array( $nextName ) ) { 
                $next =& $page->controller->getPage( call_user_func( $nextName, $page ) ); 
            } else if ( $this->_stateMachine->isValidStateName($nextName )) { 
                return $this->_stateMachine->find( $this->_next );
            } else { 
                CRM_Error::fatal( '', 'CRM-STM-8003', 'Fatal Error in next branch of code' ); 
            } 
            return $next;
        } else { 
            // assume sequential fall through case 
            $next =& $page->controller->getPage( $this->_next ); 
            return $next;
        } 
    }

    function validate( &$data ) {
        $data['valid'][$this->_name] = true;
    }

    function invalidate( &$data ) {
        $data['valid'][$this->_name] = null;
    }

    // getters and setters
    function getName( ) {
        return $this->_name;
    }

    function setName( $name ) {
        $this->_name = $name;
    }

    function getDisplayName( ) {
        return $this->_displayName;
    }

    function setDisplayName( $displayName ) {
        $this->_displayName = $displayName;
    }

    function getType( ) {
        return $this->_type;
    }

}

?>
