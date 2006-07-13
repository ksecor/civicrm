{foreach from=$essays item=essay}
{assign var=name value=$essay.name}
<tr>
      <td> {$form.essay.$name.label}</td>
</tr>  
<tr>
      <td> {$form.essay.$name.html}</td>
</tr>  
<tr>
      <td>{$form.word_count.$name.label} &nbsp;&nbsp;{$form.word_count.$name.html}</td> 
</tr>
{/foreach}
</table>

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="end"}

{edit}
{literal}
    <script type="text/javascript">
        function countit(){ 
            var formcontent = document.getElementById("essay");
            var contentvalue  = formcontent.value;
            var count = document.getElementById("word_count");
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

