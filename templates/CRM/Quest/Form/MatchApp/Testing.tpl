{* Quest College Match Application: Testing Information section *}

{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
  <td>
    <B>Questions about which tests to lists?</B> <A HREF="http://www.questbridge.org/students/schoo_information_faq.html" TARGET="_blank">Read more at our FAQs</A>
  </td>
</tr>
</table>

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
  <td colspan="2" class="grouplabel">
    <p class="preapp-instruction">{ts}Enter your test scores and dates for any of the tests below which you have taken. Please enter all test component scores and the test date for each completed test. For each test, enter the top three score received.{/ts}</p>
  </td>
</tr>
</table>

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr><td colspan=2 id="sub-category">{ts}ACT Test(s){/ts}</td></tr>
<tr><td colspan=2>
{section name=rowLoop start=1 loop=$maxACT}
    {assign var=i value=$smarty.section.rowLoop.index}
    <div id="id_act_test_{$i}">
    <table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
     <tr>
        {assign var=fld value="act_english_"|cat:$i}
        <td class="grouplabel"> {$form.$fld.label}</td>
        <td class="fieldlabel" width="75%">{$form.$fld.html}</td>
    </tr>
    <tr>
        {assign var=fld value="act_reading_"|cat:$i}
        <td class="grouplabel"> {$form.$fld.label}</td>
        <td class="fieldlabel">{$form.$fld.html}</td>
    </tr>
    <tr>
        {assign var=fld value="act_math_"|cat:$i}
        <td class="grouplabel"> {$form.$fld.label}</td>
        <td class="fieldlabel">{$form.$fld.html}</td>
    </tr>
    <tr>
        {assign var=fld value="act_science_"|cat:$i}
        <td class="grouplabel"> {$form.$fld.label}</td>
        <td class="fieldlabel">{$form.$fld.html}</td>
    </tr>
    <tr>
        {assign var=fld value="act_date_"|cat:$i}
        <td class="grouplabel"> {$form.$fld.label}</td>
        <td class="fieldlabel">{$form.$fld.html}
            {if $i LT $maxACT}
                {assign var=j value=$i+1}
                <br /><span id="id_act_test_{$j}_show">{$act_test.$j.show}</span>
            {/if}        
        </td>
    </tr>
    </table>
    </div>
{/section}
</td></tr>
</table>

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr><td colspan=2 id="sub-category">{ts}SAT Test{/ts}</td></tr>
<tr><td colspan=2>
{section name=rowLoop start=1 loop=$maxACT}
    {assign var=i value=$smarty.section.rowLoop.index}
    <div id="id_sat_test_{$i}">
    <table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
     <tr>
        {assign var=fld value="sat_criticalreading_"|cat:$i}
        <td class="grouplabel"> {$form.$fld.label}</td>
        <td class="fieldlabel" width="75%">{$form.$fld.html}</td>
    </tr>
    <tr>
        {assign var=fld value="sat_math_"|cat:$i}
        <td class="grouplabel"> {$form.$fld.label}</td>
        <td class="fieldlabel">{$form.$fld.html}</td>
    </tr>
    <tr>
        {assign var=fld value="sat_writing_"|cat:$i}
        <td class="grouplabel"> {$form.$fld.label}</td>
        <td class="fieldlabel">{$form.$fld.html}</td>
    </tr>
    <tr>
        {assign var=fld value="sat_date_"|cat:$i}
        <td class="grouplabel"> {$form.$fld.label}</td>
        <td class="fieldlabel">{$form.$fld.html}
            {if $i LT $maxSAT}
                {assign var=j value=$i+1}
                <br /><span id="id_sat_test_{$j}_show">{$sat_test.$j.show}</span>
            {/if}        
        </td>
    </tr>
    </table>
    </div>
{/section}
</td></tr>
</table>

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr><td colspan=2 id="sub-category">{ts}SAT II Subject Test(s){/ts}</td></tr>
<tr><td colspan=2>

{section name=rowLoop start=1 loop=$maxSATII}
    {assign var=i value=$smarty.section.rowLoop.index}
    <div id="id_satII_test_{$i}">
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
            {if $i LT $maxSATII}
                {assign var=j value=$i+1}
                <br /><span id="id_satII_test_{$j}_show">{$satII_test.$j.show}</span>
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
{section name=rowLoop start=1 loop=$maxAP}
    {assign var=i value=$smarty.section.rowLoop.index}
    <div id="id_ap_test_{$i}">
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
            {if $i LT $maxAP}
                {assign var=j value=$i+1}
                <br /><span id="id_ap_test_{$j}_show">{$ap_test.$j.show}</span>
            {/if}        
        </td>
    </tr>
    </table>
    </div>
{/section}
</td></tr>
</table>

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr><td colspan=2 id="sub-category">{ts}TOEFL Test(s){/ts}</td></tr>
<tr><td colspan=2>
{section name=rowLoop start=1 loop=$maxTOEFL}
    {assign var=i value=$smarty.section.rowLoop.index}
    <div id="id_toefl_test_{$i}">
    <table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
      <tr>
        {assign var=score value="toefl_score_"|cat:$i}
        <td class="grouplabel">{$form.$score.label}</td>
        <td class="fieldlabel" width="75%">{$form.$score.html}</td>
    </tr>
    <tr>
        {assign var=date value="toefl_date_$i"}
        <td class="grouplabel">{$form.$date.label}</td>
        <td class="fieldlabel">
            {$form.$date.html}
            {if $i LT $maxTOEFL}
                {assign var=j value=$i+1}
                <br /><span id="id_toefl_test_{$j}_show">{$toefl_test.$j.show}</span>
            {/if}        
        </td>
    </tr>
    </table>
    </div>
{/section}
</td></tr>
</table>


{* Next 3 fields are only included if student won the SAT Prep Scholarship. *}
{if $isPrepWinner}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td class="grouplabel">{$form.is_SAT_after_prep.label}</td>
    <td class="fieldlabel" width="75%">{$form.is_SAT_after_prep.html}</td>
</tr>
<tr id="SAT_prep_improve">
    <td class="grouplabel">{$form.is_SAT_prep_improve.label}</td>
    <td class="fieldlabel">{$form.is_SAT_prep_improve.html}</td>
</tr>
<tr id="SAT_prep_improve_how">
    <td class="grouplabel">{$form.SAT_prep_improve.label}</td>
    <td class="fieldlabel">{$form.SAT_prep_improve.html}</td>
</tr>
</table>
{/if}

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td class="grouplabel">{$form.is_SAT_again.label}</td>
    <td class="fieldlabel" width="75%">{$form.is_SAT_again.html}</td>
</tr>
<tr id="SAT_again_date">
    <td class="grouplabel">{$form.SAT_plan_date.label}</td>
    <td class="fieldlabel">{$form.SAT_plan_date.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.is_ACT_again.label}</td>
    <td class="fieldlabel">{$form.is_ACT_again.html}</td>
</tr>
<tr id="ACT_again_date">
    <td class="grouplabel">{$form.ACT_plan_date.label}</td>
    <td class="fieldlabel">{$form.ACT_plan_date.html}</td>
</tr>

<tr>
    <td class="grouplabel">{$form.is_more_SATII.label}</td>
    <td class="fieldlabel">{$form.is_more_SATII.html}</td>
</tr>
<tr id="SATII_more_subjects">
    <td class="grouplabel">{$form.more_SATII_subjects.label}</td>
    <td class="fieldlabel">{$form.more_SATII_subjects.html}</td>
</tr>
<tr id="SATII_more_date">
    <td class="grouplabel">{$form.SATII_plan_date.label}</td>
    <td class="fieldlabel">{$form.SATII_plan_date.html}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.is_tutoring.label}</td>
    <td class="fieldlabel" width="75%">{$form.is_tutoring.html}</td>
