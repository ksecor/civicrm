<?xml version="1.0" encoding="utf-8"?>
<install method="upgrade" type="component" version="{$CiviCRMVersion}">
  <name>CiviCRM</name>
  <creationDate>01/20/2009</creationDate>
  <copyright>(C) CiviCRM LLC</copyright>
  <author>CiviCRM LLC</author>
  <authorEmail>info@civicrm.org</authorEmail>
  <authorUrl>civicrm.org</authorUrl>
  <version>{$CiviCRMVersion}</version>
  <description>CiviCRM</description>
  <files folder="site">
	  <filename>civicrm.php</filename>
	  <filename>civicrm.html.php</filename>
	  <folder>views</folder>
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
      <filename>toolbar.civicrm.html.php</filename>
      <filename>install.civicrm.php</filename>
      <filename>uninstall.civicrm.php</filename>
      <filename>configure.php</filename>
      <filename>civicrm.zip</filename>
    </files>
  </administration>
</install>
