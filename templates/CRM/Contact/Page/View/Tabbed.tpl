{if $action eq 2}
  {include file="CRM/Contact/Form/Edit.tpl"}
{else}
{include file="CRM/common/dojo.tpl"}
<div id="mainTabContainer" dojoType="TabContainer" style="width: 100%; height: 600px" selectedChild="{$selectedChild}">

<div id="summary" dojoType="ContentPane" label="Summary">

{* View Contact Summary *}
<div id="contact-name" class="data-group">
   <div>
    {if $source}<label>{ts}Source{/ts}:</label>&nbsp;{$source}<br />{/if}
    <label>{$displayName}</label>
    {if $contact_type eq 'Individual' && $job_title}&nbsp;&nbsp;{ts}Job Title{/ts}:&nbsp;{$job_title}
    {elseif $home_URL}&nbsp; &nbsp; <a href="{$home_URL}" target="_blank">{$home_URL}</a>{/if}
    {if $permission EQ 'edit'}
        &nbsp; &nbsp; <input type="button" value="{ts}Edit{/ts}" name="edit_contact_info" onclick="window.location='{crmURL p='civicrm/contact/add' q="reset=1&action=update&cid=$contactId"}';"/>




    {/if}
    &nbsp; &nbsp; <input type="button" value="{ts}vCard{/ts}" name="vCard_export" onclick="window.location='{crmURL p='civicrm/contact/view/vcard' q="reset=1&cid=$contactId"}';"/>
    {if $permission EQ 'edit'}
        &nbsp; &nbsp; <input type="button" value="{ts}Delete{/ts}" name="contact_delete" onclick="window.location='{crmURL p='civicrm/contact/view/delete' q="reset=1&delete=1&cid=$contactId"}';"/>
    {/if}
    {if $url } &nbsp; &nbsp; <a href="{$url}">&raquo; {ts}View User Record{/ts}</a> {/if}
    {if $contactTag}<br />{ts}Tags{/ts}:&nbsp;{$contactTag}{/if}
   </div>
</div>

{* Include links to enter Activities if session has 'edit' permission *}

{if $permission EQ 'edit'}
    {include file="CRM/Contact/Page/View/ActivityLinks.tpl"}
{/if}

{* Display populated Locations. Primary location expanded by default. *}
{foreach from=$location item=loc key=locationIndex}

 <div id="location_{$locationIndex}_show" class="data-group">
  <a href="#" onclick="hide('location_{$locationIndex}_show'); show('location_{$locationIndex}'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{$loc.location_type}{if $loc.name} - {$loc.name}{/if}{if $locationIndex eq 1} {ts}(primary location){/ts}{/if}</label>
  {if $preferred_communication_method_display eq 'Email'}&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <label>{ts}Preferred Email:{/ts}</label> {$loc.email.1.email}
  {elseif $preferred_communication_method_display eq 'Phone'}&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <label>{ts}Preferred Phone:{/ts}</label> {$loc.phone.1.phone}{/if}
 </div>

 <div id="location_{$locationIndex}">
  <fieldset>
   <legend{if $locationIndex eq 1} class="label"{/if}>
    <a href="#" onclick="hide('location_{$locationIndex}'); show('location_{$locationIndex}_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{$loc.location_type}{if $loc.name} - {$loc.name}{/if}{if $locationIndex eq 1} {ts}(primary location){/ts}{/if}
   </legend>

  <div class="col1">
   {foreach from=$loc.phone item=phone}
     {if $phone.phone}
        {if $phone.is_primary eq 1}<strong>{/if}
        {if $phone.phone_type}{$phone.phone_type_display}:{/if} {$phone.phone}
        {if $phone.is_primary eq 1}</strong>{/if}
        <br />
     {/if}
   {/foreach}

   {foreach from=$loc.email item=email}
      {if $email.email}
        {if $email.is_primary eq 1}<strong>{/if}
        {ts}Email:{/ts} <a href="mailto:{$email.email}">{$email.email}</a>
        {if $email.is_primary eq 1}</strong>{/if}
        {/if}
        {if $email.on_hold}
	    <span class="status-hold">&nbsp;(On Hold)</span>
	    <br />
        {/if}
   {/foreach}

   {foreach from=$loc.im item=im key=imKey}
     {if $im.name or $im.provider}
        {if $im.is_primary eq 1}<strong>{/if}
        {ts}Instant Messenger:{/ts} {if $im.name}{$im.name}{/if} {if $im.provider}( {$im.provider} ) {/if}
        {if $im.is_primary eq 1}</strong>{/if}
        <br />
     {/if}
   {/foreach}
   </div>

   <div class="col2">

    {*if $config->mapAPIKey AND $loc.is_primary AND $loc.address.geo_code_1 AND $loc.address.geo_code_2*}
    {if $config->mapAPIKey AND $loc.address.geo_code_1 AND $loc.address.geo_code_2}
        <a href="{crmURL p='civicrm/contact/search/map' q="reset=1&cid=$contactId&lid=`$loc.address.location_id`"}" title="{ts}Map Primary Address{/ts}">{ts}Map this Address{/ts}</a><br />
    {/if}
    {$loc.address.display|nl2br}
  </div>
  <div class="spacer"></div>
  </fieldset>
 </div>
{/foreach}

 <div id="commPrefs_show" class="data-group">
  <a href="#" onclick="hide('commPrefs_show'); show('commPrefs'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}Communications Preferences{/ts}</label><br />
 </div>

