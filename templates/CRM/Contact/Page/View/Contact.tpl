{* View Contact Summary *}
<div id="name" class="data-group">
   <div class="float-right">
        <a href="{crmURL p='civicrm/contact/edit' q="reset=1&cid=$contact_id"}">&raquo; {ts}Edit address, phone, email...{/ts}</a>&nbsp;&nbsp;&nbsp;
   </div>
   <div>
    {if $contact_type eq 'Individual'}
        <label>{$prefix} {$display_name} {$suffix}</label> &nbsp; &nbsp; {$job_title}
    {else}
        <label>{$sort_name}</label>
    {/if}
    {if $contactTag}<br />{$contactTag}{/if}
   </div>
</div>


{* Display populated Locations. Primary location expanded by default. *}
{foreach from=$location item=loc key=locationIndex}

 <div id="location[{$locationIndex}][show]" class="data-group">
  <a href="#" onClick="hide('location[{$locationIndex}][show]'); show('location[{$locationIndex}]'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"></a><label>{$loc.location_type}{if $locationIndex eq 1} (primary location){/if}</label><br />
 </div>

 <div id="location[{$locationIndex}]">
  <fieldset>
   <legend{if $locationIndex eq 1} class="label"{/if}><a href="#" onClick="hide('location[{$locationIndex}]'); show('location[{$locationIndex}][show]'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"></a>{$loc.location_type}{if $locationIndex eq 1} {ts}(primary location){/ts}{/if}<legend/>

  <div class="col1">
    {if $loc.address.street_address}{$loc.address.street_address}<br />{/if}
    {if $loc.address.supplemental_address_1}{$loc.address.supplemental_address_1}<br />{/if}
    {if $loc.address.city OR $loc.address.state_province OR $loc.address.postal_code}
        {if $loc.address.city}{$loc.address.city},{/if} {$loc.address.state_province} {$loc.address.postal_code}<br />
    {/if}
    {$loc.address.country}
  </div>
  
  <div class="col2">
   {foreach from=$loc.phone item=phone}
     {if $phone.phone}
        {if $phone.is_primary eq 1}<strong>{/if}
        {if $phone.phone_type}{$phone.phone_type}:{/if} {$phone.phone}
        {if $phone.is_primary eq 1}</strong>{/if}
        <br />
     {/if}
   {/foreach}

   {foreach from=$loc.email item=email}
      {if $email.email}
        {if $email.is_primary eq 1}<strong>{/if}
        {ts}Email:{/ts} {$email.email}
        {if $email.is_primary eq 1}</strong>{/if}
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
  </fieldset>
 </div>
{/foreach}

 <div id="commPrefs[show]" class="data-group">
  <a href="#" onClick="hide('commPrefs[show]'); show('commPrefs'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"></a><label>{ts}Communications Preferences{/ts}</label><br />
 </div>

<div id="commPrefs">
 <fieldset>
  <legend><a href="#" onClick="hide('commPrefs'); show('commPrefs[show]'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"></a>{ts}Communications Preferences{/ts}</legend>
  <div class="col1">
    <label>{ts}Privacy:{/ts}</label>
    <span class="font-red">
    {foreach from=$privacy item=privacy_val key=privacy_label}
      {if $privacy_val eq 1}{$privacy_label|replace:"_":" "|upper} &nbsp; {/if}
    {/foreach}
    </span>
  </div>
  <div class="col2">
    <label>{ts}Prefers:{/ts}</label> {$preferred_communication_method}
  </div>
 </fieldset>
</div>

 {if $contact_type eq 'Individual'}
 <div id="demographics[show]" class="data-group">
  <a href="#" onClick="hide('demographics[show]'); show('demographics'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"></a><label>{ts}Demographics{/ts}</label><br />
 </div>

 <div id="demographics">
  <fieldset>
   <legend><a href="#" onClick="hide('demographics'); show('demographics[show]'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"></a>{ts}Demographics{/ts}</legend>
   <div class="col1">
    <label>{ts}Gender:{/ts}</label> {$gender.gender}<br />
    {if $is_deceased eq 1}
        <label>{ts}Contact is Deceased{/ts}</label>
    {/if}
   </div>
   <div class="col2">
{*    <label>{ts}Date of Birth:{/ts}</label> {$birth_date|date_format:"%B %e, %Y"} *}
    <label>{ts}Date of Birth:{/ts}</label> {$birth_date|crmDate} - actual: {$birth_date} 
   </div>
  </fieldset>
 </div>
 {/if}

