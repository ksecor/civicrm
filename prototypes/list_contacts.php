<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>  
  <title>Contacts</title><meta http-equiv="Content-Style-Type" content="text/css">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="misc/drupal.css" type="text/css" />
<link rel="stylesheet" href="themes/box_grey_smarty/style.css" type="text/css" />

<!-- <style type="text/css" media="all">@import "misc/drupal.css";</style>
<style type="text/css" media="all">@import "themes/box_grey_smarty/style.css";</style> -->

</head>
<body>

<?php
include_once 'page_header.tpl';
?>

<table id="content">
	<tbody><tr>
	<td class="sidebar" id="sidebar-left">

<?php
include_once 'crm_sideblocks.tpl';
?>

    <div class="block block-user" id="block-user-1">
        <h2>Site Menu</h2>
        <div class="content"><div class="menu">
        <ul>
        <li class="expanded"><a href="index.html" title="">CRM</a>
        <ul>
        <li class="expanded"><a href="http://localhost/dgg/drupal/rms/contact" title="List, edit and add contacts">Contacts</a>
        <ul>
        <li class="leaf"><a href="http://localhost/dgg/prototypes/add_contacts.html" class="active">Create Contact</a></li>
        <li class="leaf"><a href="http://localhost/dgg/prototypes/import_contacts.html">Import Contacts</a></li>
        <li class="leaf"><a href="http://localhost/dgg/drupal/rms/contact/search">Contact Groups</a></li>
        </ul>
        </li>
        <li class="leaf"><a href="http://localhost/dgg/drupal/rms/communicate" title="">Communicate</a></li>

        </ul>
        </li>
        <li class="collapsed"><a href="http://localhost/dgg/drupal/blog" title="view blogs here">blogs</a></li>
        <li class="collapsed"><a href="http://localhost/dgg/drupal/node" title="">content</a></li>
        <li class="collapsed"><a href="http://localhost/dgg/drupal/search" title="">search</a></li>
        <li class="leaf"><a href="http://localhost/dgg/drupal/user/1">my account</a></li>
        <li class="leaf"><a href="http://localhost/dgg/drupal/profile" title="">user list</a></li>
        <li class="collapsed"><a href="http://localhost/dgg/drupal/admin">administer</a></li>
        <li class="leaf"><a href="http://localhost/dgg/drupal/logout">log out</a></li>

        </ul>
    </div></div></div>
</td> <!-- End of sidebar cell -->

