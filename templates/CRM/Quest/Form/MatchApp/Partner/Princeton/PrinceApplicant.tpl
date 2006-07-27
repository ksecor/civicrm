{* Quest College Match: Partner: Wellesley: Applicant Info section *}
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan="2" id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>

<tr>
    <td>
        {$form.is_diploma.label}{$form.is_diploma.html}
    </td>
</tr>
<tr><td>
<table>
<tr>
    <td>subject</td>
    <td>test_date</td>
    <td>sl_hl</td>
    <td>score</td>
</tr>
{section name=rowLoop start=1 loop=7}
    {assign var=i value=$smarty.section.rowLoop.index}
    {assign var=subject value="subject_"|cat:$i}
    {assign var=test_date value="test_date_"|cat:$i}
    {assign var=sl_hl value="slhl_"|cat:$i}
    {assign var=score value="score_"|cat:$i}
<tr>
    <td>{$form.$subject.html}</td>
    <td>{$form.$test_date.html}</td>
    <td>{$form.$sl_hl.html}</td>
    <td>{$form.$score.html}</td>
</tr>
{/section}
</table>
</td></tr>
<tr>
    <td>
        {$form.princeton_activities.label}{$form.princeton_activities.html}
    </td>
</tr>
<tr>
    <td>
        {$form.pin_no.label}{$form.pin_no.html}
    </td>
</tr>
</table>

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app"> 
<tr>
    <td colspan="2" id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    <td class="optionlist">
        {$form.princeton_degree.label}{$form.princeton_degree.html}
    </td>
    </td>
</tr>
<tr>
    <td class="optionlist">
        {$form.ab_department.label}{$form.ab_department.html}
    </td>
</tr>
<tr>
    </td>
    <td class="optionlist">
        {$form.bsc_department.label}{$form.bsc_department.html}
    </td>
    </td>
</tr>
<tr>
    </td>
    <td class="optionlist">
        {$form.certificate_programs.label}{$form.certificate_programs.html}
    </td>
</tr>






</table>
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}


