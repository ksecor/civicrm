{* Quest Pre-application: Educational Interests  section *}

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}
{strip}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepTitle} {ts 1=$wizard.currentStepNumber 2=$wizard.stepCount}(step %1 of %2){/ts}
</td>
<tr>
    <td class="grouplabel" width="30%">
        {$form.educational_interest.label}</td>
    <td>
        <table class="app">
        {assign var="countEI" value=0}
        {foreach from=$form.educational_interest item=type key=key}
            {assign var="countEI" value=`$countEI+1`}
            {if $countEI gt 9 }
                {if $countEI is not odd}
                <tr>
                {/if}
                <td class="optionlist">{$form.educational_interest.$key.html}</td>
                {if $countEI is not even}
                </tr>
                {/if}
            {/if}
        {/foreach}
        {if ($countEI gt 9) and ($countEI is not odd) }
        <td class="optionlist"></td></tr>
        {/if}
        <tr>
        <td colspan=2 id="educational-interest-other" class="optionlist">{$form.educational_interest_other.html|crmReplace:class:large}</td>
        </tr>
        </table>
    </td>
</tr>
<tr>
    <td class="grouplabel" width="30%">
        {$form.college_type.label}</td>
    <td>
        <table class="app">
        {assign var="countCT" value=0}
        {foreach from=$form.college_type item=type key=key}
            {assign var="countCT" value=`$countCT+1`}
            {if $countCT gt 9 }
                {if $countCT is not odd}
                <tr>
                {/if}
                <td class="optionlist">{$form.college_type.$key.html}</td>
                {if $countCT is not even}
                </tr>
                {/if}
            {/if}
        {/foreach}
        {if ($countCT gt 9) and ($countCT is not odd) }
        <td class="optionlist"></td></tr>
        {/if}
        </table>
    </td>
</tr>
<tr>
    <td class="grouplabel" width="30%">
        {$form.college_interest.label}</td>
    <td>
        <table class="app">
        {assign var="countCI" value=0}
        {foreach from=$form.college_interest item=type key=key}
            {assign var="countCI" value=`$countCI+1`}
            {if $countCI gt 9 }
                {if $countCI is not odd}
                <tr>
                {/if}
                <td class="optionlist">{$form.college_interest.$key.html}</td>
                {if $countCI is not even}
                </tr>
                {/if}
            {/if}
        {/foreach}
        {if ($countCI gt 9) and ($countCI is not odd) }
        <td class="optionlist"></td></tr>
        {/if}
        </table>
    </td>
</tr>
<tr>
    <td class="grouplabel" width="30%">
        {$form.college_interest_other.label}</td>
    <td class="fieldlabel">
        {$form.college_interest_other.html}</td>
</tr>
</table>
{/strip}
{include file="CRM/Quest/Form/App/AppContainer.tpl" context="end"}

{literal}
    <script type="text/javascript">
      var selectedOther; 
	  selectedOther = document.getElementsByName("educational_interest[270]")[0].checked;
	  if (selectedOther) {
		show('educational-interest-other', 'table-cell');
	   } else {
		hide('educational-interest-other');
	   }
      
   	function showTextField() {
	   selectedOther = document.getElementsByName("educational_interest[270]")[0].checked;
        
	   if (selectedOther) {
		show('educational-interest-other', 'table-cell');
	   } else {
		hide('educational-interest-other');
	   }
 	}
    </script>  
{/literal}
