<?php
require_once '../civicrm.config.php';

require_once 'SimpleTest/unit_tester.php';
require_once 'SimpleTest/reporter.php';

if ( !defined( 'TEST' ) ) {
    define( 'TEST', __FILE__ );
}

class UnitTests extends GroupTest {
    
    function UnitTests( ) {
        $this->GroupTest( 'Unit Tests for CRM' );
        
        // contact api
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CreateContact.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/GetContact.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/FetchContact.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/UpdateContact.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/DeleteContact.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CreateContactWithCustomValues.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/UpdateContactWithCustomValues.php' );
        
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/GetContactFlat.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/GetContactCustom.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/Search.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/GetContactHierarchical.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/GetContactGroups.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM619.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM600.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM558.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM562.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM39.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM2474.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM491.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM503.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM514.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM520.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM521.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM522.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM523.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM531.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM627.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM645.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM707.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM778.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM703.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM785.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM787.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM764.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM652.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM825.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM878.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM881Get.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM881Delete.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM881Update.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM922.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM966.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM980.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM983.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM1011.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM1012.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM1184.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM1233.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM1469.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM1584.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM1657.php' );
        
        //api for ActivityType
        
        
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/MultiValuedCheckBox.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/Note.php'    );

        //api for Contribution
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CreateContribution.php'      );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/GetContribution.php'      );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/UpdateContribution.php'      );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/DeleteContribution.php'      );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/GetContributions.php'      );
        
        // group api
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/AddGroupContact.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/DeleteGroupContact.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/GetGroups.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/GetGroupContacts.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/SubscribeGroupContacts.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/ConfirmGroupContacts.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/getClassProperties.php'    );
        
        // location api
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CreateLocation.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/GetLocation.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/UpdateLocation.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM1282UpdateLocation.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/DeleteLocation.php'    );
        

        // membership type api
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CreateMembershipType.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/GetMembershipTypes.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/UpdateMembershipType.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/DeleteMembershipType.php' );


         // membership status api
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CreateMembershipStatus.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/GetMembershipStatuses.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/UpdateMembershipStatus.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/DeleteMembershipStatus.php' );

        // membership 
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CreateContactMembership.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/GetContactMemberships.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/UpdateContactMembership.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/DeleteMembership.php' );

        // history api
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CreateActivityHistory.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/GetActivityHistory.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/UpdateActivityHistory.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/DeleteActivityHistory.php' );
        
        // custom group api
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CreateCustomGroup.php');
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/UpdateCustomGroup.php');
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CreateCustomField.php');
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/UpdateCustomField.php');
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/CreateCustomValue.php');
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/GetCustomField.php');
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/GetCustomOptionValue.php');
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/UpdateDeleteCustomOption.php');
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/DeleteCustomGroup.php');
         
        //note api
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/CreateNote.php');
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/GetNote.php');
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/UpdateNote.php');
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/DeleteNote.php');

        //file api
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/CreateFile.php');
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/GetFile.php');
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/UpdateFile.php');
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/DeleteFile.php');
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/CreateEntityFile.php');
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/GetFilesByEntity.php');
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/DeleteEntityFile.php');
        
        // relationship api
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/CreateRelationship.php');
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/GetRelationship.php');
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/DeleteRelationship.php');
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/CreateRelationshipType.php');
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/GetRelationshipType.php');
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/UpdateRelationship.php');
        
        // tag api
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/CreateTag.php');
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/DeleteTag.php');
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/CreateEntityTag.php');
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/TagsByEntity.php');
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/GetEntitiesByTag.php');
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/DeleteEntityTag.php');
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/GetTag.php');
        
        //Participant api
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CreateParticipant.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/GetParticipants.php'      );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/UpdateParticipant.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/DeleteParticipant.php'    );
        
        //Participant Payment api
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CreateParticipantPayment.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/UpdateParticipantPayment.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/DeleteParticipantPayment.php'    );
        
        // UFGroup api
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CreateUFGroup.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/UpdateUFGroup.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CreateUFField.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/UpdateUFField.php'    );
        
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/Token.php' );

        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CreateProfileContact.php'    );
        
        //Event api
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CreateEvent.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/GetEvent.php'       );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/UpdateEvent.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/DeleteEvent.php'    );

    }

}

function user_access( $str ) {
    return true;
}

function module_list( ) {
    return array( );
}

if ( TEST == __FILE__ ) {

    require_once 'CRM/Core/Config.php';
    $test =& new UnitTests( );

    $config =& CRM_Core_Config::singleton();

    if (SimpleReporter::inCli()) {
        exit($test->run(new TextReporter()) ? 0 : 1);
    }
    $test->run(new HtmlReporter());
}


