{* Quest Pre-application:  essay section *}


{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
  <td class="grouplabel">
    <p class="preapp-instruction">     
    {ts}To minimize the risk of losing your work, you may wish to write your essay in another program and then paste it in this box when you are ready.{/ts}
    </p> 
    <label>{ts}List and describe the factors in your life that have most shaped you (1500 characters max).{/ts} <span class="marker">*</span></label>
  </td>
</tr>
<tr>
      <td> {$form.essay.html}</td>
</tr>  
<tr>
      <td>{$form.word_count.label} &nbsp;&nbsp;{$form.word_count.html}</td> 
</tr>  
  
</table>

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="end"}

{literal}
    <script type="text/javascript">
        function countit(){ 
            var formcontent = document.getElementById("essay");
            var contentvalue  = formcontent.value;
            var count = document.getElementById("word_count");
            count.value = contentvalue.length;
            if (count.value >= 1500) {
                formcontent.value = contentvalue.substr( 0, 1500 );
                count.value = 1500; 
                alert("You have reached the 1,500 character limit.");
            }
        }
   </script>  
{/literal}

