{include file="CRM/common/WizardHeader.tpl"}

<div id="form" class="form-item">
    <fieldset><legend>{ts}CiviContribute Widget{/ts}</legend>
    
    <dl>
    	<dt></dt><dd>{$form.is_active.html}&nbsp;{$form.is_active.label}</dd>
    </dl>
    <div id="widgetFields">
    <dl>
{foreach from=$fields item=field key=name}
      <dt>{$form.$name.label}</dt><dd>{$form.$name.html}</dd>   
{/foreach}
    </dl>
    </div>

    {if $action ne 4}
    <div id="crm-submit-buttons">
        <dl><dt></dt><dd>{$form.buttons.html}</dd></dl>  
    </div>
    {else}
    <div id="crm-done-button">
         <dl><dt></dt><dd>{$form.buttons.html}<br></dd></dl>
    </div>
    {/if} {* $action ne view *}

    </fieldset>
</div>      

{literal}
<script type="text/javascript">
	var is_act = document.getElementsByName('is_active');
  	if ( ! is_act[0].checked) {
           hide('widgetFields');
	}
       function widgetBlock(chkbox) {
           if (chkbox.checked) {
	      show('widgetFields');
	      return;
           } else {
	      hide('widgetFields');
    	      return;
	   }
       }
</script>
{/literal}