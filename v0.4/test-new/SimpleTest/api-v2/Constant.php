<?php

require_once 'api/v2/Constant.php';

class TestOfConstantAPIV2 extends CiviUnitTestCase 
{
    function setUp() 
    {
        // make sure this is just _41 and _generated
    }
    
    function tearDown() 
    {
    }
    
    function testConstant( )
    {
        $constants = array( 'tag'                       => 5,
                            'group'                     => 4,
                            'locationType'              => 5,
                            'activityType'              => 5,
                            'individualPrefix'          => 4,
                            'individualSuffix'          => 8,
                            'gender'                    => 3,
                            'IMProvider'                => 6,
                            'stateProvinceAbbreviation' => 60,
                            'country'                   => 242,
                            'countryIsoCode'            => 242,
                            'customGroup'               => 1,
                            'ufGroup'                   => 1,
                            'tasks'                     => 0,
                            'relationshipType'          => 7,
                            'currencySymbols'           => 16,
                            'currencyCode'              => 267,
                            'county'                    => 6,
                            'pcm'                       => 5,
                            );
        foreach ( $constants as $name => $value ) {
            $set = civicrm_constant_get( $name );
            $this->assertEqual( count( $set ), $value );
        }
    }
}

?>
