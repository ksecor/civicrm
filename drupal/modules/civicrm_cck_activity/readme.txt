// $Id: README.txt,v 1.2.2.2.2.1 2008/09/01 04:43:43 posco Exp $

============================
civicrmActivity Readme
============================

Requirements
-------------------------------

This module requires CiviCRM 2.2.x or greater and Drupal 6.x.
content,Content Copy,Number,Text and User Reference of cck.
drupal's taxonomy module and upload module( for file attachements) should be enabled .
	
	

Installation Instructions
-------------------------------

To install the civicrmActivity module, unpack the .tar.gz archive
to your sites' `modules` directory.
enable module Administer > Site building > Modules 

How to Use
-------------------------------

This module creates node type 'civicrmactivity' with cck fields.
you need to create civicrm acivity type and custom data for that 
activity type. Custom data fields are mapped with cck in to civicrmActivity.cck
file.

When you will create new Activity in civicrm with that activity type, drupal
node will create containing Activity data.Attached files for Activity also
will attach for that node. When you will edit Activity, node also will update.
You can create taxonomy terms for 'civicrmactivity' content type which will
be reflected while creating Civicrm acivity.





