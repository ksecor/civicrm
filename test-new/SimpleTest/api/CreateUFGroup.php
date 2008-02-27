<?php

require_once 'api/crm.php';

class TestOfCreateUFGroupAPI extends UnitTestCase 
{
    function tearDown( ) 
    {
        if( $this->_UFField ) {
            $UFField = crm_delete_uf_field( $this->_UFField );
        }
        if( $this->_UFGroup ) {
            $UFGroup = crm_delete_uf_group(  $this->_UFGroup );
        }
    }
    
    function testCreateUFGroupError( )
    {
        $params  = array( );
        $UFGroup = crm_create_uf_group( $params );
        $this->assertIsA( $UFGroup, 'CRM_Core_Error' );
    }
    
    function testCreateUFGroup( )
    {
        $params = array( 
                        'title' => 'New Profile Group G01'
                        );
        $this->_UFGroup = crm_create_uf_group( $params );
        $this->assertIsA( $this->_UFGroup, 'CRM_Core_DAO_UFGroup' );
        $this->assertEqual( $this->_UFGroup->title, 'New Profile Group G01' );
    }
    
    function testCreateUFGroup02( )
    {
        $params = array(
                        'title'     => 'New Profile Group G02',
                        'help_pre'  => 'Help For Profile Group G02',
                        'is_active' => 1
                        );
        $this->_UFGroup = crm_create_uf_group( $params );
        $this->assertIsA( $this->_UFGroup, 'CRM_Core_DAO_UFGroup' );
        $this->assertEqual( $this->_UFGroup->title,  'New Profile Group G02' );
        $this->assertEqual( $this->_UFGroup->help_pre, 'Help For Profile Group G02' );
        $this->assertEqual( $this->_UFGroup->is_active, 1 );
    }
    
    function testCreateUFGroup03( )
    {
        $params = array(
                        'title'     => 'New Profile Group G03',
                        'help_pre'  => 'Help For Profile Group G03',
                        'help_post' => 'This is Profile Group G03'
                        );
        $this->_UFGroup  = crm_create_uf_group( $params );
        $this->assertIsA( $this->_UFGroup, 'CRM_Core_DAO_UFGroup' );
        $this->assertEqual( $this->_UFGroup->title,  'New Profile Group G03' );
        $this->assertEqual( $this->_UFGroup->help_pre, 'Help For Profile Group G03' );
        $this->assertEqual( $this->_UFGroup->help_post,  'This is Profile Group G03' );
    }
    
    /**
     *Case for Editing UFGroup
     */ 
    function testUpdateUFGroup04( )
    {
        $params = array(
                        'title'     => 'New Profile Group G04',
                        'help_pre'  => 'Help For Profile Group G04',
                        'help_post' => 'This is Profile Group G04'
                        );
        $UFGroup = crm_create_uf_group( $params );
      
        $eparams = array(
                        'title'     => 'Edited Profile Group G04',
                        'help_pre'  => 'Help For Edited Profile Group G04',
                        'help_post' => 'This is Edited Profile Group G04'
                        );
        
        $this->_UFGroup = crm_update_uf_group( $eparams, $UFGroup );
        $this->assertIsA( $this->_UFGroup,'CRM_Core_DAO_UFGroup' );
        $this->assertEqual( $this->_UFGroup->title,  'Edited Profile Group G04' );
        $this->assertEqual( $this->_UFGroup->help_pre, 'Help For Edited Profile Group G04' );
        $this->assertEqual( $this->_UFGroup->help_post,  'This is Edited Profile Group G04' );
    }

    /**
     *Case for Creating UFField
     */ 
    function testCreateUFField( )
    {
        $params = array(
                        'title'     => 'New Profile Group G05',
                        'help_pre'  => 'Help For Profile Group G05',
                        'help_post' => 'This is Profile Group G05'
                        );
        $this->_UFGroup  = crm_create_uf_group( $params );
        $this->assertIsA( $this->_UFGroup,'CRM_Core_DAO_UFGroup' );
   
        $fparams['field_type']       = 'Individual';
        $fparams['field_name']       = 'Field_For_Group_id_'.$UFGroup->id;
        $fparams['location_type_id'] = 1;
        $fparams['phone_type']       = 'Phone';
        $fparams['weight']           = 1;
        
        $this->_UFField  = crm_create_uf_field( $this->_UFGroup, $fparams );
        $this->assertIsA( $this->_UFField,'CRM_Core_DAO_UFField' );
        $this->assertEqual( $this->_UFField->field_type,  'Individual' );
        $this->assertEqual( $this->_UFField->field_name, 'Field_For_Group_id_'.$UFGroup->id );
        $this->assertEqual( $this->_UFField->location_type_id,  1 );
        $this->assertEqual( $this->_UFField->phone_type,  'Phone' );
        $this->assertEqual( $this->_UFField->weight,  1 );
    }

