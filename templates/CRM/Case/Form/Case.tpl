{* Base template for Open Case. May be used for other special activity types at some point ..
   Note: 1. We will include all the activity fields here however each activity type file may build (via php) only those required by them. 
         2. Each activity type file can include its case fields in its own template, so that they will be included during activity edit.
*}

{if !$clientName and $action eq 1}
   <tr><td colspan="2">
   <fieldset><legend>{ts}New Client{/ts}</legend>
	<table class="form-layout-compressed" border="0">
    <tr>
        <td>{$form.prefix_id.label}<br />{$form.prefix_id.html}</td>
		<td>{$form.first_name.label}<br />{$form.first_name.html}</td>
		<td>{$form.last_name.label}<br />{$form.last_name.html}</td>
		<td>{$form.suffix_id.label}<br />{$form.suffix_id.html}</td>
	</tr>
	<tr>
        <td colspan="2">{$form.location.1.phone.1.phone.label}<br />
            {$form.location.1.location_type_id.html}&nbsp;{$form.location.1.phone.1.phone_type_id.html}<br />{$form.location.1.phone.1.phone.html}
        </td>
        <td colspan="2">{$form.location.2.phone.1.phone.label}<br />
            {$form.location.2.location_type_id.html}&nbsp;{$form.location.2.phone.1.phone_type_id.html}<br />{$form.location.2.phone.1.phone.html}
        </td>
    </tr>
    <tr>
        <td colspan="2">{$form.location.1.email.1.email.label}<br />
		{$form.location.1.email.1.email.html}</td>
        <td colspan="2"></td>
	</tr>
    {if $isDuplicate}
    <tr>
        <td colspan="2">&nbsp;&nbsp;{$form._qf_Case_next_createNew.html}</td>
        {if $onlyOneDupe}
        <td colspan="2">&nbsp;&nbsp;{$form._qf_Case_next_assignExisting.html}</td>
        {/if}
    </tr>
    {/if}
    </table>
   </fieldset>
   </td></tr>
{/if}

<fieldset><legend>{if $action eq 8}{ts}Delete Case{/ts}{else}{$activityType}{/if}</legend>
<table class="form-layout">
{if $action eq 8 or $action eq 32768 } 
      <div class="messages status"> 
        <dl> 
          <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt> 
          <dd> 
          {if $action eq 8}
            {ts}Click Delete to move this case and all associated activities to the Trash.{/ts} 
          {else}
            {ts}Click Restore to retrieve this case and all associated activities from the Trash.{/ts} 
          {/if}
          </dd> 
       </dl> 
      </div> 
{else}
{if $clientName}
    <tr><td class="label font-size12pt">{ts}Client{/ts}</td><td class="font-size12pt bold view-value">{$clientName}</td></tr>
{/if}

{* activity fields *}
{if $form.medium_id.html and $form.activity_location.html}
    <tr>
        <td class="label">{$form.medium_id.label}</td>
        <td class="view-value">{$form.medium_id.html}&nbsp;&nbsp;&nbsp;{$form.activity_location.label} &nbsp;{$form.activity_location.html}</td>
    </tr> 
{/if}

{if $form.activity_details.html}
    <tr>
        <td class="label">{$form.activity_details.label} {help id="id-details" file="CRM/Case/Form/Case.hlp"}</td>
        <td class="view-value">{$form.activity_details.html|crmReplace:class:huge}</td>
    </tr>
{/if}

{* custom data group *}
{if $groupTree}
    <tr>
       <td colspan="2">{include file="CRM/Custom/Form/CustomData.tpl"}</td>
    </tr>
{/if}

{if $form.activity_subject.html}
    <tr><td class="label">{$form.activity_subject.label}</td><td>{$form.activity_subject.html}</td></tr>
{/if}

{* inject activity type-specific form fields *}
{if $activityTypeFile}
    {include file="CRM/Case/Form/Activity/$activityTypeFile.tpl"}
{/if}

{if $form.duration.html}
    <tr>
      <td class="label">{$form.duration.label}</td>
      <td class="view-value">
        {$form.duration.html}
         <span class="description">{ts}Total time spent on this activity (in minutes).{/ts}
      </td>
    </tr> 
{/if}


{/if}	

    <tr>
        <td>&nbsp;</td><td class="buttons">{$form.buttons.html}</td>
    </tr>
</table>
</fieldset>
