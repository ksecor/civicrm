<?xml version="1.0" ?>
<mosinstall type="component">
  <name>CiviCRM</name>
  <creationDate>04/22/2007</creationDate>
  <copyright>(C) CiviCRM LLC</copyright>
  <author>CiviCRM LLC</author>
  <authorEmail>info@civicrm.org</authorEmail>
  <authorUrl>civicrm.org</authorUrl>
  <version>1.8</version>
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
       <option value="civicrm/contribute/transact">Online Contribution</option>
       <option value="civicrm/event/register">Online Event Registration</option>
    </param>
    <param name="id"    type="text" size="5" default="1" label="Contribution or Event id" />
    <param name="gid"   type="text" size="5" default="1" label="Profile id" />
    <param name="reset" type="text" size="5" default="1" label="Reset" />
  </params>
  <install>
    <queries>
    </queries>
  </install>
  <uninstall>
      <queries>
      </queries>
  </uninstall>
  <installfile><filename>install.civicrm.php</filename></installfile>
  <uninstallfile><filename>uninstall.civicrm.php</filename></uninstallfile>
  <administration>
    <menu>CiviCRM</menu>
                <submenu>
                        <menu task="civicrm/dashboard&reset=1">CiviCRM Home</menu>
                        <menu task="civicrm/contact/search&reset=1">Find Contacts</menu>
                        <menu task="civicrm/group&reset=1">Manage Groups</menu>
                        <menu task="civicrm/import&reset=1">Import Contacts</menu>
                        <menu task="civicrm/contribute&reset=1">CiviContribute</menu>
                        <menu task="civicrm/admin&reset=1">Administer CiviCRM</menu>
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
