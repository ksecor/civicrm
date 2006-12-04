{foreach from=$essays item=essay}
{assign var=name value=$essay.name}
<tr>
    <td class="essay grouplabel">
        {$form.essay.$name.label}<br /><br />
    {if $name eq 'personal'}
        These questions come from college undergraduate admissions applications.<br /><br />
        This question is considered the main essay question in this application and, consequently, your answer should be somewhat longer and more thought out than your responses to the short essay questions.<br />
    {/if}
	{if $name eq 'personal' or $name eq 'stanford_essay'}
        <br/>{$form.personalStat_quests.label}{$form.personalStat_quests.html}<br/><br/>
        {if $attachment}
           <div id="id_upload_photo">
           <strong>Your Picture</strong>
           <a href="{crmURL p='civicrm/file' q="action=view&eid=`$attachment.entity_id`&id=`$attachment.file_id`&quest=1"}">{$attachment.file_type}    </a><br/>
           {edit}
           <div id="upload_show">
                <a href="#" onclick="hide('upload_show'); show('upload'); return false;">{ts}&raquo; <label>Upload a new photo</label>{/ts}</a>
           </div>
           <div id="upload">
           {$form.uploadFile.html}<br/>
            {edit}{ts}The file should be of type GIF or JPEG. The file size should be at most 2MB.{/ts}{/edit}
           </div>
           {/edit}
          </div>
        {else}
        {edit}
        <div id="id_upload_photo">
	    {$form.uploadFile.label}&nbsp; {$form.uploadFile.html}<br/>
	    {ts}(The file should be of type GIF or JPEG. The file size should be at most 2MB.){/ts}<br/><br/>
        </div>
        {/edit}
        {/if}
	{/if}
    {if $name eq 'optional'}
      <em>You may include any additional information you feel will help us get to know you better. Please feel free to include any information on your relationship to a non-custodial parent, any extra medical expenses, special ways your school calculates GPA's, etc.</em><br /><br />
    {/if}
        {$form.essay.$name.html} &nbsp;<br /><br />
        {edit}Current word count: &nbsp; {$form.word_count.$name.html}{/edit}
    </td> 
</tr>
{/foreach}
</table>
{if $attachment}
{literal}
    <script type="text/javascript">
    hide('upload');
    </script>
{/literal}
{/if}


{include file="CRM/Quest/Form/CPS/AppContainer.tpl" context="end"}
