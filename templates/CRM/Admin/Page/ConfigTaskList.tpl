{capture assign="linkTitle"}{ts}Edit settings{/ts}{/capture}
{capture assign="destination"}{crmURL p="civicrm/admin/configtask" q="reset=1"}{/capture}
{capture assign="adminMenu"}{crmURL p="civicrm/admin" q="reset=1"}{/capture}
<div id="help" class="description">
{ts 1=$adminMenu}Use this checklist to review and complete configuration tasks for your site. You will be redirected back to this checklist after saving each setting. Settings which you have not yet
reviewed will be <span class="status-overdue">displayed in red</span>. After you've visited a page, the links will <span class="status-pending">display in green</span>  (although you may still need to revisit the page to complete or update the settings).
You can access this page again from the <a href="%1">Administer CiviCRM</a> menu at any time.{/ts}
</div>

<table class="selector">
<tr class="columnheader">
    <td colspan="2">
        <div id="scCollapsed"><a class="pane-collapsed" href="#" onclick="show('sc1','table-row');show('sc2','table-row'); hide('scCollapsed'); show('scExpanded');">{ts}Site Configuration{/ts}</a></div>
        <div id="scExpanded"><a class="pane-expanded" href="#" onclick="hide('sc1','table-row');hide('sc2','table-row'); show('scCollapsed'); hide('scExpanded');">{ts}Site Configuration{/ts}</a></div>
    </td>
</tr>
<tr class="even" id="sc1">
    <td class="tasklist nowrap"><a href="{crmURL p="civicrm/admin/setting/localization" q="reset=1&destination=`$destination`"}" title="{$linkTitle}">{ts}Localization{/ts}</a></td>
    <td>{ts}Localization settings include user language, default currency and available countries for address input.{/ts}</td>
</tr>
<tr class="even" id="sc2">
    <td class="tasklist nowrap"><a href="{crmURL p="civicrm/contact/domain" q="action=update&reset=1&destination=`$destination`"}" title="{$linkTitle}">{ts}Domain Information{/ts}</a></td>
    <td>{ts}Organization name, email address for system-generated emails, organization address{/ts}</li>
</tr>

<tr class="columnheader">
    <td colspan="2">
         <div id="vcCollapsed"><a class="pane-collapsed" href="#" onclick="show('vc1','table-row');show('vc2','table-row');show('vc3','table-row');show('vc4','table-row'); hide('vcCollapsed'); show('vcExpanded');">{ts}Viewing and Editing Contacts{/ts}</a></div>
         <div id="vcExpanded"><a class="pane-expanded" href="#" onclick="hide('vc1','table-row');hide('vc2','table-row');hide('vc3','table-row');hide('vc4','table-row'); show('vcCollapsed'); hide('vcExpanded');">{ts}Viewing and Editing Contacts{/ts}</a></div>
    </td>
</tr>
<tr class="even" id="vc1">
    <td class="tasklist nowrap"><a href="{crmURL p="civicrm/admin/setting/preferences/display" q="reset=1&destination=`$destination`"}" title="{$linkTitle}">{ts}Site Preferences{/ts}</a></td>
    <td>{ts}Configure screen and form elements for Viewing Contacts, Editing Contacts, Advanced Search, Contact Dashboard and WYSIWYG Editor.{/ts}</td>
</tr>
<tr class="even" id="vc2">
    <td class="tasklist nowrap"><a href="{crmURL p="civicrm/admin/setting/preferences/address" q="reset=1&destination=`$destination`"}" title="{$linkTitle}">{ts}Address Settings{/ts}</a></td>
    <td>{ts}Format addresses in mailing labels, input forms and screen display.{/ts}</td>
</tr>
<tr class="even" id="vc3">
    <td class="tasklist nowrap"><a href="{crmURL p="civicrm/admin/setting/mapping" q="reset=1&destination=`$destination`"}" title="{$linkTitle}">{ts}Mapping and Geocoding{/ts}</a></td>
    <td>{ts}Configure a mapping provider (e.g. Google or Yahoo) to display maps for contact addresses and event locations.{/ts}</td>
</tr>
<tr class="even"id="vc4">
    <td class="tasklist nowrap"><a href="{crmURL p="civicrm/admin/setting/misc" q="reset=1&destination=`$destination`"}" title="{$linkTitle}">{ts}Miscellaneous{/ts}</a></td>
    <td>{ts}Contact search behaviors, RECAPTCHA configuration.{/ts}</li>
