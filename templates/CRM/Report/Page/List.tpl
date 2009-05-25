{strip}
    <fieldset>
        <legend>{ts}Report List{/ts}</legend>
        {if $list}
            {foreach from=$list item=rows key=report}
	        <br>
                <h3 style="color:#0062A0;cursor:pointer;" onclick="toggle_visibility('{$report}');">
                    <strong>{if $report}{$report}{else}Contact{/if} Reports</strong></h3>
		    <div id="{$report}" style="display:none;">
		        {foreach from=$rows item=row}
                    	    <div class="action-link">
                    	        <a href="{$row.1}">&raquo; {$row.0}</a>
		            </div>
            		{/foreach}
		    </div>
	    {/foreach}
        {else}
            <div class="messages status">
            <dl>
                <dt>
                    <img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/>
                </dt>
                <dd>
                    {ts}There are currently no Report.{/ts}
                </dd>
            </dl>
            </div>
        {/if}
    </fieldset>
{/strip}
{literal}
<script type="text/javascript">
    function toggle_visibility(id) {
	var e = document.getElementById(id);
	if (e.style.display == 'block') {
	    e.style.display = 'none';
	} else {
	    e.style.display = 'block';
	}
    }
</script>
{/literal}