<div id="commPrefs">
 <fieldset>
  <legend><a href="#" onclick="hide('commPrefs'); show('commPrefs_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}Communications Preferences{/ts}</legend>
  <div class="col1">
    <label>{ts}Privacy:{/ts}</label>
    <span class="font-red upper">
    {foreach from=$privacy item=privacy_val key=privacy_label}
      {if $privacy_val eq 1}{$privacy_values.$privacy_label} &nbsp; {/if}
    {/foreach}
    {if $is_opt_out}
      {ts}DO NOT SEND BULK EMAIL{/ts}
    {/if}
    </span>
  </div>
  <div class="col2">
    <label>{ts}Communication Preference:{/ts}</label> {$preferred_communication_method_display}
  </div>
  <div class="col2">
    <label>{ts}Mail Format Preference:{/ts}</label> {$preferred_mail_format_display}
  </div>
  <div class="spacer"></div>
 </fieldset>
</div>


 {if $contact_type eq 'Individual'}
 <div id="demographics_show" class="data-group">
  <a href="#" onclick="hide('demographics_show'); show('demographics'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}Demographics{/ts}</label><br />
 </div>

 <div id="demographics">
  <fieldset>
   <legend><a href="#" onclick="hide('demographics'); show('demographics_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}Demographics{/ts}</legend>
   <div class="col1">
    <label>{ts}Gender:{/ts}</label> {$gender_display}<br />
    {if $is_deceased eq 1}
        <label>{ts}Contact is Deceased{/ts}</label>
    {/if}
   </div>
   <div class="col2">
    <label>{ts}Date of Birth:{/ts}</label> {$birth_date|crmDate}<br />
    {if $deceased_date}
        <label>{ts}Date Deceased:{/ts}</label> {$deceased_date|crmDate}
    {/if}
   </div>
   <div class="spacer"></div>
  </fieldset>
 </div>
 {/if}

 {include file="CRM/Contact/Page/View/InlineCustomData.tpl"}
</div>

{foreach from=$allTabs key=tabName item=tabValue}
  <div id="{$tabValue.id}" dojoType="ContentPane" href="{$tabValue.url}" label="{$tabName}" style="display: none" adjustPaths="false"></div>
{/foreach}
</div>

{literal}
 <script type="text/javascript">

   init_blocks = function( ) {
{/literal}
      var showBlocks = new Array({$showBlocks});
      var hideBlocks = new Array({$hideBlocks});
{literal}
      on_load_init_blocks( showBlocks, hideBlocks );
  }

  dojo.addOnLoad( init_blocks );
 </script>
{/literal}

{/if}
