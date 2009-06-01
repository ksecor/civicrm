{strip}
    <fieldset>
        <legend>{ts}Report Instance List{/ts}</legend>
        {if $list}
            {foreach from=$list item=rows key=report}
	        <br>
                <div style="cursor:pointer;background-color:#F5F5F5" onclick="toggle_visibility('{$report}');">
	            <table class="form-layout">
		        <tr>
			    <td><strong>{if $report}{$report}{else}Contact{/if} Reports Instance</strong></td>
			</tr>
		    </table>
	        </div>
		<div id="{$report}" style="display:block;">
		    <table class="report">
		        {foreach from=$rows item=row}
	                    <tr >
			        <td width="300"><a href="{$row.url}">&raquo; {$row.title}</a><br/>{$row.label}</td>
				<td width="450">{$row.description}</td>
	                        <td><a href="{$row.deleteUrl}" onclick="return window.confirm('Are you sure you want Delete this Instance?');">Delete</a></td>
			    </tr>
	        	{/foreach}
                    </table>
                </div>
	    {/foreach}
        {else}
            <div class="messages status">
            <dl>
                <dt>
                    <img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/>
                </dt>
                <dd>
                    {ts}There are currently no Report Instance.{/ts}
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