<td class="main-content" id="content-left">
    <!-- breadcrumb bar -->
	<div class="breadcrumb"><a href="http://localhost/dgg/drupal">Home</a> &raquo; <a href="http://localhost/dgg/prototypes" title="">CRM</a> &raquo; Contacts</div>

    <!-- recently viewed items bar -->
    <div class="rmsListNav">
        <span>Recently Viewed:</span> 
        <a href=""><img src="i/contacts.png" align="absmiddle">&nbsp;Janet Ball</a> &nbsp;&nbsp;
        <a href=""><img src="i/contacts.png" align="absmiddle">&nbsp;Darsha</a>
    </div>

	<!-- start main content -->
    <table class="rmsPageTitle" width="100%">
        <tr><td><h3 class="content-title">Contacts</h3></td>
        <td align="right"><a href="" title="Help"><img src="i/help.png" align="absmiddle" alt="Help">&nbsp;Help</a></td></tr>
    </table>
        
	<table><tbody><tr id="rms-frame">
    <td> <!-- Begin cell for contact list -->

    <!-- Form to choose a list to display. -->
    <div id="select-contact-list">
	<form action="list_contacts.html" method="post" name="select-contact-list">
    <div class="form-item">
    <label>List: <label>
    <select name="view_list" class="form-select">
        <option value="">-all contacts-</option><option value="5">Board Members</option><option value="3">Top Donors</option><option value="4">Active Volunteers</option>
    </select>
	<input type="submit" name="change_list_view" value="go">
    </div></div>
    
	<div id="contact_list">
	<table class="rmsListNav" width="100%"><tbody>
    <tr>
        <td width="150">
            &nbsp; <a href="" title="Export all records in this list"><img src="i/export.png" align="absmiddle"> Export</a>
        </td>
        <td>
            <a href="" title="Go to first record">&lt;&lt;&nbsp;first&nbsp;&nbsp;
            <a href="" title="Go to previous record">&lt;&nbsp;previous
            &nbsp; &nbsp; &nbsp; (1-25 of 233) &nbsp; &nbsp; &nbsp;
            <a href="" title="Go to next record">&gt;&nbsp;next&nbsp;&nbsp;
            <a href="" title="Go to previous record">&raquo;&nbsp;previous
        </td>
        <td class="form-item" align="right">
            <form name="rmsPager" action="post" action="list_contacts.html">
            <span>Page <input class="form-text" type="text" name="page_number" size="1" value="1"> of 9</span>
            <input type="submit" name="goToPage" value="go">&nbsp;
            </form>
        </td>
    </tr>
    </tbody></table>
    
	<form action="list_contacts.html" method="post" name="rmsListForm">
    <table class="rmsListContacts" width="100%"><tbody>
    <tr><th><a href="list_contacts.html" title="Sort by Name"><img src="i/arrow.png" align="absmiddle">&nbsp;Name</a></th>
        <th><a href="list_contacts.html" title="Sort by Email"><img src="i/arrow.png" align="absmiddle">&nbsp;Email</a></th>
        <th><a href="list_contacts.html" title="Sort by Phone"><img src="i/arrow.png" align="absmiddle">&nbsp;Phone</a></th>
        <th><a href="list_contacts.html" title="Sort by Address"><img src="i/arrow.png" align="absmiddle">&nbsp;Address</a></th>
        <th><a href="list_contacts.html" title="Sort by City"><img src="i/arrow.png" align="absmiddle">&nbsp;City</th>
        <th><a href="list_contacts.html" title="Sort by State"><img src="i/arrow.png" align="absmiddle">&nbsp;State/Prov</th>
        <th></th>
    </tr>
    <tr class="odd">
        <td><a href="edit_contact.html" title="View this contact.">Ball, Janet</a></td>
        <td><a href="" title="Send email to this contact.">janet.ball@yahoo.com</a></td>
        <td><a href="" title="Log a call to this phone number.">415-423-0951</a></td>
        <td><a href="" title="Send a letter to this contact">12 Page St., Apt 3</td>
        <td>San Francisco</td>
        <td>CA</td>
        <td><input type="hidden" name="edit[status][1]" value="0" />
        <div class="form-item">
        <input type="checkbox" class="form-checkbox" name="edit[status][1]" id="edit-status[1]" value="1" />
        </div>
        </td>
    </tr>
    <tr class="even">
        <td><a href="edit_contact.html" title="View this contact.">Clarkson, Paula</a></td>
        <td><a href="" title="Send email to this contact.">pclarkson@hotmail.com</td>
        <td><a href="" title="Log a call to this phone number.">415-423-0951</a></td>
        <td><a href="" title="Send a letter to this contact">12 Riverview St.</td>
        <td>Sacramento</td>
        <td>CA</td>
        <td><input type="hidden" name="edit[status][1]" value="0" />
        <div class="form-item">
        <input type="checkbox" class="form-checkbox" name="edit[status][1]" id="edit-status[1]" value="1" />
        </div>
        </td>
    </tr>
    <tr class="odd">
        <td><a href="edit_contact.html" title="View this contact.">Ball, Janet</a></td>
        <td><a href="" title="Send email to this contact.">janet.ball@yahoo.com</td>
        <td><a href="" title="Log a call to this phone number.">415-423-0951</a></td>
        <td><a href="" title="Send a letter to this contact">12 Page St., Apt 3</td>
        <td>San Francisco</td>
        <td>CA</td>
        <td><input type="hidden" name="edit[status][1]" value="0" />
        <div class="form-item">
        <input type="checkbox" class="form-checkbox" name="edit[status][1]" id="edit-status[1]" value="1" />
        </div>
        </td>
    </tr>
    <tr class="even">
        <td><a href="edit_contact.html" title="View this contact.">Clarkson, Paula</a></td>
        <td><a href="" title="Send email to this contact.">pclarkson@hotmail.com</td>
        <td><a href="" title="Log a call to this phone number.">415-423-0951</a></td>
        <td><a href="" title="Send a letter to this contact">12 Riverview St.</td>
        <td>Sacramento</td>
        <td>CA</td>
        <td><input type="hidden" name="edit[status][1]" value="0" />
        <div class="form-item">
        <input type="checkbox" class="form-checkbox" name="edit[status][1]" id="edit-status[1]" value="1" />
        </div>
        </td>
    </tr>
    <tr class="odd">
        <td><a href="edit_contact.html" title="View this contact.">Ball, Janet</a></td>
        <td><a href="" title="Send email to this contact.">janet.ball@yahoo.com</td>
        <td><a href="" title="Log a call to this phone number.">415-423-0951</a></td>
        <td><a href="" title="Send a letter to this contact">12 Page St., Apt 3</td>
        <td>San Francisco</td>
        <td>CA</td>
        <td><input type="hidden" name="edit[status][1]" value="0" />
        <div class="form-item">
        <input type="checkbox" class="form-checkbox" name="edit[status][1]" id="edit-status[1]" value="1" />
        </div>
        </td>
    </tr>
    <tr class="even">
        <td><a href="edit_contact.html" title="View this contact.">Clarkson, Paula</a></td>
        <td><a href="" title="Send email to this contact.">pclarkson@hotmail.com</td>
        <td><a href="" title="Log a call to this phone number.">415-423-0951</a></td>
        <td><a href="" title="Send a letter to this contact">12 Riverview St.</td>
        <td>Sacramento</td>
        <td>CA</td>
        <td><input type="hidden" name="edit[status][1]" value="0" />
        <div class="form-item">
        <input type="checkbox" class="form-checkbox" name="edit[status][1]" id="edit-status[1]" value="1" />
        </div>
        </td>
    </tr>
    <tr class="odd">
        <td><a href="edit_contact.html" title="View this contact.">Ball, Janet</a></td>
        <td><a href="" title="Send email to this contact.">janet.ball@yahoo.com</td>
        <td><a href="" title="Log a call to this phone number.">415-423-0951</a></td>
        <td><a href="" title="Send a letter to this contact">12 Page St., Apt 3</td>
        <td>San Francisco</td>
        <td>CA</td>
        <td><input type="hidden" name="edit[status][1]" value="0" />
        <div class="form-item">
        <input type="checkbox" class="form-checkbox" name="edit[status][1]" id="edit-status[1]" value="1" />
        </div>
        </td>
    </tr>
    <tr class="even">
        <td><a href="edit_contact.html" title="View this contact.">Clarkson, Paula</a></td>
        <td><a href="" title="Send email to this contact.">pclarkson@hotmail.com</td>
        <td><a href="" title="Log a call to this phone number.">415-423-0951</a></td>
        <td><a href="" title="Send a letter to this contact">12 Riverview St.</td>
        <td>Sacramento</td>
        <td>CA</td>
        <td><input type="hidden" name="edit[status][1]" value="0" />
        <div class="form-item">
        <input type="checkbox" class="form-checkbox" name="edit[status][1]" id="edit-status[1]" value="1" />
        </div>
        </td>
    </tr>
	</table>
	</form>
    
    <!-- Rows per page.  -->
    <table id="rms-list-action"><tbody><tr valign="top">
	<td class="rmsSubLink">
    <label>Rows per page:</label>
    25 &nbsp; | &nbsp; <a href="">50</a> &nbsp; | &nbsp; <a href="">100</a> &nbsp; | &nbsp; <a href="">All</a>
	</td>

    <!-- Action select. -->
	<td class="form-item" align="right">
	<form action="list_contacts.html" method="post" name="rmsActionForm">
    <label>Action:</label>
    <select name="rms_action" class="form-select">
        <option value="">-for selected records-</option><option value="del">Delete</option><option value="print">Print</option><option value="Export">Export</option><option value="addToGroup">Add to a Group</option><option value="addToHousehold">Add to Household</option><option value="defineRelationship">Define Relationship</option>
    </select>
	<input type="submit" name="do_action" value="go">
    <div class="rmsSubLink">Select: <a href="">All</a> &nbsp; | &nbsp; <a href="">None</a></div>
    </form>
	</td>
    </tr></tbody></table>
    
    </td> <!-- End cell for contact list -->
    
    <!-- Begin cell for wizard menus -->
    <!-- <td class="rmsWizardMenu"></td> -->
    <!-- End cell for wizard menus. -->
    
    </tr></tbody>
    </table>
	<!-- end content -->

<!-- end main content -->
</td><!-- mainContent -->		
</tr>
</tbody></table>

<?php
include_once 'page_footer.tpl';
?>

</body></html>
