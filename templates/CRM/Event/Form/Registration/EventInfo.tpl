{* this template is used for displaying event  *}

<div class="form-item">
    <div class="header-dark">
        {ts}Event Information{/ts}
    </div>
    <div class="display-block">
	{*<fieldset><legend>{ts}Online Registration{/ts}</legend>*}
	<table class="form-layout-compressed">
	  <tr><td>{ts}<strong>Event Title</strong>{/ts}</td><td>{ts}{$event.title}{/ts}</td></tr>
	  <tr><td>{ts}<strong>Event Description</strong>{/ts}</td><td>{ts}{$event.description}{/ts}</td></tr>
	  <tr><td>{ts}<strong>Event Start Date</strong>{/ts}</td><td>{ts}{$event.event_start_date}{/ts}</td></tr>
	  <tr><td>{ts}<strong>Event End Date</strong>{/ts}</td><td>{ts}{$event.event_end_date}{/ts}</td></tr>
      <tr><td>{ts}<strong>Paid Event?</strong>{/ts}</td><td>{if $event.is_monetary eq 1}{ts}Yes{/ts} {else} {ts}No{/ts}{/if}</td></tr>
      <tr><td>{ts}<strong>Event Fee Label</strong>{/ts}</td></tr>
         {section name=loop start=1 loop=11}
            {assign var=idx value=$smarty.section.loop.index}
            <tr><td><strong>{$custom.label.$idx}</td><td>{$config->defaultCurrencySymbol}&nbsp;{$custom.value.$idx}</td><td class="even-row">{$custom.default.$idx}<br/></td></tr>
         {/section}

	{*</fieldset>*}
    </table>
    </div>	
    <div id="crm-submit-buttons">
        {$form.buttons.html}
    </div>
</div>
