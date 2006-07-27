{* Quest College Match: Partner: Wellesley: Applicant Info section *}
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle.app_info}</td>
</tr>

<tr>
    <td>
    <table class="no-border">
    <tr class="tr-vertical-center-text">
        <td clas="grouplabel" width="35%">{$form.is_diploma.label}</td>
        <td clas="grouplabel">{$form.is_diploma.html}</td>
    </tr>
    </table>
    </td>
</tr>
<tr>
<td>{ts}Please list any International Baccalaureate tests you have taken or plan to take in 2007.{/ts}<br/></td>
</tr>
<tr><td>
<table>

<tr class="bold-label">
    <td class="optionlist">Subject</td>
    <td class="optionlist">Month / Year</td>
    <td class="optionlist">SL or HL</td>
    <td class="optionlist">Score</td>
</tr>
{section name=rowLoop start=1 loop=7}
    {assign var=i value=$smarty.section.rowLoop.index}
    {assign var=subject value="subject_"|cat:$i}
    {assign var=test_date value="test_date_"|cat:$i}
    {assign var=sl_hl value="slhl_"|cat:$i}
    {assign var=score value="score_"|cat:$i}
<tr>
    <td class="optionlist">{$form.$subject.html}</td>
    <td class="optionlist">{$form.$test_date.html}</td>
    <td class="fieldlabel optionlist">{$form.$sl_hl.html}</td>
    <td class="optionlist">{$form.$score.html|crmReplace:class:"four form-text"}</td>
</tr>
{/section}
</table>
</td></tr>
<tr>
    <td class="grouplabel">
        {$form.princeton_activities.label}<br/>
        <table>
        {assign var=count value=1}
        {foreach from=$form.princeton_activities key=k1 item=dnc1}
	        {if $count lt 10} 
            {assign var=count value=$count+1}
            {else}
	            {if $k1 is odd} 
		        <tr>
	            {/if}
	            <td class="grouplabel optionlist">{$form.princeton_activities.$k1.html}</td>	
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
    <td>
    <table class="no-border">
    <tr>
        <td width="33%" class="grouplabel">{$form.pin_no.label}</td>
        <td class="grouplabel">
            {$form.pin_no.html|crmReplace:class:"four form-text"}
            <div class="italic-text">
            {ts}You will use this number to log into Princeton's admission site (www.princeton.edu/admission) to check the status of your application. The first time you log in you will be asked to select a password to replace this pin number. Later on, you may log in to find out if we have received all of the required pieces of your application. At the end of the process, you will have the option to check your decision online.{/ts}
            </div>
        </td>
    </tr>
    </table>    
    </td>
</tr>
</table>

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app"> 
<tr>
    <td colspan="2" id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle.acd_intr}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.princeton_degree.label}<br/>
    <table><tr>
        <td class="optionlist grouplabel">{$form.princeton_degree.1.html}</td>
        <td class="optionlist grouplabel">{$form.princeton_degree.2.html}</td>
        <td class="optionlist grouplabel">{$form.princeton_degree.3.html}</td>
    </tr></table>
    </td>
    </td>
</tr>
<tr>
    <td>
    <div>
        {ts}What programs of study do you think you would like to follow at Princeton? Please indicate the three departments in which you are most interested at this time. Your preferences simply give us an idea of what your academic interests are at this time. (Your choices are not binding in any way.) *{/ts}
    </div><br/>
    
    <table>
    <tr><td colspan="3" class="grouplabel optionlist" id="bold-table-header">{$form.ab_department.label}</td></tr>
    {assign var=count value=1}
        {foreach from=$form.ab_department key=k2 item=dnc2}
	        {if $count lt 10} 
            {assign var=count value=$count+1}
            {else}
	            {if !(($k2-1)%3) }
		        <tr>
	            {/if}
	            <td class="grouplabel optionlist">{$form.ab_department.$k2.html}</td>
	            {if !($k2%3) }
		        </tr>
	            {/if}
    	    {/if}
 	    {/foreach}
	    {if !(($k2-1)%3) }
            <td class="grouplabel optionlist"></td>
		    <td class="grouplabel optionlist"></td>
            </tr>
	    {/if}
        {if !(($k2-2)%3) }
            <td class="grouplabel optionlist"></td>
            </tr>
	    {/if}
    <tr><td colspan="3" class="grouplabel optionlist" id="essay">{$form.bse_department.label}</td></tr>
    {assign var=count value=1}
        {foreach from=$form.bse_department key=k3 item=dnc3}
	        {if $count lt 10} 
            {assign var=count value=$count+1}
            {else}
	            {if !(($k3-1)%3) }
		        <tr>
	            {/if}
	            <td class="grouplabel optionlist">{$form.bse_department.$k3.html}</td>	
	            {if !($k3%3) } 
		        </tr>
	            {/if}
    	    {/if}
 	    {/foreach}
	    {if !(($k3-1)%3) }
            <td class="grouplabel optionlist"></td>
		    <td class="grouplabel optionlist"></td>
            </tr>
	    {/if}
        {if !(($k3-2)%3) }
            <td class="grouplabel optionlist"></td>
            </tr>
	    {/if}
    </table>
    </td>
</tr>
<tr>
    <td>
    <div>
        {ts}In addition to majoring in one of the departments listed above, many students also choose to complete one or more certificate programs. In some cases they constitute a focus within a particular department (e.g., the Creative Writing track in English); in other cases they are interdisciplinary (e.g., Environmental Studies). Programs award a certificate of proficiency. If, in addition to the choices of major you indicated above, you also have an interest in any of the programs listed below, please indicate the three in which you are most interested at this time. (Your choices are not binding in any way.){/ts}
    </div><br/>
    
    <table>
    {assign var=count value=1}
        {foreach from=$form.certificate_programs key=k4 item=dnc4}
	        {if $count lt 10}
            {assign var=count value=$count+1}
            {else}
	            {if !(($k4-1)%3) }
		        <tr>
	            {/if}
	            <td class="grouplabel optionlist">{$form.certificate_programs.$k4.html}</td>	
	            {if !($k4%3) }
		        </tr>
	            {/if}
    	    {/if}
 	    {/foreach}
        {if !(($k4-1)%3) }
            <td class="grouplabel optionlist"></td>
		    <td class="grouplabel optionlist"></td>
            </tr>
	    {/if}
        {if !(($k4-2)%3) }
            <td class="grouplabel optionlist"></td>
            </tr>
	    {/if}
	</table>
    </td>
</tr>
</table>
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}
