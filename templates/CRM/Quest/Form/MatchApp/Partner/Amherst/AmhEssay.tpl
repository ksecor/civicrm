{* Quest College Match: Partner: Amherst: Essay section *}
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan="2" id="category">Amherst College - Short Answer</td>
</tr>
<tr>
    <td class="grouplabel" colspan="2">
        {$form.essay.short_essay.label}<br />
        {$form.essay.short_essay.html} &nbsp;<br /><br />
        {$form.word_count.short_essay.label} &nbsp;&nbsp;{$form.word_count.short_essay.html}
    </td> 
</tr>
</table>
<br />
<table cellpadding=0 cellspacing=1 border=0 width="90%" class="app">
    <tr><td colspan="2" id="category">Amherst College - Essay</td></tr>
    <tr>
        <td colspan="2" class="grouplabel">
            <label>In addition to the essay you're asked to write as part of the College Match Application, Amherst requires another essay (250-500 words). We do not offer interviews as part of the application process at Amherst. However, we are eager to know more about you. Your essays provide you with an opportunity to speak to us. Please keep this in mind when responding to one of the following quotations. <strong>It is not necessary to research, read, or refer to the texts from which these quotations are taken; we are looking for original, personal responses to these short excerpts rather than book reviews or book reports</strong>.</label> <span class="marker">*</span> (500 words max)<br />
            <br />
            {$form.amherst_essay.html}
        </td>
    </tr>
    <tr>
        <td class="grouplabel" colspan="2">
            {$form.essay.essay.html} &nbsp;<br /><br />
            {$form.word_count.essay.label} &nbsp;&nbsp;{$form.word_count.essay.html}
        </td> 
    </tr>
</table>
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}

