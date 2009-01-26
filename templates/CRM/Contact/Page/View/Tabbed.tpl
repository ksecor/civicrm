{* Contact Summary template for new tabbed interface. Replaces Basic.tpl *}
{if $action eq 2}
  {include file="CRM/Contact/Form/Edit.tpl"}
{else}
<div id="mainTabContainer" dojoType="dijit.layout.TabContainer" class ="tundra" style="width: 100%; height: 600px; overflow-y: auto;" >
<div id="summary" dojoType="dojox.layout.ContentPane" title="{ts}Summary{/ts}" class ="tundra" style="overflow: auto; width: 100%; height: 100%;">
{* View Contact Summary *}
<div id="contact-name" class="section-hidden section-hidden-border">
   <div>
    <label><span class="font-size12pt">{$displayName}</span></label>
      {if $nick_name}&nbsp;&nbsp;({$nick_name}){/if}
      {if $legal_name}&nbsp;&nbsp;({$legal_name}){/if}
    {if $permission EQ 'edit'}
        &nbsp; &nbsp; <input type="button" accesskey="E" value="{ts}Edit{/ts}" name="edit_contact_info" onclick="window.location='{crmURL p='civicrm/contact/add' q="reset=1&action=update&cid=$contactId"}';"/>
    {/if}
    &nbsp; &nbsp; <input type="button" value="{ts}vCard{/ts}" name="vCard_export" onclick="window.location='{crmURL p='civicrm/contact/view/vcard' q="reset=1&cid=$contactId"}';"/>
    &nbsp; &nbsp; <input type="button" value="{ts}Print{/ts}" name="contact_print" onclick="window.location='{crmURL p='civicrm/contact/view/print' q="reset=1&print=1&cid=$contactId"}';"/>
    {if $permission EQ 'edit'}
        &nbsp; &nbsp; <input type="button" value="{ts}Delete{/ts}" name="contact_delete" onclick="window.location='{crmURL p='civicrm/contact/view/delete' q="reset=1&delete=1&cid=$contactId"}';"/>
    {/if}
    {if $dashboardURL } &nbsp; &nbsp; <a href="{$dashboardURL}">&raquo; {ts}View Contact Dashboard{/ts}</a> {/if}
    {if $url } &nbsp; &nbsp; <a href="{$url}">&raquo; {ts}View User Record{/ts}</a> {/if}

    <table class="form-layout-compressed">
    <tr>
        {if $source}<td><label>{ts}Source{/ts}:</label></td><td>{$source}</td>{/if}
        {if $sic_code}<td><label>{ts}SIC Code{/ts}:</label></td><td>{$sic_code}</td>{/if}
        <td id='tagLink'><label><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=$contactId&selectedChild=tag"}" title="{ts}Edit Tags{/ts}">{ts}Tags{/ts}</a>:</label></td><td id="tags">{$contactTag}</td>
    </tr>
    <tr>
        {if $job_title}<td><label>{ts}Job Title{/ts}:</label></td><td>{$job_title}</td>{/if}
        {if $current_employer}<td><label>{ts}Current Employer{/ts}:</label></td><td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$current_employer_id`"}" title="{ts}view current employer{/ts}">{$current_employer}</a></td>{/if}
        {if $home_URL}<td><label>{ts}Website{/ts}</label></td><td><a href="{$home_URL}" target="_blank">{$home_URL}</a></td>{/if}
        {if !$current_employer}<td colspan="2"></td>{/if}
        {if !$home_URL}<td colspan="2"></td>{/if}
    </tr>
    </table>
   </div>
</div>

{* Include links to enter Activities if session has 'edit' permission *}

{if $permission EQ 'edit'}
    {include file="CRM/Activity/Form/ActivityLinks.tpl"}
{/if}

{* Display populated Locations. Primary location expanded by default. *}
{foreach from=$location item=loc key=locationIndex}

<div id="location_{$locationIndex}_show" class="section-hidden section-hidden-border">
  <a href="#" onclick="hide('location_{$locationIndex}_show'); show('location_{$locationIndex}'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{$loc.location_type}{if $loc.name} - {$loc.name}{/if}{if $locationIndex eq 1} {ts}(primary location){/ts}{/if}</label>
  {if $preferred_communication_method_display eq 'Email' AND $loc.email.1.email}&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <label>{ts}Preferred Email:{/ts}</label> {$loc.email.1.email}
  {elseif $preferred_communication_method_display eq 'Phone' AND $loc.phone.1.phone}&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <label>{ts}Preferred Phone:{/ts}</label> {$loc.phone.1.phone}{/if}
</div>

<div id="location_{$locationIndex}" class="section-shown">
  <fieldset>
   <legend{if $locationIndex eq 1} class="label"{/if}>
    <a href="#" onclick="hide('location_{$locationIndex}'); show('location_{$locationIndex}_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{$loc.location_type}{if $loc.name} - {$loc.name}{/if}{if $locationIndex eq 1} {ts}(primary location){/ts}{/if}
   </legend>

  <div class="col1">
   {foreach from=$loc.phone item=phone}
     {if $phone.phone}
        {if $phone.is_primary eq 1}<strong>{/if}
        {if $phone.phone}{if $phone.phone_type}{$phone.phone_type}{else}{ts}Phone{/ts}{/if}:{/if} {$phone.phone}
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
	  {/if}
      {if $email.is_bulkmail}
	    <span class="status-hold">&nbsp;(Bulk Mailings)</span>
	  {/if}
	<br />
   {/foreach}

   {foreach from=$loc.im item=im key=imKey}
     {if $im.name or $im.provider}
        {if $im.is_primary eq 1}<strong>{/if}
        {ts}Instant Messenger:{/ts} {if $im.name}{$im.name}{/if} {if $im.provider}( {$im.provider} ) {/if}
        {if $im.is_primary eq 1}</strong>{/if}
        <br />
     {/if}
   {/foreach}

  {foreach from=$loc.user_unique_id item=user_unique_id}
    {if $user_unique_id.user_unique_id}
      {if $user_unique_id.is_primary eq 1}<strong>{/if}
      {ts}User_Unique_Id:{/ts} {if $user_unique_id.user_unique_id}{$user_unique_id.user_unique_id}{/if}
      {if $user_unique_id.is_primary eq 1}</strong>{/if}
     </br>
    {/if}
  {/foreach}
{foreach from=$loc.openid item=openid}
     {if $openid.openid}
        {ts}OpenID:{/ts} <a href="{$openid.openid}">{$openid.openid|mb_truncate:40}</a>
        {*if $email.is_primary eq 1}</strong>{/if*}
     {/if}
     {if $config->userFramework eq "Standalone" }
	{if $openid.allowed_to_login eq 1}		
	{ts}(Allowed to login){/ts}
	{/if}
     {/if} 	
     <br />
{/foreach}
   </div>
   <div class="col2">
    {*if $config->mapAPIKey AND $loc.is_primary AND $loc.address.geo_code_1 AND $loc.address.geo_code_2*}
    {if $config->mapAPIKey AND $loc.address.geo_code_1 AND $loc.address.geo_code_2}
        <a href="{crmURL p='civicrm/contact/map' q="reset=1&cid=`$contactId`&lid=`$loc.location_type_id`"}" title="{ts}Map Primary Address{/ts}">{ts}Map this Address{/ts}</a><br />
    {/if}
    {if $HouseholdName and $locationIndex eq 1}
    <strong>Household Address:</strong><br />
    <a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$mail_to_household_id`"}">{$HouseholdName}</a>{/if}
    {$loc.address.display|nl2br}
  </div>
  <div class="spacer"></div>
  </fieldset>
