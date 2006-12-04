{* Quest Pre-application:  School Other Information section *}
{include file="CRM/Quest/Form/CPS/AppContainer.tpl" context="begin"}

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    <td colspan=2 class="grouplabel"><p>{ts}If you've attended any special programs at other secondary schools, including summer schools and programs, or colleges where you took courses for credit, etc., please list them here.{/ts}</p></td>
</tr>
<tr><td colspan=2>

{section name=rowLoop start=1 loop=$maxOtherSchool}
    {assign var=i value=$smarty.section.rowLoop.index}
    <div id="id_otherSchool_info_{$i}">
    <table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
      <tr>
	{assign var=org_name value="organization_name_"|cat:$i}
    	<td class="grouplabel">{$form.$org_name.label} </td>
    	<td class="fieldlabel"> {$form.$org_name.html} </td>
      </tr>
      <tr>
	{assign var=date_entry value="date_of_entry_"|cat:$i}
	{assign var=date_exit value="date_of_exit_"|cat:$i}
    	<td class="grouplabel">{$form.$date_entry.label} </td>
    	<td class="grouplabel"><label>From</label> {$form.$date_entry.html} &nbsp;&nbsp; <label>To</label> {$form.$date_exit.html} </td>
      </tr>
      <tr>
    	<td class="grouplabel" rowspan="4"><label>{ts}Location{/ts}</label></td>
      </tr>
      <tr>
	{assign var=location value="location_"|cat:$i}
    	<td class="fieldlabel">{$form.$location.1.address.city.html}<br>{ts}{edit}City{/edit}{/ts}</td>
      </tr>
      <tr>
    	<td class="fieldlabel">{$form.$location.1.address.state_province_id.html}<br>{ts}{edit}State or Province{/edit}{/ts}</td>
      </tr>
      <tr>
    	<td class="fieldlabel">{$form.$location.1.address.country_id.html}<br>{ts}{edit}Country{/edit}{/ts}</td>
      </tr>
      <tr>
	{assign var=note value="note_"|cat:$i}
	<td class="grouplabel">{$form.$note.label}</td>
	<td class="fieldlabel">{$form.$note.html}</td>
      </tr>
      {if $i LT $maxOtherSchool}
        <tr>
	   <td colspan=2>
    	        {assign var=j value=$i+1}
               	<br /><span id="id_otherSchool_info_{$j}_show">{$otherSchool_info.$j.show}</span>
           </td>
        </tr> 
      {/if}
    </table>
    </div>
{/section}
</td></tr>
</table>
{include file="CRM/Quest/Form/CPS/AppContainer.tpl" context="end"}
