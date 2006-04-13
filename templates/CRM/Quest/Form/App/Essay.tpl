{* Quest Pre-application:  essay section *}


{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
  <td class="grouplabel">
    {edit}
    <p class="preapp-instruction">     
    {ts}To minimize the risk of losing your work, you may wish to write your essay in another program and then paste it in this box when you are ready.{/ts}
    </p> 
    {/edit}

    <label>{ts}We are interested in  learning more about you and the context in which you have grown up, formed your aspirations, and accomplished your academic successes. Please describe the factors that have most shaped your personal life and your personal aspirations. (3000 characters max).{/ts} <span class="marker">*</span></label>
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

