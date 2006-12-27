{* this template is used for adding event  *}

<div class="form-item">
    <div class="header-dark">
        {ts}Event Information{/ts}
    </div>
    <div class="display-block">
	{*<fieldset><legend>{ts}Online Registration{/ts}</legend>
	{*<table border=0>
	  <tr><td>{ts}<strong>Event Title</strong>{/ts}</td><td>{ts}{$event.title}{/ts}</td></tr>
	  <tr><td>{ts}<strong>Event Description</strong>{/ts}</td><td>{ts}{$event.description}{/ts}</td></tr>
	  <tr><td>{ts}<strong>Event Start Date</strong>{/ts}</td><td>{ts}{$event.event_start_date}{/ts}</td></tr>
	  <tr><td>{ts}<strong>Event End Date</strong>{/ts}</td><td>{ts}{$event.event_end_date}{/ts}</td></tr>
	{*</table>*}
	  <dl>
	    <dt>{ts}<strong>Event Title</strong>{/ts}</dt><dd>{ts}{$event.title}{/ts}</dd>
	    <dt>{ts}<strong>Event Description</strong>{/ts}</dt><dd>{ts}{$event.description}{/ts}</dd>
	    <dt>{ts}<strong>Event Start Date</strong>{/ts}</dt><dd>{ts}{$event.event_start_date}{/ts}</dd>
	    <dt>{ts}<strong>Event End Date</strong>{/ts}</dt><dd>{ts}{$event.event_end_date}{/ts}</dd>
	  </dl>	
	{*</fieldset>*}
    </div>	
    <div id="crm-submit-buttons">
        {$form.buttons.html}
    </div>
</div>