</tr>

<tr class="columnheader">
    <td colspan="2">
         <div id="emCollapsed"><a class="pane-collapsed" href="#" onclick="show('em1','table-row');show('em2','table-row'); hide('emCollapsed'); show('emExpanded');">{ts}Sending Emails (includes contribution receipts and event confirmations){/ts}</a></div>
         <div id="emExpanded"><a class="pane-expanded" href="#" onclick="hide('em1','table-row');hide('em2','table-row'); show('emCollapsed'); hide('emExpanded');">{ts}Sending Emails (includes contribution receipts and event confirmations){/ts}</a></div>
    </td>
</tr>
<tr class="even" id="em1">
    <td class="tasklist nowrap"><a href="{crmURL p="civicrm/admin/setting/smtp" q="reset=1&destination=`$destination`"}" title="{$linkTitle}">{ts}Outbound Email{/ts}</a></td>
    <td>{ts}Settings for outbound email - either SMTP server, port and authentication or Sendmail path and argument.{/ts}</td>
</tr>
<tr class="even" id="em2">
    <td class="tasklist nowrap"><a href="{crmURL p="civicrm/admin/options/from_email_address" q="group=from_email_address&action=update&id=213&reset=1&destination=`$destination`"}" title="{$linkTitle}">{ts}From Email Addresses{/ts}</a></td>
    <td>{ts}Define general email address(es) that can be used as the FROM address when sending email to contacts from within CiviCRM (e.g. info@example.org){/ts}</li>
</tr>

<tr class="columnheader">
    <td colspan="2">
         <div id="cnCollapsed"><a class="pane-collapsed" href="#" onclick="show('cn1','table-row');{if $config->userFramework EQ 'Drupal'}show('cn2','table-row');{/if} hide('cnCollapsed'); show('cnExpanded');">{ts}Online Contributions / Online Membership Signup / Online Event Registration{/ts}</a></div>
         <div id="cnExpanded"><a class="pane-expanded" href="#" onclick="hide('cn1','table-row');{if $config->userFramework EQ 'Drupal'}hide('cn2','table-row');{/if} show('cnCollapsed'); hide('cnExpanded');">{ts}Online Contributions / Online Membership Signup / Online Event Registration{/ts}</a></div>
    </td>
</tr>
<tr class="even" id="cn1">
    <td class="tasklist nowrap"><a href="{crmURL p="civicrm/admin/paymentProcessor" q="action=add&reset=1&pp=PayPal&destination=`$destination`"}" title="{$linkTitle}">{ts}Payment Processors{/ts}</a></td>
    <td>{ts}Select and configure one or more payment processing services for online contributions, events and / or membership fees.{/ts}</td>
</tr>
  
{if $config->userFramework EQ 'Drupal'}
    <tr class="even" id="cn2">
        <td class="tasklist"><a href="{$config->userFrameworkBaseURL}?q=admin/user/permissions&destination=civicrm/admin/configtask">{ts}Permissions for Anonymous Users{/ts}</a></td>  
        <td>{ts}You will also need to change Drupal permissions so anonymous users can make contributions, register for events and / or use profiles to enter contact information.{/ts} {docURL page="Default Permissions and Roles"}</li>
    </tr>
{/if}
</table>
<br />

<div class="description">
{ts 1=$adminMenu}The next set of tasks involve planning and have multiple steps. You may want to check out the {docURL page="Getting Started" text="Getting Started"} documentation before you begin. You will
not be returned to this page after completing these tasks, but you can always get back here from the <a href="%1">Administer CiviCRM</a> menu.{/ts}
</div>  

<table class="selector">
<tr class="columnheader">
    <td colspan="2">
         <div id="ctCollapsed"><a class="pane-collapsed" href="#" onclick="show('ct1','table-row');show('ct2','table-row'); hide('ctCollapsed'); show('ctExpanded');">{ts}Organize your contacts{/ts}</a></div>
         <div id="ctExpanded"><a class="pane-expanded" href="#" onclick="hide('ct1','table-row');hide('ct2','table-row'); show('ctCollapsed'); hide('ctExpanded');">{ts}Organize your contacts{/ts}</a></div>
    </td>
</tr>
<tr class="even" id="ct1">
    <td class="tasklist nowrap"><a href="{crmURL p="civicrm/admin/tag" q="reset=1&destination=`$destination`"}" title="{$linkTitle}">{ts}Tags (Categories){/ts}</a></td>
    <td>{ts}Tags can be assigned to any contact record, and are a convenient way to find contacts. You can create as many tags as needed to organize and segment your records.{/ts}</td>
