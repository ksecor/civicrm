{capture assign=docURLTitle}{ts}Opens online documentation in a new window.{/ts}{/capture}
<div id="help">
    {ts 1="http://wiki.civicrm.org/confluence//x/OSk" 2=$docURLTitle}Use the links below to configure or modify the global settings for CiviCRM for this site. Refer to the <a href='%1' target='_blank' title='%2'>Administration Documentation</a> for details on settings and options.{/ts}
</div>
<table class="report"> 
<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/admin/setting/component' q='reset=1'}" id="idComponents">&raquo; {ts}Enable Components{/ts}</a></td>
    <td>{ts}Enable CiviContribute, CiviPledge, CiviEvent, CiviMember, CiviGrant and/or CiviMail components.{/ts}</td>
</tr>

<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/admin/setting/preferences/display' q='reset=1'}" id="idPreferences">&raquo; {ts}Site Preferences{/ts}</a></td>
    <td>{ts}Configure screen and form elements for Viewing Contacts, Editing Contacts, Advanced Search, Contact Dashboard and WYSIWYG Editor.{/ts}</td>
</tr>

<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/admin/setting/path' q='reset=1'}" id="idPath">&raquo; {ts}Directories{/ts}</a></td>
    <td>{ts}Configure directories in your file system for temporary uploads, images, custom files and custom templates.{/ts}</td>
</tr>

<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/admin/setting/url' q='reset=1'}" id="idUrls">&raquo; {ts}Resource URLs{/ts}</a></td>
    <td>{ts}URLs used to access CiviCRM resources (CSS files, Javascript files, images, etc.). Enable secure URLs.{/ts}</td>
</tr>

<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/admin/setting/smtp' q='reset=1'}" id="idSMTP">&raquo; {ts}Outbound Email (SMTP/Sendmail){/ts}</a></td>
    <td>{ts}Settings for outbound email. SMTP server, port and authentication. Or SENDMAIL path and argument.{/ts}</td>
</tr>

<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/admin/setting/mapping' q='reset=1'}" id="idMapping">&raquo; {ts}Mapping and Geocoding{/ts}</a></td>
    <td>{ts}Configure a mapping provider (e.g. Google or Yahoo) to display maps for contact addresses.{/ts}</td>
</tr>

<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/admin/paymentProcessor' q='reset=1'}" id="idPayments">&raquo; {ts}Payment Processors{/ts}</a></td>
    <td>{ts}Select and configure one or more payment processing services for online contributions, events and / or membership fees.{/ts}</td>
</tr>

<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/admin/setting/localization' q='reset=1'}" id="idLocale">&raquo; {ts}Localization{/ts}</a></td>
    <td>{ts}Localization settings include user language, default currency and available countries for address input.{/ts}</td>
</tr>

<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/admin/setting/preferences/address' q='reset=1'}" id="idAddress">&raquo; {ts}Address Settings{/ts}</a></td>
    <td>{ts}Format addresses in mailing labels, input forms and screen display. Configure optional Address Standardization provider.{/ts}</td>
</tr>

<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/admin/setting/date' q='reset=1'}" id="idDates">&raquo; {ts}Date Formats{/ts}</a></td>
    <td>{ts}Configure input and display formats for Date fields.{/ts}</td>
</tr>

{if $config->userFramework != 'Standalone'}
<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/admin/setting/uf' q='reset=1'}" id="idUF">&raquo; {ts 1=$config->userFramework}%1 Integration Settings{/ts}</a></td>
    <td>{ts 1=$config->userFramework}%1 version and user table name.{/ts}</td>
</tr>
{/if}

<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/admin/setting/misc' q='reset=1'}" id="idMisc">&raquo; {ts}Miscellaneous Settings{/ts}</a></td>
    <td>{ts}Enable version checking and configure optional CAPTCHA feature for Profile forms.{/ts}</td>
</tr>

<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/admin/setting/debug' q='reset=1'}" id="idDebug">&raquo; {ts}Debugging{/ts}</a></td>
    <td>{ts}Enable debugging features including display of template variables and backtracing.{/ts}</td>
</tr>
</table>
