{foreach from=$essays item=essay}
{assign var=name value=$essay.name}
<tr>
    <td class="essay grouplabel">
        {$form.essay.$name.label}<br /><br />
    {if $name eq 'personal'}
        These questions come from college undergraduate admissions applications.<br /><br />
        This question is considered the main essay question in this application and, consequently, your answer should be somewhat longer and more thought out than your responses to the short answer questions.<br />
    {/if}
	{if $name eq 'personal' or $name eq 'stanford_essay'}
        <br/>{$form.personalStat_quests.label}{$form.personalStat_quests.html}<br/><br/>
        <div id="id_upload_photo">
	    {$form.upload_photo.label}&nbsp; {$form.upload_photo.html}<br/>
	    {ts}(The file should be of type GIF or JPEG. The file size should be at most 2MB.){/ts}<br/><br/>
        </div>
	{/if}
    {if $name eq 'optional'}
      <em>You may include any additional information you feel will help us get to know you better. Please feel free to include any information on your relationship to a non-custodial parent, any extra medical expenses, special ways your school calculates GPA&quot;s, etc.</em><br /><br />
    {/if}
        {$form.essay.$name.html} &nbsp;<br /><br />
        Current word count: &nbsp; {$form.word_count.$name.html}
    </td> 
</tr>
{/foreach}
</table>

{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}
