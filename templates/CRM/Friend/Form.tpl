{* Enduser Tell-a-Friend form. *} 
{if $status eq 'thankyou' } {* Form has been submitted. *}
    <p>{$thankYouText}</p>
    
    {* Add button for donor to create their own Personal Campaign page *}
    {if $linkText}
 	<div>
        <a href="{$linkTextUrl}" title="{$linkText}" class="button"><span>&raquo; {$linkText}</span></a>
    </div><br /><br />
    {/if}
      
{else}
<table class="form-layout-compressed">
	<tr>
		<td colspan=2>
            <p>
                {if $context EQ 'pcp'}
                    {ts 1=$pcpTitle}Spread the word about this fundraising page (%1). Add your personal message below. A link to the fundraising page will be automatically included in the email.{/ts}
                {else}
                    {$intro}
                {/if}
            </p>
		</td>
	</tr>

	<tr>
		<td class="right font-size12pt">{$form.from_name.label}&nbsp;&nbsp;</td>
		<td class="font-size12pt">{$form.from_name.html} &lt;{$form.from_email.html}&gt;</td>
	</tr>
	<tr>
		<td class="label font-size12pt">{$form.suggested_message.label}</td>
		<td>{$form.suggested_message.html}</td>
	</tr>

	<tr>
		<td></td>
		<td>
		<fieldset><legend>{ts}Send to these Friend(s){/ts}</legend>
		<table>
			<tr class="columnheader">
				<td>{ts}First Name{/ts}</td>
				<td>{ts}Last Name{/ts}</td>
				<td>{ts}Email Address{/ts}</td>
			</tr>
			{section name=mail start=1 loop=$mailLimit} 
			{assign var=idx	value=$smarty.section.mail.index}
			<tr>
				<td class="even-row">{$form.friend.$idx.first_name.html}</td>
				<td class="even-row">{$form.friend.$idx.last_name.html}</td>
				<td class="even-row">{$form.friend.$idx.email.html}</td>
			</tr>
			{/section}
		</table>
		</fieldset>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>{$form.buttons.html}</td>
	</tr>
</table>
{/if}