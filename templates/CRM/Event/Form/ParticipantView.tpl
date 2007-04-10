<div class="form-item">  
<fieldset>
        {if $history neq 1}
            <legend>{ts}View Participant{/ts}</legend>
        {else}
            <legend>{ts}View Activity History{/ts}</legend>
        {/if}
        <dl>  
        <dt class="font-size12pt">{ts}Name{/ts}</dt><dd class="font-size12pt"><strong>{$displayName}</strong>&nbsp;</dd>
        <dt>{ts}Event{/ts}</dt><dd>{$event}&nbsp;</dd>
        <dt>{ts}Participant Role{/ts}</dt><dd>{$role}&nbsp;</dd>
        {if $history neq 1}
            <dt>{ts}Registration Date{/ts}</dt><dd>{$register_date|truncate:10:''|crmDate}&nbsp;</dd>
        {else}
            <dt>{ts}Modified Date{/ts}</dt><dd>{$modified_date|truncate:10:''|crmDate}&nbsp;</dd>   
        {/if}
        <dt>{ts}Status{/ts}</dt><dd>{$status}&nbsp;</dd>
        {if $source}
            <dt>{ts}Event Source{/ts}</dt><dd>{$source}&nbsp;</dd>
        {/if}
        {if $event_level}
        <dt>{ts}Event Level{/ts}</dt><dd>{$event_level}&nbsp;</dd>
        {/if}
        {if $history neq 1}
	    {foreach from=$note item="rec"}
		    {if $rec }
			<dt>{ts}Note:{/ts}</dt><dd>{$rec}</dd>	
	   	    {/if}
        {/foreach}
         
            {include file="CRM/Contact/Page/View/InlineCustomData.tpl" mainEditForm=1}  
        {/if}
        <dl>
           <dt></dt><dd>{$form.buttons.html}</dd>
        </dl>
    </dl>

</fieldset>  
</div>
