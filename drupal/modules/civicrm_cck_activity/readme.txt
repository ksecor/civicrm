// $Id: README.txt,v 1.1 2009/15/05 07:17:11  Exp $

============================
civicrmActivity Readme
============================

Requirements
------------

This module requires CiviCRM 2.2.x or greater and Drupal 6.x.
content,Content Copy,Number,Text and User Reference of cck.
drupal's taxonomy module and upload module( for file attachements) should be enabled .
	
	

Installation Instructions
-------------------------

To install the civicrmActivity module, unpack the .tar.gz archive
to your sites' `modules` directory.
enable module Administer > Site building > Modules 

How to Use
----------

This module creates node type 'civicrmactivity' with cck fields.
you need to create civicrm acivity type and custom data for that 
activity type. Custom data fields are mapped with cck in to civicrmActivity.cck
file.

When you will create new Activity in civicrm with that activity type, drupal
node will create containing Activity data.Attached files for Activity also
will attach for that node. When you will edit Activity, node also will be updated.
You can create taxonomy terms for 'civicrmactivity' content type which you can select 
while creating Civicrm acivity, if and Taxonomy Custom Filed is created and its ID is defined  in civicrm_cck_activity.module.

Editing civicrmActivity.cck and civicrm_cck_activity.module.
------------------------------------------------------------

* If you are adding the Custom fileds to an activity type, then in cck fields definition you have to mention the custom id of that field for key civicrm_field.
Example: In our Content type 'civicrmactivity', we have a integer field Audience Size. So, Create a custom field for meeting type Activity, and mention the civicrm_field as ,  'civicrm_field' => 'custom_{custom field ID}'( eg: custom_1 ),  in field definition of Audience size in civicrmActivity.cck . Now when the civicrm activity of type " meeting "  will be created, its Audience type field will be mapped with the node field, 'Audience Size'.  Similarly you can define for , Audience Type, Meeting Type. Remember that the Custom fields for, Audience Type & Meeting Type sould be of type " Alphanumeric-Select " and should contain the values as allowed in the cck field defination of the those fields.

* If you want to assign a Taxonomy term to the created civicrm Activity node, you should create a custom field (select box) and define its Id as 
'civicrm_taxonomy_field'  =>'custom_{custom field ID}' ( eg: custom_4 ),in  $content['extra'] array of civicrmActivity.cck. Also define the civicrm_taxonomy_fid with the fid of the created custom field for taxanomy (eg: 'civicrm_taxonomy_fid'   =>'1')  in $content['extra']. After doing so the taxonomy terms for 'civicrmactivity' node type will be fetched in the custom field for taxonomy Terms in civicrm activity.
  To change the activity type in $content['extra'] array of civicrmActivity.cck , modify the value of 'civicrm_activity_type' with the id of activty type.

Note:-- Use the latest date module for working of the date functionality.



