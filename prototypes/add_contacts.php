<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>  
  <title>Add Contact</title>
  <meta http-equiv="Content-Style-Type" content="text/css">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<base href="http://localhost/dgg/prototypes/">
<style type="text/css" media="all">@import "misc/drupal.css";</style>
<style type="text/css" media="all">@import "themes/box_grey_smarty/style.css";</style>
</head>
<!-- init function sets display properties for form sections -->
<body onload="init('add_contact','contact_title');">

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
        <li class="expanded"><a href="http://localhost/dgg/drupal/rms" title="">CRM</a>
        <ul>
        <li class="expanded"><a href="http://localhost/dgg/drupal/rms/contact" title="List, edit and add contacts">Contacts</a>
        <ul>
        <li class="leaf"><a href="http://localhost/dgg/drupal/rms/contact/list" class="active">Add Contact</a></li>
        <li class="leaf"><a href="http://localhost/dgg/drupal/rms/contact/add">Import Contacts</a></li>
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
	<div class="breadcrumb"><a href="">Home</a> &raquo; <a href="http://localhost/dgg/prototypes" title="">CRM</a> &raquo; <a href="http://localhost/dgg/prototypes/list_contacts.html" title="">Contacts</a> &raquo; Add Contact</div>

    <!-- recently viewed items bar -->
    <div class="rmsListNav">
        <span>Recently Viewed:</span> 
        <a href=""><img src="i/contacts.png" align="absmiddle">&nbsp;Janet Ball</a> &nbsp;&nbsp;
        <a href=""><img src="i/contacts.png" align="absmiddle">&nbsp;Darsha</a>
    </div>

	<!-- start main content -->
<script type="text/javascript">
//<![CDATA[
var sections = new Array('demographics', 'notes', 'phone0_2', 'location2', 'expand_phone0_3', 'phone0_3');
var showSections = new Array('core');
function init(formName,fieldName) {
    for (var i = 0; i < sections.length; i++) {
        document.getElementById(sections[i]).style.display = 'none';
    }
    for (var i = 0; i < showSections.length; i++) {
        document.getElementById(showSections[i]).style.display = 'block';
    }
    document.forms[formName].elements[fieldName].focus();
}

function show(section) {
    for (var i = 0; i < sections.length; i++) {
        if (sections[i] == section) {
            document.getElementById(sections[i]).style.display = 'block';
        }
    }
}

