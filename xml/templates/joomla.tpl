<?xml version="1.0" encoding="utf-8"?>
<install type="component" version="2.1">
  <name>CiviCRM</name>
  <creationDate>04/01/2008</creationDate>
  <copyright>(C) CiviCRM LLC</copyright>
  <author>CiviCRM LLC</author>
  <authorEmail>info@civicrm.org</authorEmail>
  <authorUrl>civicrm.org</authorUrl>
  <version>2.1</version>
  <description>CiviCRM</description>
  <files>
      <filename>civicrm.php</filename>
      <filename>civicrm.html.php</filename>
  </files>
  <params>
    <param name="task" type="list" default="civicrm/profile" label="Choose CiviCRM task">
       <option value="civicrm/user">Contact Dashboard</option>
       <option value="civicrm/profile">Profile Search and Listings</option>
       <option value="civicrm/profile/create">Profile Create</option>
       <option value="civicrm/profile/edit">Profile Edit</option>
       <option value="civicrm/profile/view">Profile View</option>
       <option value="civicrm/contribute/transact">Online Contribution</option>
       <option value="civicrm/event/info">Event Info Page</option>
       <option value="civicrm/event/register">Online Event Registration</option>
    </param>
    <param name="id"    type="text" size="5" label="Contribution or Event id" description="The ID number of your Contribution or Event as defined when the Contribution or Event was created. Leave blank if not relevant"/>
    <param name="gid"   type="text" size="5" label="Profile id" description="The ID of your Profile as defined when the profile was created. Leave blank if not relevant"/>
    <param name="reset" type="text" size="5" default="1" label="Reset" description="Keep this set to 1. This is needed for the form to work properly." />
  </params>
  <install>
    <queries>
    </queries>
  </install>
  <uninstall>
      <queries>
      </queries>
  </uninstall>
  <installfile>install.civicrm.php</installfile>
  <uninstallfile>uninstall.civicrm.php</uninstallfile>
  <administration>
    <menu task="civicrm/dashboard&amp;reset=1">CiviCRM</menu>
                <submenu>
                        <menu task="civicrm/dashboard&amp;reset=1">CiviCRM Home</menu>
                        <menu task="civicrm/contact/search&amp;reset=1">Find Contacts</menu>
                        <menu task="civicrm/group&amp;reset=1">Manage Groups</menu>
                        <menu task="civicrm/import&amp;reset=1">Import Contacts</menu>
                        <menu task="civicrm/contribute&amp;reset=1">CiviContribute</menu>
                        <menu task="civicrm/pledge&amp;reset=1">CiviPledge</menu>
                        <menu task="civicrm/member&amp;reset=1">CiviMember</menu>
                        <menu task="civicrm/event&amp;reset=1">CiviEvent</menu>
                        <menu task="civicrm/admin&amp;reset=1">Administer CiviCRM</menu>
                </submenu>
    <files folder="admin">
      <filename>admin.civicrm.php</filename>
      <filename>toolbar.civicrm.php</filename>
      <filename>install.civicrm.php</filename>
      <filename>uninstall.civicrm.php</filename>
      <filename>configure.php</filename>
      <folder>civicrm/{$file}</folder>
    </files>
  </administration>
</install>