<div id="relationships[show]" class="data-group">
  {if $relationship.totalCount}
    <a href="#" onClick="hide('relationships[show]'); show('relationships'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"></a><label>{ts}Relationships{/ts}</label> ({$relationship.totalCount})<br />
  {else}
    <dl><dt>{ts}Relationships{/ts}</dt><dd>{capture assign=crmURL}{crmURL p='civicrm/contact/view/rel' q='action=add'}{/capture}{ts 1=$crmURL}No relationships. Use the <a href="%1">Relationships tab</a> to add them.{/ts}</dd></dl>
  {/if}
</div>

{* Relationships block display property is always hidden (non) if there are no relationships *}
<div id="relationships">
 {if $relationship.totalCount}
 <fieldset><legend><a href="#" onClick="hide('relationships'); show('relationships[show]'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"></a>{if $relationship.totalCount GT 3}{ts 1=$relationship.totalCount}Relationships (3 of %1){/ts}{else}{ts}Relationships{/ts}{/if}</legend>
    {strip}
        <table>
        <tr class="columnheader">
            <th>{ts}Relationship{/ts}</th>
            <th></th>
            <th>{ts}City{/ts}</th>
            <th>{ts}State{/ts}</th>
            <th>{ts}Email{/ts}</th>
            <th>{ts}Phone{/ts}</th>
            <th>&nbsp;</th>
        </tr>

        {foreach from=$relationship.data item=rel}
          {*assign var = "rtype" value = "" }
              {if $rel.contact_a > 0 }
            {assign var = "rtype" value = "b_a" }
          {else}	  
            {assign var = "rtype" value = "a_b" }
          {/if*}
            <tr class="{cycle values="odd-row,even-row"}">
                <td class="label">{$rel.relation}</td>
                <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$rel.cid`"}">{$rel.name}</a></td>
                <td>{$rel.city}</td>
                <td>{$rel.state}</td>
                <td>{$rel.email}</td>
                <td>{$rel.phone}</td>
                <td><a href="{crmURL p='civicrm/contact/view/rel' q="rid=`$rel.id`&action=update&rtype=`$rel.rtype`"}">{ts}Edit{/ts}</a></td> 
            </tr>  
        {/foreach}
        {if $relationship.totalCount gt 3 }
            <tr class="even-row"><td colspan="7"><a href="{crmURL p='civicrm/contact/view/rel' q='action=browse'}">&raquo; {ts}View All Relationships...{/ts}</a></td></tr>
        {/if}
        </table>
	{/strip}
   <div class="action-link">
       <a href="{crmURL p='civicrm/contact/view/rel' q='action=add'}">&raquo; {ts}New Relationship{/ts}</a>
   </div>
 </fieldset>
 {/if}
</div>

<div id="groups[show]" class="data-group">
  {if $group.totalCount}
    <a href="#" onClick="hide('groups[show]'); show('groups'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"></a><label>{ts}Group Memberships{/ts}</label> ({$group.totalCount})<br />
  {else}
    <dl><dt>{ts}Group Memberships{/ts}</dt><dd>{capture assign=$crmURL}{crmURL p='civicrm/contact/view/group' q='action=add'}{/capture}{ts 1=$crmURL}No group memberships. Use the <a href="%1">Groups tab</a> to add them.{/ts}</dd></dl>
  {/if}
</div>

<div id="groups">
 <fieldset><legend><a href="#" onClick="hide('groups'); show('groups[show]'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"></a>{if $group.totalCount GT 3}{ts 1=$group.totalCount}Group Memberships (3 of %1){/ts}{else}{ts}Group Memberships{/ts}{/if}</legend>
	{strip}
	<table>
        <tr class="columnheader">
		<th>{ts}Group{/ts}</th>
		<th>{ts}Tag{/ts}</th>
		<th>{ts}Status{/ts}</th>
		<th>{ts}Date Added{/ts}</th>
	</tr>
    {foreach from=$group.data item=row}
        <tr class="{cycle values="odd-row,even-row"}">
        	<td>{$row.title}</td>
	    	<td></td>	
	    	<td>{ts 1=$row.in_method}Added (by %1){/ts}</td> 
            <td>{$row.in_date|date_format:"%B %e, %Y"}</td>
        </tr>
    {/foreach}
    {if $group.totalCount gt 3 }
        <tr class="even-row"><td colspan="7"><a href="{crmURL p='civicrm/contact/view/group' q='action=browse'}">&raquo; {ts}View All Group Memberships...{/ts}</a></td></tr>
    {/if}
    </table>
	{/strip}
   <div class="action-link">
       <a href="{crmURL p='civicrm/contact/view/group'}">&raquo; {ts}New Group Membership{/ts}</a>
   </div>
 </fieldset>
</div>

<div id="activities[show]" class="data-group">
  {if $activity.totalCount}
    <a href="#" onClick="hide('activities[show]'); show('activities'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"></a><label>{ts}Activities{/ts}</label> ({$activity.totalCount})<br />
  {else}
    <dl><dt>{ts}Activities{/ts}</dt><dd>{capture assign=crmURL}{crmURL p='civicrm/contact/view/activity' q='action=add'}{/capture}{ts 1=$crmURL}No activities. Use the <a href="%1">Activity tab</a> to add them.{/ts}</dd></dl>
  {/if}
</div>

<div id="activities">
 <fieldset><legend><a href="#" onClick="hide('activities'); show('activities[show]'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"></a>{if $activity.totalCount GT 3}{ts 1=$activity.totalCount}Activities (3 of %1){/ts}{else}{ts}Activities{/ts}{/if}</legend>
	{strip}
	<table>
        <tr class="columnheader">
		<th>{ts}Activity Type{/ts}</th>
		<th>{ts}Description{/ts}</th>
		<th>{ts}Activity Date{/ts}</th>
	</tr>
    {foreach from=$activity.data item=row}
        <tr class="{cycle values="odd-row,even-row"}">
        	<td>{$row.activity_type}</td>
	    	<td>{$row.activity_summary}</td>	
            <td>{$row.activity_date|crmDate}</td>
        </tr>
    {/foreach}
    {if $activity.totalCount gt 3 }
        <tr class="even-row"><td colspan="7"><a href="{crmURL p='civicrm/contact/view/activity' q='action=browse'}">&raquo; {ts}View All Activities...{/ts}</a></td></tr>
    {/if}
    </table>
	{/strip}
   <div class="action-link">
       <a href="{crmURL p='civicrm/contact/view/activity' q='action=add'}">&raquo; {ts}New Activities{/ts}</a>
   </div>
 </fieldset>
</div>

<div id="notes[show]" class="data-group">
  {if $noteTotalCount}
    <a href="#" onClick="hide('notes[show]'); show('notes'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"></a><label>{ts}Contact Notes{/ts}</label> ({$noteTotalCount})<br />
  {else}
    <dl><dt>{ts}Contact Notes{/ts}</dt><dd>{capture assign=crmURL}{crmURL p='civicrm/contact/view/note' q='action=add'}{/capture}{ts 1=$crmURL}No notes. Use the <a href="%1">Notes tab</a> to add them.{/ts}</dd></dl>
  {/if}
</div>

<div id="notes">
{if $note}
  <fieldset><legend><a href="#" onClick="hide('notes'); show('notes[show]'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"></a>{if $noteTotalCount GT 3}{ts 1=$noteTotalCount}Contact Notes (3 of %1){/ts}{else}{ts}Contact Notes{/ts}{/if}</legend></legend>
       {strip}
       <table>
       <tr class="columnheader">
    	<th>{ts}Note{/ts}</th>
	    <th>{ts}Date{/ts}</th>
	    <th></th>
       </tr>
       {foreach from=$note item=note}
       <tr class="{cycle values="odd-row,even-row"}">
            <td>
                {$note.note|mb_truncate:80:"...":true}
                {* Include '(more)' link to view entire note if it has been truncated *}
                {assign var="noteSize" value=$note.note|count_characters:true}
                {if $noteSize GT 80}
                    <a href="{crmURL p='civicrm/contact/view/note' q="nid=`$note.id`&action=view"}">{ts}(more){/ts}</a>
                {/if}
            </td>
            <td>{$note.modified_date|date_format:"%B %e, %Y"}</td>
            <td><a href="{crmURL p='civicrm/contact/view/note' q="nid=`$note.id`&action=update"}">{ts}Edit{/ts}</a></td> 
       </tr>  
       {/foreach}
       {if $noteTotalCount gt 3 }
            <tr class="even-row"><td colspan="7"><a href="{crmURL p='civicrm/contact/view/note' q='action=browse'}">&raquo; {ts}View All Notes...{/ts}</a></td></tr>
       {/if}
       </table>
       {/strip}
       
       <div class="action-link">
         <a href="{crmURL p='civicrm/contact/view/note' q='action=add'}">&raquo; {ts}New Note{/ts}</a>
       </div>
 </fieldset>
{/if}
</div> <!-- End of Notes block -->

 <script type="text/javascript">
    var showBlocks = new Array({$showBlocks});
    var hideBlocks = new Array({$hideBlocks});

{* hide and display the appropriate blocks as directed by the php code *}
    on_load_init_blocks( showBlocks, hideBlocks );
 </script>

