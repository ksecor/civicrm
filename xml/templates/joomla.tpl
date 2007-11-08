<?xml version="1.0" ?>
<mosinstall type="component">
  <name>CiviCRM</name>
  <creationDate>04/22/2007</creationDate>
  <copyright>(C) CiviCRM LLC</copyright>
  <author>CiviCRM LLC</author>
  <authorEmail>info@civicrm.org</authorEmail>
  <authorUrl>civicrm.org</authorUrl>
  <version>2.0</version>
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
                        <menu task="option=com_civicrm&amp;task=civicrm/dashboard&amp;reset=1">CiviCRM Home</menu>
                        <menu task="option=com_civicrm&amp;task=civicrm/contact/search&amp;reset=1">Find Contacts</menu>
                        <menu task="option=com_civicrm&amp;task=civicrm/group&amp;reset=1">Manage Groups</menu>
                        <menu task="option=com_civicrm&amp;task=civicrm/import&amp;reset=1">Import Contacts</menu>
                        <menu task="option=com_civicrm&amp;task=civicrm/contribute&amp;reset=1">CiviContribute</menu>
                        <menu task="option=com_civicrm&amp;task=civicrm/member&amp;reset=1">CiviMember</menu>
                        <menu task="option=com_civicrm&amp;task=civicrm/event&amp;reset=1">CiviEvent</menu>
                        <menu task="option=com_civicrm&amp;task=civicrm/admin&amp;reset=1">Administer CiviCRM</menu>
                </submenu>
    <files>
      <filename>admin.civicrm.php</filename>
      <filename>toolbar.civicrm.php</filename>
      <filename>install.civicrm.php</filename>
      <filename>uninstall.civicrm.php</filename>
      <filename>configure.php</filename>
{foreach from=$files item=file}
      <filename>civicrm/{$file}</filename>
{/foreach}
    </files>
  </administration>
</mosinstall>
