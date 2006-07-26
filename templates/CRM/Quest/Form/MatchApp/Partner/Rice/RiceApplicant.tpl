{* Quest College Match: Partner: Amherst: Applicant Info section *}
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan="2" id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.rice_academic.label}</td>
    <td class="grouplabel">
       <table>
	<tr>
	  <td class="optionlist grouplabel">{$form.rice_academic.1.html}</td>
	  <td class="optionlist grouplabel">{$form.rice_academic.2.html}</td>
	</tr>
	<tr>
	  <td class="optionlist grouplabel">{$form.rice_academic.3.html}</td>
	  <td class="optionlist grouplabel">{$form.rice_academic.4.html}</td>
	</tr>
	<tr>
	  <td class="optionlist grouplabel">{$form.rice_academic.5.html}</td>
	  <td class="optionlist grouplabel">{$form.rice_academic.6.html}</td>
	</tr>
       </table>
    </td>
</tr>
<tr>
     <td class="grouplabel" width="33%">{ts}Check principal area(s) of interest, including areas outside the school to which you are applying. Subjects in blue are not majors at Rice, but some courses in these disciplines are offered.{/ts}</td>
     <td class="grouplabel">
       <table>
        {foreach from=$schools key=name item=title}
        {assign var=schFld value="rice_"|cat:$name}
        <tr><td colspan="2" class="grouplabel optionlist" id="bold-table-header">{$form.$schFld.label}</td></tr>
        {assign var=count value=1}
        {foreach from=$form.$schFld key=k1 item=dnc1}
	{if $count lt 10} 
        {assign var=count value=$count+1}
        {else}
	  {if $k1 is odd} 
		<tr>
	  {/if}
	  <td class="grouplabel optionlist">{$form.$schFld.$k1.html}
	    {if ($name eq "music") and ($k1 eq 4)}
		<br/>{$form.music_other.label}{$form.music_other.html}
	    {/if}
	  </td>	
	  {if $k1 is even} 
		</tr>
	  {/if}
    	{/if}
 	{/foreach}
	{if $k1 is odd}
		<td class="grouplabel optionlist"></td></tr>
	{/if}

        {/foreach}
       </table>
     </td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.essay.academic_school.label}
    </td>
    <td class="grouplabel">
        {$form.essay.academic_school.html} 
    </td> 
</tr>
<tr>
    <td class="grouplabel">
        {$form.is_medicine.label}
    </td>
    <td class="grouplabel">
        {$form.is_medicine.html} 
    </td> 
</tr>
<tr>
    <td class="grouplabel">
        {$form.is_rotc.label}
    </td>
    <td class="grouplabel">
        {$form.is_rotc.html} 
    </td> 
</tr>
<tr>
    <td class="grouplabel">
        {$form.essay.account_school.label}
    </td>
    <td class="grouplabel">
        {$form.essay.account_school.html} 
    </td> 
</tr>
<tr>
     <td class="grouplabel">{$form.rice_contacts.label}</td>
     <td class="grouplabel">
       <table>

        {assign var=count value=1}
        {foreach from=$form.rice_contacts key=k1 item=dnc1}
	{if $count lt 10} 
        {assign var=count value=$count+1}
        {else}
	  {if $k1 is odd} 
		<tr>
	  {/if}
	  <td class="grouplabel optionlist">{$form.rice_contacts.$k1.html}
	        {foreach from=$addTexts key=k2 item=name}
	            {if $k2 eq $k1}
			<br/>{$form.$name.label}{$form.$name.html}
		    {/if}	
	 	{/foreach}
		
	  </td>	
	  {if $k1 is even} 
		</tr>
	  {/if}
    	{/if}
 	{/foreach}
	{if $k1 is odd}
		<td class="grouplabel optionlist"></td></tr>
	{/if}


       </table>
     </td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.essay.other_colleges.label}
    </td>
    <td class="grouplabel">
        {$form.essay.other_colleges.html} 
    </td> 
</tr>
<tr>
    <td class="grouplabel">
        {$form.is_consent.label}
    </td>
    <td class="grouplabel">
        {$form.is_consent.html} 
    </td> 
</tr>
</table>
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}

