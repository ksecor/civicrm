{* Quest College Match: Partner: Columbia: Personal Essay section *}
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan="2" id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    <td class="grouplabel">
	{ts}Write an essay which conveys to the reader a sense of who you are. Possible topics may include, but are not limited to, experiences which have shaped your life, the circumstances of your upbringing, your most meaningful intellectual achievement, the way you see the world -- the people in it, events great and small, everyday life -- or any personal theme which appeals to your imagination. Please remember that we are concerned not only with the substance of your prose but also with your writing style as well. We prefer that you limit yourself to approximately 250-500 words.{/ts}
        {$form.essay.personal.label}<br />
        {$form.essay.personal.html} &nbsp;<br /><br />
        {$form.word_count.personal.label} &nbsp;&nbsp;{$form.word_count.personal.html}
    </td> 
</tr>
</table>
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}

