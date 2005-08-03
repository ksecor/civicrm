<?xml version="1.0" ?>
<mosinstall type="component">
  <name>CiviCRM</name>
  <creationDate>07/28/2005</creationDate>
  <author>Amy Hoy, Donald Lobo and Ryan Ozimek</author>
  <copyright>(C) Copyright 2005 by PICnet, Inc., and  Social Source Foundation</copyright>
  <authorEmail>ryan@picnet.net</authorEmail>
  <authorUrl>www.picnet.net</authorUrl>
  <version>1.1</version>
  <description>CiviCRM</description>
  <files>
  </files>
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
                        <menu task="civicrm/contact/search&reset=1">Find Contacts</menu>
                        <menu task="civicrm/group&reset=1">Manage Groups</menu>
                        <menu task="civicrm/import&reset=1">Import Contacts</menu>
                        <menu task="civicrm/admin&reset=1">Administer CiviCRM</menu>
                </submenu>
    <files>
      <filename>admin.civicrm.php</filename>
      <filename>toolbar.civicrm.php</filename>
      <filename>install.civicrm.php</filename>
      <filename>uninstall.civicrm.php</filename>
      <filename>config.main.php</filename>
      <filename>civicrm.php</filename>
{foreach from=$files item=file}
      <filename>civicrm/{$file}</filename>
{/foreach}
    </files>
  </administration>
</mosinstall>
