{strip}
    <fieldset>
        <legend>{ts}Report List{/ts}</legend>
        {if $list}
            {foreach from=$list item=rows key=report}
	        <br>
                <div style="cursor:pointer;background-color:#F5F5F5" onclick="toggle_visibility('{$report}');">
	            <table class="form-layout">
		        <tr>
			    <td><strong>{if $report}{$report}{else}Contact{/if} Reports</strong></td>
			</tr>
		    </table>
	        </div>
		<div id="{$report}" style="display:none;">
		    <table class="report">
		        {foreach from=$rows item=row}
	                    <tr >
			        <td width="300"><a href="{$row.2}">&raquo; {$row.0}</a>
   				    {if $row.instance}
					<div align="right">
					    <a href="{$row.instance}">{ts}Instance{/ts}</a>
					</div>
				    {/if}
			        </td>
				<td width="450">{$row.1}</td>
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