function hide(section) {
    for (var i = 0; i < sections.length; i++) {
        if (sections[i] == section) {
            document.getElementById(sections[i]).style.display = 'none';
        }
        else if (section == 'expand_phone0_2') {
            document.getElementById(section).style.display = 'none';
        }
    }
}
//]]>
</script>
    <table class="rmsPageTitle" width="100%">
        <tr><td><h3 class="content-title">Add Contact (Individual)</h3></td>
        <td align="right">
            <span class="rmsSubLink"><a href="">&raquo; Add Contact Organization</a> &nbsp; &raquo; <a href="">Add Household</a></span> &nbsp; &nbsp;
            <a href="" title="Help"><img src="i/help.png" align="absmiddle" alt="Help">&nbsp;Help</a>
        </td>
    </tr>
    </table>
    <br/>
	<ul class="tabs primary">
	<li class="active"><a href="admin/block" class="active">Contact Details</a></li>
	<li><a href="">Relationships and Roles</a></li>
	<li><a href="">Voter Info [ext]</a></li>
	</ul>
	
	<!-- start main content -->
	<form action="add_contacts.html" method="post" name="add_contact">
	<input type="submit" name="add_contact" value="save" class="form-submit">
	<input type="button" name="cancel" value="cancel" class="form-submit"><br/>

	<div id="core">
	<fieldset><legend>Name and Greeting</legend>
	<table border="0" cellpadding="2" cellspacing="2" width="90%"><tbody>

	<tr>
		<td class="form-item">
			<label>First / Last:</label>
		</td>
		<td class="form-item">
			<select name="edit[title]" id="contact_title"><option>-title-</option><option>Ms.</option><option>Mrs.</option><option>Mr.</option><option>Dr.</option><option>(none)</option></select>
			<input type="text" maxlength="255" class="form-text" name="edit[first_name]" size="25" value="" />
			<input type="text" maxlength="255" class="form-text" name="edit[last_name]" size="25" value="" />
			<select name="edit[suffix]"><option>-suffix-</option><option>Jr.</option><option>Sr.</option><option>II</option><option>(none)</option></select>
		</td>
	</tr>
	<tr>
		<td class="form-item">
			<label>Greeting:</label>
		</td>
		<td class="form-item">
			<select name="edit[greeting]"><option>default - 'Dear [first] [last]'</option><option>informal - 'Dear [first]'</option><option>formal - 'Dear [title] [last]'</option></select>
		</td>
	</tr>
	<tr>
		<td class="form-item">
			<label>Job Title:</label>
		</td>
		<td class="form-item">
			<input type="text" maxlength="255" class="form-text" name="edit[job_title]" size="35" value="" />
		</td>
	</tr>
	</tbody></table>
	</fieldset>

	<fieldset><legend>Communication Preferences</legend>
	<table border="0" cellpadding="2" cellspacing="2" width="90%"><tbody>
	<tr>
		<td class="form-item">
			<label>Privacy:</label>
		</td>
		<td class="form-item">
			<input type="checkbox" class="form-checkbox" value="edit[no_email]"> Do not call
			<input type="checkbox" class="form-checkbox" value="edit[no_email]"> Do not contact by email
			<input type="checkbox" class="form-checkbox" value="edit[no_email]"> Do not contact by postal mail
		</td>
	</tr>
	<tr>
		<td class="form-item">
			<label>Prefers:</label>
		</td>
		<td class="form-item">
			<select name="edit[pref_comm]"><option>-no preference-</option><option>by email</option><option>by phone</option><option>by postal mail</option></select>
			<div class="description">Preferred method of communicating with this individual</div>
		</td>
	</tr>

	</tbody></table>
	</fieldset>

	<fieldset><legend>Location</legend>
	<table border="0" cellpadding="2" cellspacing="2"><tbody>

	<tr>
		<td class="form-item">
			<select name="edit[0][context]"><option>Home</option><option>Work</option><option>Other</option></select> &nbsp;
		</td>
		<td colspan=2 class="form-item">
			<input type="checkbox" class="form-checkbox" value="edit[0][primary-location]"> <label>Primary location for this contact</label>
		</td>
	</tr>
	<tr>
		<td></td>
		<td class="form-item">
			<label>Preferred Phone:</label>
		</td>
		<td class="form-item">
			<select name="edit[0][phoneType][0]" class="form-select"><option selected>phone</option><option>mobile</option><option>fax</option><option>pager</option></select>
			<input type="text" maxlength="255" class="form-text" name="edit[0][phone][0]" size="25" value="" />
		</td>
	</tr>

    <!-- Second phone block.-->
	<tr><td></td><td colspan="2">
        <table id="phone0_2"><tr>
		<td class="form-item">
			<label>Other Phone:</label>
		</td>
		<td class="form-item">
			<select name="edit[phoneType1-2]" class="form-select"><option>phone</option><option selected value="mobile">mobile</option><option>fax</option><option>pager</option></select>
			<input type="text" maxlength="255" class="form-text" name="edit[0][phone][1]" size="25" value="" /><br/>
		</td></tr>
        <tr><td colspan="2"><a onclick="hide('phone0_2'); hide('expand_phone0_3'); return false;" onkeypress="hide('phone0_2'); return false;" href="#phone0_2">[-] hide phone...</a><td></tr>
        </table></td>
	</tr>

    <!-- Third phone block.-->
	<tr><td></td><td colspan="2">
        <table id="phone0_3"><tr>
		<td class="form-item">
			<label>Other Phone:</label>
		</td>
		<td class="form-item">
			<select name="edit[phoneType1-3]" class="form-select"><option>phone</option><option value="mobile">mobile</option><option value="fax" selected>fax</option><option>pager</option></select>
			<input type="text" maxlength="255" class="form-text" name="edit[0][phone][3]" size="25" value="" /><br/>
		</td></tr>
        <tr><td colspan="2"><a onclick="hide('phone0_3'); return false;" onkeypress="hide('phone0_3'); return false;" href="#phone0_3">[-] hide phone...</a><td></tr>
        </table></td>
	</tr>

	<tr><td></td><td colspan=2>
        <table id="expand_phone0_2">
        <tr><td><a onclick="show('phone0_2'); hide('expand_phone0_2'); show('expand_phone0_3'); return false;" onkeypress="show('phone0_2'); return false;" href="#phone0_2">[+] another phone...</a></td></tr>
        </table>
        <table id="expand_phone0_3">
        <tr><td><a onclick="show('phone0_3'); hide('expand_phone0_3'); return false;" onkeypress="show('phone0_3'); return false;" href="#phone0_3">[+] another phone...</a></td></tr>
        </table>
    </td></tr>
    
	<tr>
		<td></td>
		<td class="form-item">
			<label>Email:</label>
		</td>
		<td class="form-item">
			<input type="text" maxlength="255" class="form-text" name="edit[0][email][0]" size="35" value="" />
		</td>
	</tr>
	<tr><td></td><td colspan=2 class="form-item"><a href="">[+] another email</a></td></tr>

	<tr>
		<td></td>
		<td class="form-item">
			<label>Instant Message:</label>
		</td>
		<td class="form-item">
			<select name="edit[0][imType][0]" class="form-select"><option>-select IM service-</option><option>AIM (AOL)</option><option>ICQ</option><option>MSN Messenger</option><option>Yahoo! Messenger</option></select>
			<input type="text" maxlength="255" class="form-text" name="contact_location[0].im_1" size="15" value="" />
			<div class="description">Select IM service and enter screen-name / user id.</div>
		</td>
	</tr>
	<tr><td></td><td colspan=2 class="form-item"><a href="">[+] another instant message id</a></td></tr>

	<tr>
		<td></td>
		<td class="form-item">
			<label>Street Address:</label>
		</td>
		<td class="form-item">
			<input type="text" maxlength="255" class="form-text" name="edit[0][street]" size="35" value="" /><br/>
			<div class="description">Street number, street name, apartment/unit/suite - OR P.O. box</div>
		</td>
	</tr>
	<tr>
		<td></td>
		<td class="form-item">
			<label>Additional<br/>Address:</label>
		</td>
		<td class="form-item">
			<textarea class="form-textarea" name="edit[0][address2]" cols="35" rows="2"></textarea><br/>
			<div class="description">Supplemental address info, e.g. c/o, department name, building name, etc.</div>
		</td>
	</tr>
	<tr>
		<td></td>
		<td class="form-item">
			<label>City:</label>
		</td>
		<td class="form-item">
			<input type="text" maxlength="255" class="form-text" name="edit[city]" size="35" value="" /><br/>
		</td>
	</tr>
	<tr>
		<td></td>
		<td class="form-item">
			<label>State / Province:</label>
		</td>
		<td class="form-item">
			<select name="edit[0][state]"><option>-none-</option><option>California</option><option>Oregon</option><option>Washington</option><option>-Canadian Provinces-</option><option>British Columbia</option></select>
		</td>
	</tr>
	<tr>
		<td></td>
		<td class="form-item">
			<label>Zip / Postal Code:</label>
		</td>
		<td class="form-item">
			<input type="text" maxlength="64" class="form-text" name="edit[0][postal_code]" size="15" value="" /><br/>
		</td>
	</tr>
	<tr>
		<td></td>
		<td class="form-item">
			<label>Country:</label>
		</td>
		<td class="form-item">
			<select name="edit[0][country]"><option>-select-</option><option>Canada</option><option>India</option><option>Poland</option><option>United States</option></select>
		</td>
	</tr>

	</tbody></table>
	</fieldset>
	</div> <!-- end 'core' section of contact form -->

	<div id="location2">
	<fieldset><legend>Location</legend>
	<table border="0" cellpadding="2" cellspacing="2"><tbody>

	<tr>
		<td class="form-item">
			<select name="edit[1][context]"><option>Home</option><option selected>Work</option><option>Other</option></select> &nbsp;
		</td>
		<td colspan=2 class="form-item">
			<input type="checkbox" class="form-checkbox" value="edit[1][primary-location]"> <label>Primary location for this contact</label>
		</td>
	</tr>
	<tr>
		<td></td>
		<td class="form-item">
			<label>Preferred Phone:</label>
		</td>
		<td class="form-item">
			<select name="edit[1][phoneType][0]" class="form-select"><option selected>phone</option><option>mobile</option><option>fax</option><option>pager</option></select>
			<input type="text" maxlength="255" class="form-text" name="edit[1][phone[0]" size="25" value="" />
		</td>
	</tr>

    <!-- Second phone block.-->
    <div id="phone2-2" style="display: none;">
	<tr>
		<td></td>
		<td class="form-item">
			<label>Other Phone:</label>
		</td>
		<td class="form-item">
			<select name="edit[1][phoneType][1]" class="form-select"><option>phone</option><option selected value="mobile">mobile</option><option>fax</option><option>pager</option></select>
			<input type="text" maxlength="255" class="form-text" name="edit[1][phone][1]" size="25" value="" />
		</td>
	</tr>
    </div>

	<tr><td></td><td colspan=2 class="form-item"><a href="">[+] another phone</a></td></tr>
    
	<tr>
		<td></td>
		<td class="form-item">
			<label>Email:</label>
		</td>
		<td class="form-item">
			<input type="text" maxlength="255" class="form-text" name="edit[1][email][0]" size="35" value="" />
		</td>
	</tr>
	<tr><td></td><td colspan=2 class="form-item"><a href="">[+] another email</a></td></tr>
	<tr>
		<td></td>
		<td class="form-item">
			<label>Instant Message:</label>
		</td>
		<td class="form-item">
			<select name="edit[1][imType][0]" class="form-select"><option>-select IM service-</option><option>AIM (AOL)</option><option>ICQ</option><option>MSN Messenger</option><option>Yahoo! Messenger</option><option>pager</option></select>
			<input type="text" maxlength="255" class="form-text" name="edit[1][im][0]" size="15" value="" />
			<div class="description">Select IM service and enter screen-name / user id.</div>
		</td>
	</tr>
	<tr><td></td><td colspan=2 class="form-item"><a href="">[+] another instant message id</a></td></tr>

	<tr>
		<td></td>
		<td class="form-item">
			<label>Street Address:</label>
		</td>
		<td class="form-item">
			<input type="text" maxlength="255" class="form-text" name="edit[1][street]" size="35" value="" /><br/>
			<div class="description">Street number, street name, apartment/unit/suite - OR P.O. box</div>
		</td>
	</tr>
	<tr>
		<td></td>
		<td class="form-item">
			<label>Additional<br/>Address:</label>
		</td>
		<td class="form-item">
			<textarea class="form-textarea" name="edit[1][address2]" cols="35" row="2"></textarea><br/>
			<div class="description">Supplemental address info, e.g. c/o, department name, building name, etc.</div>
		</td>
	</tr>
	<tr>
		<td></td>
		<td class="form-item">
			<label>City:</label>
		</td>
		<td class="form-item">
			<input type="text" maxlength="255" class="form-text" name="edit[city]" size="35" value="" /><br/>
		</td>
	</tr>
	<tr>
		<td></td>
		<td class="form-item">
			<label>State / Province:</label>
		</td>
		<td class="form-item">
			<select name="edit[1][state]"><option>-none-</option><option>California</option><option>Oregon</option><option>Washington</option><option>-Canadian Provinces-</option><option>British Columbia</option></select>
		</td>
	</tr>
	<tr>
		<td></td>
		<td class="form-item">
			<label>Zip / Postal Code:</label>
		</td>
		<td class="form-item">
			<input type="text" maxlength="64" class="form-text" name="edit[1][postal_code]" size="15" value="" /><br/>
		</td>
	</tr>
	<tr>
		<td></td>
		<td class="form-item">
			<label>Country:</label>
		</td>
		<td class="form-item">
			<select name="edit[1][country]"><option>-select-</option><option>Canada</option><option>India</option><option>Poland</option><option>United States</option></select>
		</td>
	</tr>
	<tr>
		<td colspan=2>
			<a tabindex="20" onclick="hide('location2'); return false;" onkeypress="hide('location2'); return false;" href="#location2">[-] hide location...</a><br/><br/>
		</td>
	</tr>
	</tbody></table>
	</fieldset>
	</div> <!-- end location2 div -->

	<div id="location2_link">
	<a tabindex="20" onclick="show('location2'); return false;" onkeypress="show('location2'); return false;" href="#location2">[+] another location...</a><br/><br/>
	</div>
	

	<div id="demographics">
	<fieldset><legend>Demographics</legend>
	<table border="0" cellpadding="2" cellspacing="2"><tbody>
	<tr>
		<td class="form-item">
			<label>Gender:</label>
		</td>
		<td class="form-item">
			<input type="radio" class="form-radio" name="edit[gender]" value="F"> Female
			<input type="radio" class="form-radio" name="edit[gender]" value="M"> Male
			<input type="radio" class="form-radio" name="edit[gender]" value="T"> Transgender
		</td>
	</tr>
	<tr>
		<td class="form-item">
			<label>Date of Birth:</label>
		</td>
		<td class="form-item">
			<select name="edit[dob_day]"><option>-day-</option><option>01</option><option>02</option><option>03</option></select>
			<select name="edit[dob_day]"><option>-month-</option><option>Jan</option><option>Feb</option><option>Mar</option></select>
			<select name="edit[dob_day]"><option>-year-</option><option>1950</option><option>1951</option><option>1952</option></select>
		</td>
	</tr>
	<tr>
		<td class="form-item" colspan=2>
            <input type="checkbox" class="form-checkbox" value="1"> 
			<label>Contact is Deceased</label>
		</td>
	</tr>
	<tr>
		<td class="form-item">
			<label>[ custom demographics flds ]:</label>
		</td>
		<td class="form-item">... go here ...
		</td>
	</tr>
	<tr>
		<td colspan=2>
			<a tabindex="20" onclick="hide('demographics'); return false;" onkeypress="hide('demographics'); return false;" href="#demographics">[-] hide demographics...</a><br/><br/>
		</td>
	</tr>

	</tbody></table>
	</fieldset>
	</div> <!-- end demographics div -->

	<div id="demographics_link">
	<a tabindex="20" onclick="show('demographics'); return false;" onkeypress="show('demographics'); return false;" href="#demographics">[+] demographics (gender, date of birth)</a><br/><br/>
	</div>

	<div id="notes">
	<fieldset><legend>Contact Notes</legend>
	<table border="0" cellpadding="2" cellspacing="2" width="90%"><tbody>
	<tr>
		<td class="form-item">
			<label>Notes:</label>
		</td>
		<td> &nbsp; </td>
		<td class="form-item">
			<textarea class="form-textarea" name="edit[notes1]" rows="3" cols="70"></textarea><br/>
			<div class="description">Record any descriptive comments about this contact. You may add an unlimited number of notes, and view or search on them at any time.</div>
		</td>
	</tr>
	<tr>
		<td colspan=3>
			<a tabindex="20" onclick="hide('notes'); return false;" onkeypress="hide('notes'); return false;" href="#notes">[-] hide notes...</a>
		</td>
	</tr>

	</tbody></table>
	</fieldset>
	</div> <!-- end notes div -->

	<div id="notes_link">
	<a tabindex="20" onclick="show('notes'); return false;" onkeypress="show('notes'); return false;" href="#notes">[+] contact notes</a>
	</div>
	
	<div class="form-item">
	<input type="submit" name="add_contact" value="save" class="form-submit">
	<input type="button" name="cancel" value="cancel" class="form-submit"><br/>
	</div>
	</form>
    
	<!-- end content -->

<!-- end main content -->
</td><!-- mainContent -->		
</tr>

</tbody></table>

<?php
include_once 'page_footer.tpl';
?>

</body></html>


