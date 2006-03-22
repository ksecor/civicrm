{* Quest Pre-application: Testing Information section *}

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}

<table cellpadding=0 cellspacing=1 border=0 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepTitle} {ts 1=$wizard.currentStepNumber 2=$wizard.stepCount}(step %1 of %2){/ts}
</tr>
<tr><td colspan=2 id="sub-category">{ts}ACT Test{/ts}</td>
</tr>
<tr>
    <td class="grouplabel"> {$form.act_english.label}</td>
    <td>{$form.act_english.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.act_reading.label}</td>
    <td>{$form.act_reading.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.act_writing.label}</td>
    <td>{$form.act_writing.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.act_math.label}</td>
    <td>{$form.act_math.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.act_science.label}</td>
    <td>{$form.act_science.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.act_composite.label}</td>
    <td>{$form.act_composite.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.act_date.label}</td>
    <td>{$form.act_date.html}</td>
</tr>

</tr>
<tr><td colspan=2 class="sub-category">{ts}PSAT Test{/ts}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.psat_criticalreading.label}</td>
    <td>{$form.psat_criticalreading.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.psat_math.label}</td>
    <td>{$form.psat_math.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.psat_writing.label}</td>
    <td>{$form.psat_writing.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.psat_total.label}</td>
    <td>{$form.psat_total.html}</td>
</tr>

<tr>
    <td class="grouplabel">{$form.psat_date.label}</td>
    <td>{$form.psat_date.html}</td>
</tr>

</tr>
<tr><td colspan=2 class="sub-category">{ts}SAT Test{/ts}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.sat_criticalreading.label}</td>
    <td>{$form.sat_criticalreading.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.sat_math.label}</td>
    <td>{$form.sat_math.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.sat_writing.label}</td>
    <td>{$form.sat_writing.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.sat_total.label}</td>
    <td>{$form.sat_total.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.sat_date.label}</td>
    <td>{$form.sat_date.html}</td>
</tr>
</tr>
<tr><td colspan=2 class="sub-category">{ts}SAT II Subject Test{/ts}</td>
</tr>

{*{section name=rowLoop start=1 loop=6}*}
{section name=rowLoop start=1 loop=2}
 <tr>
    {assign var=subject value="satII_"|cat:$smarty.section.rowLoop.index|cat:"_subject"}
    <td class="grouplabel">{$form.$subject.label}</td>
    <td> {$form.$subject.html}</td>
       
</tr>
<tr>
    {assign var=score value="satII_"|cat:$smarty.section.rowLoop.index|cat:"_score"}
    <td class="grouplabel">{$form.$score.label}</td>
    <td> {$form.$score.html}</td>
</tr>
<tr>
    {assign var=date value="satII_"|cat:$smarty.section.rowLoop.index|cat:"_date"}
    <td class="grouplabel">{$form.$date.label}</td>
    <td> {$form.$date.html}</td>
</tr>
{/section}

<tr><td colspan=2 class="sub-category">{ts}AP Tests{/ts}</td>
</tr>
{*{section name=rowLoop start=1 loop=33}*}
{section name=rowLoop start=1 loop=2}
 <tr>
    {assign var=subject value="ap_"|cat:$smarty.section.rowLoop.index|cat:"_subject"}
    <td class="grouplabel">{$form.$subject.label}</td>
    <td> {$form.$subject.html}</td>
       
</tr>
<tr>
    {assign var=score value="ap_"|cat:$smarty.section.rowLoop.index|cat:"_score"}
    <td class="grouplabel">{$form.$score.label}</td>
    <td> {$form.$score.html}</td>
</tr>
<tr>
    {assign var=date value="ap_"|cat:$smarty.section.rowLoop.index|cat:"_date"}
    <td class="grouplabel">{$form.$date.label}</td>
    <td> {$form.$date.html}</td>
</tr>
{/section}
<tr>
    <td class="grouplabel">{$form.is_test_tutoring.label}</td>
    <td>{$form.is_test_tutoring.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.test_tutoring.label}</td>
    <td>{$form.test_tutoring.html}</td>
</tr>
</table>

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="end"}
