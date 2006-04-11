{* Quest Pre-application: Testing Information section *}

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
  <td colspan="2" class="grouplabel">
    <p class="preapp-instruction">{ts}Enter your test scores and dates for any of the tests below which you have taken. Please enter all test component scores and the test date for each completed test.{/ts}</p>
  </td>
</tr>
<tr><td colspan=2 id="sub-category">{ts}PSAT Test{/ts}</td>
</tr>
<tr> 
    <td class="grouplabel">{$form.psat_criticalreading.label}</td>
    <td class="fieldlabel" width="75%">{$form.psat_criticalreading.html}</td>
</tr>
<tr> 
    <td class="grouplabel">{$form.psat_math.label}</td>
    <td class="fieldlabel">{$form.psat_math.html}</td>   
</tr>
<tr> 
    <td class="grouplabel">{$form.psat_writing.label}</td>
    <td class="fieldlabel">{$form.psat_writing.html}</td>  
</tr>
<tr>
    <td class="grouplabel">{$form.psat_date.label}</td>
    <td class="fieldlabel">{$form.psat_date.html}</td>
</tr>
</table>
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr><td colspan=2 id="sub-category">{ts}PLAN (Pre-ACT) Test{/ts}</td>
</tr>
<tr>
    <td class="grouplabel"> {$form.pact_english.label}</td>
    <td class="fieldlabel" width="75%">{$form.pact_english.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.pact_reading.label}</td>
    <td class="fieldlabel">{$form.pact_reading.html}</td>
</tr>
{*<tr>
    <td class="grouplabel">{$form.pact_writing.label}</td>
    <td class="fieldlabel">{$form.pact_writing.html}</td>
</tr>*}
<tr>
    <td class="grouplabel">{$form.pact_math.label}</td>
    <td class="fieldlabel">{$form.pact_math.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.pact_science.label}</td>
    <td class="fieldlabel">{$form.pact_science.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.pact_date.label}</td>
    <td class="fieldlabel">{$form.pact_date.html}</td>
</tr>
</table>
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr><td colspan=2 id="sub-category">{ts}ACT Test{/ts}</td>
</tr>
<tr>
    <td class="grouplabel"> {$form.act_english.label}</td>
    <td class="fieldlabel" width="75%">{$form.act_english.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.act_reading.label}</td>
    <td class="fieldlabel">{$form.act_reading.html}</td>
</tr>
{*<tr>
    <td class="grouplabel">{$form.act_writing.label}</td>
    <td class="fieldlabel">{$form.act_writing.html}</td>
</tr>*}
<tr>
    <td class="grouplabel">{$form.act_math.label}</td>
    <td class="fieldlabel">{$form.act_math.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.act_science.label}</td>
    <td class="fieldlabel">{$form.act_science.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.act_date.label}</td>
    <td class="fieldlabel">{$form.act_date.html}</td>
</tr>
</table>

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr><td colspan=2 id="sub-category">{ts}SAT Test{/ts}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.sat_criticalreading.label}</td>
    <td class="fieldlabel" width="75%">{$form.sat_criticalreading.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.sat_math.label}</td>
    <td class="fieldlabel">{$form.sat_math.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.sat_writing.label}</td>
    <td class="fieldlabel">{$form.sat_writing.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.sat_date.label}</td>
    <td class="fieldlabel">{$form.sat_date.html}</td>
</tr>
</table>
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr><td colspan=2 id="sub-category">{ts}SAT II Subject Test(s){/ts}</td></tr>
<tr><td colspan=2>
{assign var=maxSAT value=6}
{section name=rowLoop start=1 loop=$maxSAT}
    {assign var=i value=$smarty.section.rowLoop.index}
    <div id="satII_test_{$i}">
    <table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
     <tr>
        {assign var=subject value="satII_subject_id_"|cat:$i}
        <td class="grouplabel">{$form.$subject.label}</td>
        <td class="fieldlabel" width="75%"> {$form.$subject.html}</td>
           
    </tr>
    <tr>
        {assign var=score value="satII_score_"|cat:$i}
        <td class="grouplabel">{$form.$score.label}</td>
        <td class="fieldlabel"> {$form.$score.html}</td>
    </tr>
    <tr>
        {assign var=date value="satII_date_"|cat:$i}
        <td class="grouplabel">{$form.$date.label}</td>
        <td class="fieldlabel">
            {$form.$date.html}
            {if $i LT $maxSAT}
                {assign var=j value=$i+1}
                <br /><span id="satII_test_{$j}[show]">{$satII_test.$j.show}</span>
            {/if}        
        </td>
    </tr>
    </table>
    </div>
{/section}
</td></tr>
</table>

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr><td colspan=2 id="sub-category">{ts}AP Test(s){/ts}</td></tr>
<tr><td colspan=2>
{assign var=maxAP value=33}
{section name=rowLoop start=1 loop=$maxAP}
    {assign var=i value=$smarty.section.rowLoop.index}
    <div id="ap_test_{$i}">
    <table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
     <tr>
        {assign var=subject value="ap_subject_id_"|cat:$i}
        <td class="grouplabel">{$form.$subject.label}</td>
        <td class="fieldlabel" width="75%">{$form.$subject.html}</td>
    </tr>
    <tr>
        {assign var=score value="ap_score_id_"|cat:$i}
        <td class="grouplabel">{$form.$score.label}</td>
        <td class="fieldlabel" width="75%">{$form.$score.html}</td>
    </tr>
    <tr>
        {assign var=date value="ap_date_$i"}
        <td class="grouplabel">{$form.$date.label}</td>
        <td class="fieldlabel">
            {$form.$date.html}
            {*{if $i GT 1}<span id="ap_test_{$i}[hide]">{$ap_test.$i.hide}</span>{/if}*}
            {if $i LT $maxAP}
                {assign var=j value=$i+1}
                <br /><span id="ap_test_{$j}[show]">{$ap_test.$j.show}</span>
            {/if}        
        </td>
    </tr>
    </table>
    </div>
{/section}
</td></tr>
</table>

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td class="grouplabel">{$form.is_test_tutoring.label}</td>
    <td class="fieldlabel" width="75%">{$form.is_test_tutoring.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.test_tutoring.label}</td>
    <td class="grouplabel">
        {assign var="countCI" value=0}
        {foreach from=$form.test_tutoring item=type key=key}
            {assign var="countCI" value=`$countCI+1`}
            {if $countCI gt 9 }
                {$form.test_tutoring.$key.html} &nbsp;&nbsp;
            {/if}
        {/foreach}
    </td>
</tr>
</table>

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="end"}
