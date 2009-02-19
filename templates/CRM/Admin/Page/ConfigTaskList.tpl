{capture assign="linkTitle"}{ts}Edit settings{/ts}{/capture}
{capture assign="destination"}{crmURL p="civicrm/admin/configtask" q="reset=1"}{/capture}
<div id="help" class="description section-hidden-border">
{ts}Use this checklist to review and complete configuration tasks for your site. You will be redirected back to this checklist after saving from most settings pages. Settings which you have not yet
reviewed will be <span class="status-overdue">displayed in red</span>. After you've visited a page, the links will <span class="status-pending">display in green</span>  (although you may still need to revisit the page to complete or update the settings).{/ts}
</div>

<table class="form-layout">
<tr><td><span class="summary">{ts}Site configuration{/ts}</span></td></tr>
<tr><td<ul class="tasklist">
<li><a href="{crmURL p="civicrm/admin/setting/localization" q="reset=1&destination=`$destination`"}" title="{$linkTitle}">{ts}Localization{/ts}</a> - {ts}Localization settings include user language, default currency and available countries for address input.{/ts}</li>
<li><a href="{crmURL p="civicrm/contact/domain" q="action=update&reset=1&destination=`$destination`"}" title="{$linkTitle}">{ts}Domain Information{/ts}</a> - {ts}Organization name, email address for system-generated emails, organization address{/ts}</li>
</ul>
</td></tr>

<tr><td<h2>Viewing and Editing Contacts</h2></td></tr>
<tr><td<ul class="tasklist">
<li><a href="{crmURL p="civicrm/admin/setting/preferences/display" q="reset=1&destination=`$destination`"}" title="{$linkTitle}">{ts}Site Preferences{/ts}</a> - {ts}Configure screen and form elements for Viewing Contacts, Editing Contacts, Advanced Search, Contact Dashboard and WYSIWYG Editor{/ts}</li>
<li><a href="{crmURL p="civicrm/admin/setting/preferences/address" q="reset=1&destination=`$destination`"}" title="{$linkTitle}">{ts}Address Settings{/ts}</a> - {ts}Format addresses in mailing labels, input forms and screen display{/ts}</li>
<li><a href="{crmURL p="civicrm/admin/setting/mapping" q="reset=1&destination=`$destination`"}" title="{$linkTitle}">{ts}Mapping and Geocoding{/ts}</a> - {ts}Configure a mapping provider (e.g. Google or Yahoo) to display maps for contact addresses and event locations.{/ts}</li>
<li><a href="{crmURL p="civicrm/admin/setting/misc" q="reset=1&destination=`$destination`"}" title="{$linkTitle}">{ts}Miscellaneous{/ts}</a> - {ts}Contact search behaviors, RECAPTCHA configuration{/ts}</li>
</ul>
</td></tr>

<tr><td<h2>Sending Emails (includes contribution receipts and event confirmations)</h2></td></tr>
<tr><td<ul class="tasklist">
<li><a href="{crmURL p="civicrm/admin/setting/smtp" q="reset=1&destination=`$destination`"}" title="{$linkTitle}">{ts}Outbound Email{/ts}</a> - {ts}Settings for outbound email - either SMTP server, port and authentication or Sendmail path and argument.{/ts}</li>
<li><a href="{crmURL p="civicrm/admin/options/from_email_address" q="group=from_email_address&reset=1&destination=`$destination`"}" title="{$linkTitle}">{ts}From Email Addresses{/ts}</a> - {ts}Define general email address(es) that can be used as the FROM address when sending email to contacts from within CiviCRM (e.g. info@example.org){/ts}</li>
</ul>
</td></tr>

<tr><td<h2>Online Contributions / Online Membership Signup / Online Event Registration</h2></td></tr>
<tr><td<ul class="tasklist">
<li><a href="{crmURL p="civicrm/admin/paymentProcessor" q="reset=1&destination=`$destination`"}" title="{$linkTitle}">{ts}Payment Processors{/ts}</a> - {ts}Select and configure one or more payment processing services for online contributions, events and / or membership fees.{/ts}</li>
{if $config->userFramework EQ 'Drupal'}
<li><a href="{$config->userFrameworkBaseURL}?q=admin/user/permissions&destination=civicrm/admin/configtask">{ts}Permissions for Anonymous Users{/ts}</a> - {ts}You will also need to change Drupal permissions so anonymous users can make contributions, register for events and / or use profiles to enter contact information.{/ts} {docURL page="Default Permissions and Roles"}</li>
{/if}
</ul>
</td></tr>

<tr><td<h2>Organize your contacts</h2></td></tr>
<tr><td<ul class="tasklist">
<li><a href="{crmURL p="civicrm/admin/tag" q="reset=1&destination=`$destination`"}" title="{$linkTitle}">{ts}Tags (Categories){/ts}</a> - {ts}Tags can be assigned to any contact record, and are a convenient way to find contacts. You can create as many tags as needed to organize and segment your records.{/ts}</li>
<li><a href="{crmURL p="civicrm/group" q="reset=1&destination=`$destination`"}" title="{$linkTitle}">{ts}Manage Groups{/ts}</a> - {ts}Use Groups to organize contacts (e.g. these contacts are members of our 'Steering Committee').{/ts}</li>
</ul>
</td></tr>

<tr><td<h2>Customize Data, Forms and Screens</h2></td></tr>
<tr><td<ul class="tasklist">
<li><a href="{crmURL p="civicrm/admin/custom/group" q="reset=1&destination=`$destination`"}" title="{$linkTitle}">{ts}Custom Data{/ts}</a> - {ts}Configure custom fields to collect and store custom data which is not included in the standard CiviCRM forms.{/ts}</li>
<li><a href="{crmURL p="civicrm/admin/uf/group" q="reset=1&destination=`$destination`"}" title="{$linkTitle}">{ts}Profiles{/ts}</a> - {ts}Profiles allow you to aggregate groups of fields and include them in your site as input forms, contact display pages, and search and listings features.{/ts}</li>
</ul>
</td></tr>

<tr><td<h2>Components</h2></td></tr>
{ts}Once you've reviewed and updated these settings, you can move on to exploring, configuring and using the various optional components for fundraising and constituent engagement. The links below will take you to the online documentation for each component.{/ts}
<tr><td<ul class="tasklist">
<li>{docURL page="CiviContribute Admin" text="CiviContribute and CiviPledge"}</li>
<li>{docURL page="CiviEvent Admin" text="CiviEvent"}</li>
<li>{docURL page="CiviMember Admin" text="CiviMember"}</li>
<li>{docURL page="CiviMail Admin" text="CiviMail"}</li>
<li>{docURL page="CiviCase Admin" text="CiviCase"}</li>
</ul>
</td></tr>
</table>