</tr>
<tr id="tutor_tests">
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

{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}

{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="is_tutoring"
    trigger_value       ="1"
    target_element_id   ="tutor_tests" 
    target_element_type ="table-row"
    field_type          ="radio"
    invert              = 0
}

{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="is_SAT_again"
    trigger_value       ="1"
    target_element_id   ="SAT_again_date" 
    target_element_type ="table-row"
    field_type          ="radio"
    invert              = 0
}

{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="is_ACT_again"
    trigger_value       ="1"
    target_element_id   ="ACT_again_date" 
    target_element_type ="table-row"
    field_type          ="radio"
    invert              = 0
}

{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="is_more_SATII"
    trigger_value       ="1"
    target_element_id   ="SATII_more_subjects|SATII_more_date" 
    target_element_type ="table-row"
    field_type          ="radio"
    invert              = 0
}

{if $isPrepWinner}
    {include file="CRM/common/showHideByFieldValue.tpl" 
        trigger_field_id    ="is_SAT_after_prep"
        trigger_value       ="1"
        target_element_id   ="SAT_prep_improve" 
        target_element_type ="table-row"
        field_type          ="radio"
        invert              = 0
    }

    {include file="CRM/common/showHideByFieldValue.tpl" 
        trigger_field_id    ="is_SAT_prep_improve"
        trigger_value       ="1"
        target_element_id   ="SAT_prep_improve_how" 
        target_element_type ="table-row"
        field_type          ="radio"
        invert              = 0
    }
{/if}
