{* this template is used for displaying event information *}

<div class="form-item">
    <div class="header-dark">
        {ts}Event Information{/ts}
    </div>
    <div class="display-block">
	<table class="form-layout-compressed">
	  <tr><td>{ts}<strong>Event Title</strong>{/ts}</td><td>{ts}{$event.title}{/ts}</td></tr>
	  <tr><td>{ts}<strong>Event Description</strong>{/ts}</td><td>{ts}{$event.description}{/ts}</td></tr>
	  <tr><td>{ts}<strong>Event Start Date</strong>{/ts}</td><td>{ts}{$event.event_start_date}{/ts}</td></tr>
	  <tr><td>{ts}<strong>Event End Date</strong>{/ts}</td><td>{ts}{$event.event_end_date}{/ts}</td></tr>
      <tr><td>{ts}<strong>Paid Event?</strong>{/ts}</td><td>
{if $event.is_monetary eq 1}{ts}Yes{/ts}
      <tr><td>{ts}<strong>Fee Levels</strong>{/ts}</td>
         {section name=loop start=1 loop=2}
            {assign var=idx value=$smarty.section.loop.index}
                 {if $custom.value.$idx}
           <td>{$config->defaultCurrencySymbol}{$custom.value.$idx}&nbsp;&nbsp;&nbsp;<strong>{$custom.label.$idx}</td><td class="even-row">{$custom.default.$idx}</td></tr>
                 {/if}
         {/section}
         {section name=loop start=2 loop=11}
            {assign var=idx value=$smarty.section.loop.index}
                 {if $custom.value.$idx}
           <tr><td></td> <td>{$config->defaultCurrencySymbol}{$custom.value.$idx}&nbsp;&nbsp;&nbsp;<strong>{$custom.label.$idx}</td><td class="even-row">{$custom.default.$idx}<br/></td></tr>
                 {/if}
         {/section}
{else} 
       {ts}No{/ts}
{/if}</td>
      <tr><td>{ts}<strong>Location</strong>{/ts}</td><td>{ts}{$location.1.address.display|nl2br}{/ts}  {if ( $config->mapAPIKey AND ( is_numeric($location.1.address.geo_code_1)  OR ( $config->mapGeoCoding AND $location.1.address.city AND $location.1.address.state_province ) ) ) }<br/><a href="{crmURL p='civicrm/contact/search/map' q="reset=1&eid=`$event.id`"}" title="{ts}Map this Address{/ts}">{ts}Map this Address{/ts}</a>{/if}</td></tr>

   </table>
   </div>	
    <div id="crm-submit-buttons" class="underline-effect">
        {$form.buttons.html|crmReplace:class:"submit-link"}
    </div>
</div>
