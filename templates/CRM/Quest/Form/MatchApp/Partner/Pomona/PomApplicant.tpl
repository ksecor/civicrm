{* Quest College Match: Partner: Pomona *}
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan="3" id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    <td colspan="3" class="grouplabel">List any people who have stimulated your interest in Pomona, giving their name and relationship to you: <br /> </td>
</tr>
<tr><td class="grouplabel" colspan=2>
    <table>
    <tr>
    <td class="optionlist">Name</td>
    <td class="optionlist">Department</td>
    <td class="optionlist">Relationship</td>
    </tr>
    {assign var=i value=0 }
    {foreach from=$fields item=name key=id}
        {assign var=i value=$i+1 }
        {if ($id LT 9) }
            {if ( ($i-1)%3 == 0) }
            <tr>
            {/if}
            <td class="optionlist">{$form.$name.html|crmReplace:class:medium}</td>
            {if (($i % 3)==0) }
            </tr>
            {/if}
        {/if}
        {if ($id eq 9) }
            {if ( ($i-1)%3 == 0) }
            </tr>
            {/if}
        </table>
        </td>
        </tr>
        <tr>
            <td class="grouplabel">{$form.$name.label}</td>
            <td class="grouplabel" colspan="2">{$form.$name.html}</td>
        </tr>
        <tr>
            <td class="grouplabel">{$form.txtBrContext.label}</td>
            <td class="grouplabel" colspan="2">{$form.txtBrContext.html}</td> 
        </tr>
        {/if}
        
        {if ($id eq 10) }
        <tr>
            <td class="grouplabel">{$form.$name.label}</td>
             <td class="grouplabel" colspan="2">{$form.$name.html}</td>
        </tr>
        <tr>
            <td class="grouplabel">{$form.txtBrContext.label}</td>
            <td class="grouplabel" colspan="2">{$form.txtBrContext.html}</td> 
        </tr>
        {/if}
    {/foreach}


</table>
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}
