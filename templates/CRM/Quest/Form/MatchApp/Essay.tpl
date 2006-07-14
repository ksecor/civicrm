{foreach from=$essays item=essay}
{assign var=name value=$essay.name}
<tr>
    <td class="grouplabel">
        {$form.essay.$name.label}<br />
	{if $name eq 'personal'}
           <br/>{$form.personalStat_quests.label}{$form.personalStat_quests.html}<br/><br/>
	   {$form.upload_photo.label}&nbsp;{$form.upload_photo.html}<br/>
	   {ts}(The file should be of type GIF or JPEG. The file size should be at most 2MB.){/ts}<br/><br/>
	{/if }
        {$form.essay.$name.html} &nbsp;<br /><br />
        {$form.word_count.$name.label} &nbsp;&nbsp;{$form.word_count.$name.html}
    </td> 
</tr>
{/foreach}
</table>

{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}

{edit}
{literal}
    <script type="text/javascript">
        function countit(common_id){ 
            var formcontent = document.getElementById("essay_" + common_id);
            var contentvalue  = formcontent.value;
            var count = document.getElementById("word_count_" + common_id);
            count.value = contentvalue.length;
            if (count.value >= 3000) {
                formcontent.value = contentvalue.substr( 0, 3000 );
                count.value = 3000; 
                alert("You have reached the 1,500 character limit.");
            }
        }
   </script>  
{/literal}
{/edit}

