<?php

require_once 'CRM/Form.php';

class CRM_Contacts_Form_CRUD extends CRM_Form {

  
  function __construct( $name, $state, $mode = self::MODE_NONE ) {
    parent::__construct( $name, $state, $mode );
  }
  
  function buildQuickForm( ) {
    
    
    $this->addDefaultButtons( array ( 1 => array ( 'next', 'Save', true ),
				      2 => array ( 'reset' , 'Reset', false ),
				      3 => array ( 'cancel', 'Cancel', false )
				      ));

    $prefixsel = array( 'title'=> '-title-', 'Mrs.'=> 'Mrs.', 'Ms.'=> 'Ms.', 'Mr.'=> 'Mr.', 'Dr'=> 'Dr.', 'none'=> '(none)' );
    //$prefixsel = array('-title-','Mrs.','Ms.','Mr.','Dr.','(none)' );
    $this->addElement( 'select', 'prefix', null, $prefixsel );
    $this->add( 'text', 'first_name', null, array('id'=>'txt'), true );
    $this->add( 'text', 'last_name', null, null, true);
    
    $suffixsel= array('suffix'=> '-suffix-','Jr.'=> 'Jr.','Sr.'=> 'Sr.', '||'=>'||','none'=> '(none)' );
    //$suffixsel= array('-suffix-','Jr.','Sr.', '||','(none)' );
    $this->add( 'select', 'suffix', null, $suffixsel );
    

    $greetingselect = array('Formal'=> 'default - Dear [first] [last]','Informal'=> 'Dear [first]', 
			    'Honorific'=>'Dear [title] [last]','Custom'=> 'Customized' );
  

    $this->addElement( 'select', 'greeting_type', null, $greetingselect);
    $this->add( 'text', 'job_title', null );
    $this->add( 'checkbox', 'do_not_phone', null );
    $this->add( 'checkbox', 'do_not_email', null );
    $this->add( 'checkbox', 'do_not_mail', null );

    $pcmvar = array( 'None'  => '-no preference-','Phone'=>'by phone', 'Email'=>'by email', 
		     'Postal'=>'by postal email');
    $this->add( 'select','preferred_communication_method',null,$pcmvar);

    $rd =& $this->addElement( 'radio', 'gender', 'femalex', 'Female','female' );
    $this->addElement( 'radio', 'gender', 'malex', 'Male', 'male' );
    $this->addElement( 'radio', 'gender', 'malex', 'Transgender','transgender' );

    $a = true;
    $rd->setChecked( $a );


    $ddselect = array(1 => '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18',
		      '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31' );
    
    $this->add('select', 'dd',null,$ddselect );
    $mmselect = array( 1 => 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec' );
    
    for( $i = 1950; $i < 2010; $i++ ) { 
      $yyselect[ $i ] = & strval( $i );
    }
    $this->add( 'select','mm',null,$mmselect );
    $this->add( 'select','yy', null, $yyselect );

    $this->add( 'checkbox', 'is_deceased', null );


    /*    $cotvar = array('Home'=>'Home','Work'=>'Work','Other'=>'Other');
    $this->addElement('select','contextselect',null,$cotvar);
    $this->add( 'checkbox', 'primarylocationcheckbox', null );
    $phvar = array('Phone'=> 'Phone', 'Mobile'=>'Mobile','Fax'=> 'Fax','Pager'=> 'Pager' );
    $this->add( 'select','phonetypeselect_1',null,$phvar);
    $this->add( 'text', 'phonetext_1', null );
    $this->add( 'select','phonetypeselect_2',null,$phvar);
    $this->add( 'text', 'phonetext_2', null );
    $this->add( 'select','phonetypeselect_3',null,$phvar);
    $this->add( 'text', 'phonetext_3', null );

    $this->add( 'text', 'editemailtext_1', null );
    $this->add( 'text', 'editemailtext_2', null );
    $this->add( 'text', 'editemailtext_3', null );

    $imvar = array('AIM'=> 'AIM (AOL)','ICQ'=> 'ICQ','MSN'=> 'MSN Messenger','Yahoo'=> 'Yahoo Messenger' );
    $this->add( 'select','imtypeselect_1',null,$imvar);
    $this->add( 'text', 'contactlocationtext_1', null );
    $this->add( 'select','imtypeselect_2',null,$imvar);
    $this->add( 'text', 'contactlocationtext_2', null );
    $this->add( 'select','imtypeselect_3',null,$imvar);
    $this->add( 'text', 'contactlocation_3', null );

    $this->add( 'text', 'editstreettext_0', null );
    $this->add( 'textarea', 'addresstextarea_0', null );
    $this->add( 'text', 'citytext', null );
    $this->add( 'text', 'postalcodetext_0', null );

    $esvar = array('California'=>'California','Oregon'=>'Oregon','Washington'=>'Washington',
	           'Canadian Provinces'=>'-Canadian Provinces-','British Columbia'=>'British Columbia');
    $this->addElement('select','editstateselect_0',null,$esvar);
    $ctyvar = array('Canada'=>'Canada','India'=>'India','Poland'=>'Poland','United States'=>'United States');
    $this->addElement('select','countryselect_0',null,$ctyvar);
    */

    if ( $this->_mode == self::MODE_VIEW || self::MODE_UPDATE ) {
      $this->setDefaultValues( );
    }
    if ( $this->validate( ) && $this->_mode == self::MODE_VIEW ) {
      $this->freeze( );
    }
  }
  
  function setDefaultValues( ) {
    $defaults = array( );
    
    $defaults['first_name'] = 'Dave';
    $defaults['last_name' ] = 'Greenberg';
    $defaults['Email'     ] = 'dgg@blackhole.net';
    $defaults['telephone_no_home'] = '1-800-555-1212';
    
    $this->setDefaults( $defaults );
  }
  
  function addRules( ) {
    $this->addRule( 'email', t(' should be a valid well formed email address.'), 'email' );
    $this->addRule( 'telephone_no_home', t( ' should be a valid phone number.'), 'phoneNumber' );
  }

  
  function postProcess( ) {
    $content = '<pre>' . print_r($_POST, TRUE) . '</pre>';
    CRM_Utils::debug( 'Content', $content );
  }
 
  /*
  function preProcess() {
    $content = '<pre>' . print_r($_POST, TRUE) . '</pre>';
    CRM_Utils::debug( 'Content', $content );
  }
  */


  function process( ) { // this form performs the action after clicking save
    //  print_r($_POST);
    // write your insert statements here
    
    $contact = new CRM_Contacts_DAO_Contact( );
    
    $contact->domain_id = 1;
    $contact->contact_type = $_POST['contact_type'];
    $contact->sort_name = $_POST['sort_name'];
    $contact->source = $_POST['source'];
    $contact->preferred_communication_method = $_POST['preferred_communication_method'];
    $contact->do_not_phone = $_POST['do_not_phone'];
    $contact->do_not_email = $_POST['do_not_email'];
    $contact->do_not_mail = $_POST['do_not_mail'];
    $contact->hash = $_POST['hash'];
    
    if( !$contact->insert( ) ) {
      die ("Cannot insert data in contact table.");
      // $contact->raiseError("Cannot insert","","continue");
    }
    
    $contact_individual = new CRM_Contacts_DAO_Contact_Individual();
    $contact_individual->contact_id = $contact->id;
    $contact_individual->first_name = $_POST['first_name'];
    $contact_individual->middle_name = $_POST['middle_name'];
    $contact_individual->last_name = $_POST['last_name'];
    $contact_individual->prefix = $_POST['prefix'];
    $contact_individual->suffix = $_POST['suffix'];
    $contact_individual->job_title = $_POST['job_title'];
    
    $contact_individual->greeting_type = $_POST['greeting_type'];
    $contact_individual->custom_greeting = $_POST['custom_greeting'];
    $contact_individual->gender = $_POST['gender'];

    if( $_POST['dd'] < 10  ){
      $day = "0".$_POST['dd'];
    }else{
      $day = $_POST['dd'];
    }

    if( $_POST['mm'] < 10  ){
      $mnt = "0".$_POST['mm'];
    }else{
      $mnt = $_POST['mm'];
    }

    $contact_individual->birth_date = $_POST['yy'].$mnt.$day;
    $contact_individual->is_deceased = $_POST['is_deceased'];
    
    if( !$contact_individual->insert( ) ){
      $contact->delete( );
      die ("Cannot insert data in contact individual table.");
      //$contact->raiseError("Cannot insert data in contact individual table...");
    }
  }

  

}
?>