    /**
     *Case for Creating UFField
     */ 
    function testCreateUFFieldWithoutWeightError( )
    {
        $params = array(
                        'title'     => 'New Profile Group G06',
                        'help_pre'  => 'Help For Profile Group G06',
                        'help_post' => 'This is Profile Group G06'
                        );
        $this->_UFGroup  = crm_create_uf_group( $params );
        
        $fparams['field_type']       = 'Individual';
        $fparams['field_name']       = 'Field_For_Group_id_'.$UFGroup->id;
        $fparams['location_type_id'] = 1;
        $fparams['phone_type']       = 'Phone';
        
        $this->_UFField = crm_create_uf_field( $this->_UFGroup, $fparams );
        $this->assertIsA( $this->_UFField, 'CRM_Core_Error' );
    }

    /**          
     *Case for Editing UFField
     */ 
    function testUpdateUFField( )
    {
        $params = array(
                        'title'     => 'New Profile Group G07',
                        'help_pre'  => 'Help For Profile Group G07',
                        'help_post' => 'This is Profile Group G07'
                        );
        $this->_UFGroup = crm_create_uf_group( $params );
        $this->assertIsA( $this->_UFGroup,'CRM_Core_DAO_UFGroup' );      
        
        $fparams['field_type']       = 'Individual';
        $fparams['field_name']       = 'Field_For_Group_id_'.$UFGroup->id;
        $fparams['location_type_id'] = 1;
        $fparams['phone_type']       = 'Phone';
        $fparams['weight']           = 1;
        $this->_UFField = crm_create_uf_field( $this->_UFGroup, $fparams );
        $this->assertIsA( $this->_UFField,'CRM_Core_DAO_UFField' );

        $eparams['field_type']       = 'Updated_Individual';
        $eparams['field_name']       = 'Updated_Field_For_Group_id_'.$UFGroup->id;
        $eparams['location_type_id'] = 1;
        $eparams['phone_type']       = 'Phone';
        $this->_UFField = crm_update_uf_field( $eparams, $this->_UFField );
        $this->assertIsA( $this->_UFField,'CRM_Core_DAO_UFField' );
        $this->assertEqual( $this->_UFField->field_type,  'Updated_Individual' );
        $this->assertEqual( $this->_UFField->field_name, 'Updated_Field_For_Group_id_'.$UFGroup->id );
        $this->assertEqual( $this->_UFField->location_type_id,  1 );
        $this->assertEqual( $this->_UFField->phone_type,  'Phone' );
    }

    function testDeleteUFGroup( )
    {
        $params = array(
                        'title'     => 'New Profile Group G08',
                        'help_pre'  => 'Help For Profile Group G08',
                        'help_post' => 'This is Profile Group G08'
                        );
        $UFGroup = crm_create_uf_group( $params );
        
        $dUFGroup = crm_delete_uf_group( $UFGroup );
        $this->assertEqual( $dUFGroup, true );
    }

    function testDeleteUFField( )
    {
        $params = array(
                        'title'     => 'New Profile Group G08',
                        'help_pre'  => 'Help For Profile Group G08',
                        'help_post' => 'This is Profile Group G08'
                        );
        $UFGroup = crm_create_uf_group( $params );
        
        $fparams['field_type']       = 'Individual';
        $fparams['field_name']       = 'Field_For_Group_id_'.$UFGroup->id;
        $fparams['location_type_id'] = 1;
        $fparams['phone_type']       = 'Phone';
        $fparams['weight']           = 1;
        
        $UFField = crm_create_uf_field( $UFGroup, $fparams );

        $dUFField = crm_delete_uf_field( $UFField );
        $this->assertEqual( $dUFField, true );

        $dUFGroup = crm_delete_uf_group( $UFGroup );
        $this->assertEqual( $dUFGroup, true );
    }
}
?>
