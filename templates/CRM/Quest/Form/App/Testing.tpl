{* Quest Pre-application: Testing Information section *}

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepTitle} {ts 1=$wizard.currentStepNumber 2=$wizard.stepCount}(step %1 of %2){/ts}
</tr>
<tr><td colspan=2 id="sub-category">{ts}ACT Test{/ts}</td>
</tr>
<tr>
    <td class="grouplabel"> {$form.act_english.label}</td>
    <td class="fieldlabel">{$form.act_english.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.act_reading.label}</td>
    <td class="fieldlabel">{$form.act_reading.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.act_writing.label}</td>
    <td class="fieldlabel">{$form.act_writing.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.act_math.label}</td>
    <td class="fieldlabel">{$form.act_math.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.act_science.label}</td>
    <td class="fieldlabel">{$form.act_science.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.act_composite.label}</td>
    <td class="fieldlabel">{$form.act_composite.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.act_date.label}</td>
    <td class="fieldlabel">{$form.act_date.html}</td>
</tr>
</table>
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr><td colspan=2 id="sub-category">{ts}PSAT Test{/ts}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.psat_criticalreading.label}</td>
    <td class="fieldlabel">{$form.psat_criticalreading.html}</td>
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
    <td class="grouplabel">{$form.psat_total.label}</td>
    <td class="fieldlabel">{$form.psat_total.html}</td>
</tr>

<tr>
    <td class="grouplabel">{$form.psat_date.label}</td>
    <td class="fieldlabel">{$form.psat_date.html}</td>
</tr>
</table>
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr><td colspan=2 id="sub-category">{ts}SAT Test{/ts}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.sat_criticalreading.label}</td>
    <td class="fieldlabel">{$form.sat_criticalreading.html}</td>
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
    <td class="grouplabel">{$form.sat_total.label}</td>
    <td class="fieldlabel">{$form.sat_total.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.sat_date.label}</td>
    <td class="fieldlabel">{$form.sat_date.html}</td>
</tr>
</table>

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr><td colspan=2 id="sub-category">{ts}SAT II Subject Test{/ts}</td></tr>
{*{section name=rowLoop start=1 loop=6}*}
{section name=rowLoop start=1 loop=3}
    {assign var=i value=$smarty.section.rowLoop.index}
     <tr>
        {assign var=subject value="satII_subject_id_"|cat:$i}
        <td class="grouplabel">{$form.$subject.label}</td>
        <td class="fieldlabel"> {$form.$subject.html}</td>
           
    </tr>
    <tr>
        {assign var=score value="satII_score_"|cat:$i}
        <td class="grouplabel">{$form.$score.label}</td>
        <td class="fieldlabel"> {$form.$score.html}</td>
    </tr>
    <tr>
        {assign var=date value="satII_date_"|cat:$i}
        <td class="grouplabel">{$form.$date.label}</td>
        <td class="fieldlabel">{$form.$date.html}</td>
    </tr>
{/section}
</table>

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr><td colspan=2 id="sub-category">{ts}AP Tests{/ts}</td></tr>
</table>

{* assign var=maxAP value=33 *}
{assign var=maxAP value=4}
{section name=rowLoop start=1 loop=$maxAP}
    {assign var=i value=$smarty.section.rowLoop.index}
    <table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
     <tr>
        {assign var=subject value="ap_subject_id_"|cat:$i}
        <td class="grouplabel">{$form.$subject.label}</td>
        <td class="fieldlabel">{$form.$subject.html}</td>
    </tr>
    <tr>
        {assign var=score value="ap_score_"|cat:$i}
        <td class="grouplabel">{$form.$score.label}</td>
        <td class="fieldlabel">{$form.$score.html}</td>
    </tr>
    <tr>
        {assign var=date value="ap_date_$i"}
        <td class="grouplabel">{$form.$date.label}</td>
        <td class="fieldlabel">
            {$form.$date.html}
            {if $i LT $maxAP}
                <br /><span id="ap_test_{$i}[show]">{$ap_test.$i.show}</span>
            {/if}        
        </td>
    </tr>
    </table>
{/section}

        <div id="ethnicity_id_2[show]">{$ethnicity_id_2.show}</div>
        <div id="ethnicity_id_2">
            {$form.ethnicity_id_2.html}
            <span id="ethnicity_id_2[hide]">{$ethnicity_id_2.hide}</span>
        </div>

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td class="grouplabel">{$form.is_test_tutoring.label}</td>
    <td class="fieldlabel">{$form.is_test_tutoring.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.test_tutoring.label}</td>
    <td class="fieldlabel">{$form.test_tutoring.html}</td>
</tr>
</table>

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="end"}
