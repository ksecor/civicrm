<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

{if $mapProvider eq 'Google'}
  {include file="CRM/Contact/Form/Task/Map/Google.tpl"}
{elseif $mapProvider eq 'Yahoo'}
  {include file="CRM/Contact/Form/Task/Map/Yahoo.tpl"}
{/if}

<p></p>
<div class="form-item">                     
    <p> 
    {$form.buttons.html}                                                                                      
    </p>    
</div>                            

</html>
