<div class='spacer'></div>
{if $mapProvider eq 'Google'}
  
  {include file="CRM/Contact/Form/Task/Map/Google.tpl"}
{elseif $mapProvider eq 'Yahoo'}
  {include file="CRM/Contact/Form/Task/Map/Yahoo.tpl"}
{/if}

<p></p>
<div class="form-item">                     
{$form.buttons.html}                                                                                      
</div>                            
