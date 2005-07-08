<?xml version="1.0" ?>
<mosinstall type="component">
  <name>CiviCRM</name>
  <creationDate>06/07/2005</creationDate>
  <author>Amy Hoy and Donald Lobo</author>
  <copyright>(C) Copyright 2005 by PICnet, Inc., and  Social Source Foundation</copyright>
  <authorEmail>amy@picnet.net</authorEmail>
  <authorUrl>www.picnet.net</authorUrl>
  <version>1.0</version>
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
  <installfile>install.civicrm.php</installfile>
  <uninstallfile>uninstall.civicrm.php</uninstallfile>
  <administration>
    <menu>CiviCRM</menu>
                <submenu>
                        <menu task="civicrm/group&reset=1">Manage Groups</menu>
                        <menu task="civicrm/import&reset=1">Import Contacts</menu>
                        <menu task="civicrm/contact/search&reset=1">Find Contacts</menu>
                        <menu task="civicrm/admin&reset=1">Administer CiviCRM</menu>
                </submenu>
    <files>
      <filename>admin.civicrm.php</filename>
      <filename>config.main.php</filename>
      <filename>config.inc.php</filename>
{foreach from=$files item=file}
      <filename>civicrm/{$file}</filename>
{/foreach}
    </files>
  </administration>
</mosinstall>
