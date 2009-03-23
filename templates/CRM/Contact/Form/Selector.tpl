{include file="CRM/common/pager.tpl" location="top"}

{include file="CRM/common/pagerAToZ.tpl"}

<table summary="{ts}Search results listings.{/ts}" class="selector">
  <thead class="sticky">
  <th scope="col" title="Select All Rows">{$form.toggleSelect.html}</th>
  {if $context eq 'smog'}
      <th scope="col">
        {ts}Status{/ts}
      </th>
  {/if}
  {foreach from=$columnHeaders item=header}
    <th scope="col">
    {if $header.sort}
      {assign var='key' value=$header.sort}
      {$sort->_response.$key.link}
    {else}
      {$header.name}
    {/if}
    </th>
  {/foreach}
  </thead>

  {counter start=0 skip=1 print=false}

  { if $id }
      {foreach from=$rows item=row}
        <tr id='rowid{$row.contact_id}' class="status-hold {cycle values="odd-row,even-row"}">
            {assign var=cbName value=$row.checkbox}
            <td>{$form.$cbName.html}</td>
            {if $context eq 'smog'}
              {if $row.status eq 'Pending'}<td class="status-pending"}>
              {elseif $row.status eq 'Removed'}<td class="status-removed">
              {else}<td>{/if}
              {$row.status}</td>
            {/if}
            <td>{$row.contact_type}</td>
            <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}">{$row.sort_name}</a></td>
            {foreach from=$row item=value key=key} 
               {if ($key neq "checkbox") and ($key neq "action") and ($key neq "contact_type") and ($key neq "status") and ($key neq "sort_name") and ($key neq "contact_id") and ($key neq "contact_sub_type")}
                <td>
                {if $key EQ "household_income_total" }
                    {$value|crmMoney}
		{elseif strpos( $key, '_date' ) !== false }
                    {$value|crmDate}
                {else}
                    {$value}
                {/if}
                     &nbsp;
                 </td>
               {/if}
            {/foreach}
            <td>{$row.action|replace:'xx':$row.contact_id}</td>
        </tr>
     {/foreach}
  {else}
      {foreach from=$rows item=row}
         <tr id='rowid{$row.contact_id}' class="{cycle values="odd-row,even-row"}">
            {assign var=cbName value=$row.checkbox}
            <td>{$form.$cbName.html}</td>
            {if $context eq 'smog'}
                {if $row.status eq 'Pending'}<td class="status-pending"}>
                {elseif $row.status eq 'Removed'}<td class="status-removed">
                {else}<td>{/if}
                {$row.status}</td>
            {/if}
            <td>{$row.contact_type}</td>	
            <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}">{$row.sort_name}</a></td>
            {if $action eq 512 or $action eq 256}
              <td>{$row.street_address|mb_truncate:22:"...":true}</td>
              <td>{$row.city}</td>
              <td>{$row.state_province}</td>
              <td>{$row.postal_code}</td>
              <td>{$row.country}</td>
              <td {if $row.on_hold}class="status-hold"{/if}>{$row.email|mb_truncate:17:"...":true}{if $row.on_hold}&nbsp;(On Hold){/if}</td>
              <td>{$row.phone}</td> 
           {else}
              {foreach from=$row item=value key=key}
                {if ($key neq "checkbox") and ($key neq "action") and ($key neq "contact_type") and ($key neq "contact_sub_type") and ($key neq "status") and ($key neq "sort_name") and ($key neq "contact_id")}
                 <td>{$value}&nbsp;</td>
                {/if}   
              {/foreach}
            {/if}
            <td>{$row.action|replace:'xx':$row.contact_id}</td>
         </tr>
    {/foreach}
  {/if}
</table>

<!-- Context Menu -->
<ul id="contactMenu" class="contextMenu">
   <li><a href="#contribution">Record Contribution</a></li>
   <li><a href="#participant">Register for Event</a></li>
   <li><a href="#activity">Record Activity</a></li>
   <li><a href="#pledge">Add Pledge</a></li>
   <li><a href="#membership">Enter Membership</a></li>
   <li><a href="#email">Send an Email</a></li>
</ul>
<script type="text/javascript">
 {* this function is called to change the color of selected row(s) *}
    var fname = "{$form.formName}";	
    on_load_init_checkboxes(fname);
 {literal}
cj(document).ready( function() {
var url= "{/literal}{crmURL p='civicrm/contact/view/changeaction q="reset=1&action=add&cid=changeid&context=changeaction" h=0}{literal}";
var activityUrl = "{/literal}{crmURL p='civicrm/contact/view/activity q="reset=1&snippet=1&cid=changeid" h=0}{literal}";

// Show menu when contact row is right clicked
cj(".selector tr").contextMenu({
		menu: 'contactMenu'
    }, function( action ){ 
         cj(".selector tr").mouseover(function() {
             var contactId = cj(this).attr('id').substr(5);
             if ( action == 'activity' || action == 'email' ) {
                 if ( action == 'email' ) {
                  activityUrl = activityUrl.replace( /&snippet=1/, '&atype=3&action=add' );
                 }
               url = activityUrl.replace( /changeid/, contactId );
             } else {
               url =  url.replace( /changeaction/g, action ); url = url.replace( /changeid/, contactId );
             }
           window.location = url;
        });
	});
});

{/literal}
</script>
{include file="CRM/common/pager.tpl" location="bottom"}
