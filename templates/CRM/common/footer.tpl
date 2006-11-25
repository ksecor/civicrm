{include file="CRM/common/version.tpl" assign=version}

<div class="footer" id="record-log"> 


{if $lastModified}
<span class="col1">{if $external_identifier}{ts}External ID{/ts}:&nbsp;{$external_identifier}{/if}&nbsp; &nbsp;{ts}CiviCRM ID{/ts}:&nbsp;{$contact_id}</span>
{ts}Last Change by{/ts} <a href="{crmURL p='civicrm/contact/view' q="action=view&reset=1&cid=`$lastModified.id`"}">{$lastModified.name}</a> ({$lastModified.date|crmDate}) &nbsp; <a href="{crmURL p='civicrm/contact/view/log' q="reset=1&action=browse&cid=`$contactId`"}">&raquo; {ts}View Change Log{/ts}</a>{/if}</div>

<div class="footer" id="civicrm-footer"> 
{ts 1=$version 2='http://www.affero.org/oagpl.html' 3='http://civicrm.org/download' 4='http://issues.civicrm.org/jira/browse/CRM?report=com.atlassian.jira.plugin.system.project:roadmap-panel' 5='http://wiki.civicrm.org/confluence/display/CRM/CiviCRM+Documentation'}Powered by CiviCRM %1. CiviCRM is openly available under the <a href="%2">Affero General Public License (AGPL)</a>. <a href="%3">Download source</a>. <a href="%4">View issues and report bugs</a>. <a href="%5">Online documentation</a>.{/ts}
</div> 