</tr>
<tr class="even" id="ct2">
    <td class="tasklist nowrap"><a href="{crmURL p="civicrm/group" q="reset=1&destination=`$destination`"}" title="{$linkTitle}">{ts}Manage Groups{/ts}</a></td>
    <td>{ts}Use Groups to organize contacts (e.g. these contacts are members of our 'Steering Committee').{/ts}</li>
</tr>

<tr class="columnheader">
    <td colspan="2">
         <div id="cuCollapsed"><a class="pane-collapsed" href="#" onclick="show('cu1','table-row');show('cu2','table-row'); hide('cuCollapsed'); show('cuExpanded');">{ts}Customize Data, Forms and Screens{/ts}</a></div>
         <div id="cuExpanded"><a class="pane-expanded" href="#" onclick="hide('cu1','table-row');hide('cu2','table-row'); show('cuCollapsed'); hide('cuExpanded');">{ts}Customize Data, Forms and Screens{/ts}</a></div>
    </td>
</tr>
<tr class="even" id="cu1">
    <td class="tasklist nowrap"><a href="{crmURL p="civicrm/admin/custom/group" q="reset=1&destination=`$destination`"}" title="{$linkTitle}">{ts}Custom Data{/ts}</a></td>
    <td>{ts}Configure custom fields to collect and store custom data which is not included in the standard CiviCRM forms.{/ts}</td>
</tr>
<tr class="even" id="cu2">
    <td class="tasklist nowrap"><a href="{crmURL p="civicrm/admin/uf/group" q="reset=1&destination=`$destination`"}" title="{$linkTitle}">{ts}Profiles{/ts}</a></td>
    <td>{ts}Profiles allow you to aggregate groups of fields and include them in your site as input forms, contact display pages, and search and listings features.{/ts}</li>
</tr>
</table>
<br />

<div class="description">{ts}Now you can move on to exploring, configuring and using the various optional components for fundraising and constituent engagement. The links below will take you to the online documentation for each component.{/ts}</div>
<table class="selector">
<tr class="columnheader">
    <td colspan="2">
         <div id="coCollapsed"><a class="pane-collapsed" href="#" onclick="show('co1','table-row');show('co2','table-row');show('co3','table-row');show('co4','table-row');show('co5','table-row');show('co6','table-row'); hide('coCollapsed'); show('coExpanded');">{ts}Components{/ts}</a></div>
         <div id="coExpanded"><a class="pane-expanded" href="#" onclick="hide('co1','table-row');hide('co2','table-row');hide('co3','table-row');hide('co4','table-row');hide('co5','table-row');hide('co6','table-row'); show('coCollapsed'); hide('coExpanded');">{ts}Components{/ts}</a></div>
    </td>
</tr>
<tr class="even" id="co1">
    <td class="tasklist nowrap" style="width: 10%;">{docURL page="CiviContribute Admin" text="CiviContribute"}</td>
    <td>{ts}Online fundraising and donor management, as well as offline contribution processing and tracking.{/ts}</td>    
</tr>
<tr class="even" id="co2">
    <td class="tasklist nowrap" style="width: 10%;">{docURL page="Manage Pledges" text="CiviPledge"}</td>
    <td>{ts}Accept and track pledges (for recurring gifts).{/ts}</td>    
</tr>
<tr class="even" id="co3">
    <td class="tasklist nowrap">{docURL page="CiviEvent Admin" text="CiviEvent"}</td>
    <td>{ts}Online event registration and participant tracking.{/ts}</td>
</tr>
<tr class="even" id="co4">
    <td class="tasklist nowrap">{docURL page="CiviMember Admin" text="CiviMember"}</td>
    <td>{ts}Online signup and membership management.{/ts}</td>
</tr>
<tr class="even" id="co5">
    <td class="tasklist nowrap">{docURL page="CiviMail Admin" text="CiviMail"}</td>
    <td>{ts}Personalized email blasts and newsletters.{/ts}</td>
</tr>
<tr class="even" id="co6">
    <td class="tasklist nowrap">{docURL page="CiviCase Admin" text="CiviCase"}</td>
    <td>{ts}Integrated case management for human service providers{/ts}</td>
</tr>
</table>

{include file="CRM/common/showHide.tpl" elemType="table-row"}
