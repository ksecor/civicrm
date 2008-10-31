<?php

function testCustomGroupOne( ) {
    $params = array( 'entityID'  => 102,
                     'custom_1'  => 'Env',
                     'custom_2'  => 'S'  ,
                     'custom_3'  => '19680612',
                     'custom_4'  => '^AEdu^A' );
  
    $error  = CRM_Core_BAO_CustomValueTable::setValues( $params );
    CRM_Core_Error::debug( $error );

    $result = CRM_Core_BAO_CustomValueTable::getValues( $params );
    CRM_Core_Error::debug( $result );
}

function testCustomGroupTwoSingle( ) {
        $params = array( 'entityID'    => 102,
                         'custom_5_-1' => 'First String',
                         );

        $error = CRM_Core_BAO_CustomValueTable::setValues( $params );
        CRM_Core_Error::debug( $error );
        
        $newParams = array( 'entityID'    => 102,
                            'custom_5'    => 1 );
        $result = CRM_Core_BAO_CustomValueTable::getValues( $newParams );
        CRM_Core_Error::debug( $result );
}

function testCustomGroupTwoMultiple( ) {
        $params = array( 'entityID'    => 102,
                         'custom_5_-1' => 'First Multiple String',
                         'custom_5_-2' => 'Second Multiple String',
                         );

        $error = CRM_Core_BAO_CustomValueTable::setValues( $params );
        CRM_Core_Error::debug( $error );
        
        $newParams = array( 'entityID'    => 102,
                            'custom_5'    => 1 );
        $result = CRM_Core_BAO_CustomValueTable::getValues( $newParams );
        CRM_Core_Error::debug( $result );
}

function testCustomGroupTwoUpdate( ) {
        $params = array( 'entityID'    => 102,
                         'custom_5_6' => 'FOO',
                         'custom_5_7' => 'BAR',
                         );

        $error = CRM_Core_BAO_CustomValueTable::setValues( $params );
        CRM_Core_Error::debug( $error );
        
        $newParams = array( 'entityID'    => 102,
                            'custom_5'    => 1 );
        $result = CRM_Core_BAO_CustomValueTable::getValues( $newParams );
        CRM_Core_Error::debug( $result );
}

function testCustomGroupTwoOldFormat( ) {
        $params = array( 'entityID' => 102,
                         'custom_5' => 'Where will this go?',
                         );
        
        $error = CRM_Core_BAO_CustomValueTable::setValues( $params );
        CRM_Core_Error::debug( $error );
        
        $newParams = array( 'entityID'    => 102,
                            'custom_5'    => 1 );
        $result = CRM_Core_BAO_CustomValueTable::getValues( $newParams );
        CRM_Core_Error::debug( $result );
}

function run( ) {
    require_once '../civicrm.config.php';
    require_once 'CRM/Core/Config.php';
    $config = CRM_Core_Config::singleton( );

    require_once 'CRM/Core/BAO/CustomValueTable.php';

    // testCustomGroupOne( );
    // testCustomGroupTwoSingle( );
    // testCustomGroupTwoMultiple( );
    // testCustomGroupTwoUpdate( );
    testCustomGroupTwoOldFormat( );
}  


run( );