</div>
{/foreach}

{if $showCommBlock}
<div id="commPrefs_show" class="section-hidden section-hidden-border">
  <a href="#" onclick="hide('commPrefs_show'); show('commPrefs'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}Communications Preferences{/ts}</label><br />
 </div>

<div id="commPrefs" class="section-shown">
 <fieldset>
  <legend><a href="#" onclick="hide('commPrefs'); show('commPrefs_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}Communications Preferences{/ts}</legend>
  <div class="col1">
    <label>{ts}Privacy:{/ts}</label>
    <span class="font-red upper">
    {foreach from=$privacy item=privacy_val key=privacy_label}
      {if $privacy_val eq 1}{$privacy_values.$privacy_label} &nbsp; {/if}
    {/foreach}
    {if $is_opt_out}
      {ts}No Bulk Emails (User Opt Out){/ts}
    {/if}
    </span>
  </div>
  <div class="col2">
    <label>{ts}Method:{/ts}</label> {$preferred_communication_method_display}
  </div>
  <div class="col2">
    <label>{ts}Email Format Preference:{/ts}</label> {$preferred_mail_format_display}
  </div>
  <div class="spacer"></div>
 </fieldset>
</div>
{/if}

{if $contact_type eq 'Individual' AND $showDemographics}
<div id="demographics_show" class="section-hidden section-hidden-border">
  <a href="#" onclick="hide('demographics_show'); show('demographics'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}Demographics{/ts}</label><br />
 </div>

<div id="demographics" class="section-shown">
  <fieldset>
   <legend><a href="#" onclick="hide('demographics'); show('demographics_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}Demographics{/ts}</legend>
   <div class="col1">
    <label>{ts}Gender:{/ts}</label> {$gender_display}<br />
    {if $is_deceased eq 1}
        <label>{ts}Contact is Deceased{/ts}</label><br />
    {/if}
    {if $deceased_date}
        <label>{ts}Date Deceased:{/ts}</label> {$deceased_date|crmDate}
    {/if}
   </div>
   <div class="col2">
    <label>{ts}Date of Birth:{/ts}</label> {$birth_date|crmDate}<br />
    {* Show calculated age unless contact is deceased. *}
    {if $is_deceased neq 1}
        {if $age.y}  
        <label>{ts}Age{/ts}:</label> {ts count=$age.y plural='%count years'}%count year{/ts}<br />
        {/if}
        {if $age.m} 
        <label>{ts}Age{/ts}:</label> {ts count=$age.m plural='%count months'}%count month{/ts}<br />
        {/if}
    {/if}
    </div>
   <div class="spacer"></div>
  </fieldset>
 </div>
 {/if}

 {include file="CRM/Custom/Page/CustomDataView.tpl"}
</div>

{foreach from=$allTabs key=tabName item=tabValue}
  <div id="{$tabValue.id}" dojoType="dojox.layout.ContentPane" href="{$tabValue.url}" title="{$tabValue.title}" 
class ="tundra" {if $tabValue.id eq $selectedChild} selected="true"{/if} style="overflow: auto; width: 100%; height:100%;"></div>
{/foreach}
</div>


 <script type="text/javascript">
   {if !$contactTag}cj("#tagLink").hide( );{/if}    
{literal}
   init_blocks = function( ) {
{/literal}
      var showBlocks = new Array({$showBlocks});
      var hideBlocks = new Array({$hideBlocks});
{literal}
      on_load_init_blocks( showBlocks, hideBlocks );
  }
  dojo.addOnLoad( init_blocks );
{/literal}
 </script>

{/if}
