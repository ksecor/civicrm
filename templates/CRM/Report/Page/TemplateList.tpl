{strip}
{if $list}
    {foreach from=$list item=rows key=report}		
	<div style="cursor:pointer;" onclick="toggle_visibility('{$report}');">
	    <table class="report-layout">
		<tr>
		    <th>{if $report}{$report}{else}Contact{/if} Reports</th>
            <th>
                <div style=" float:right; width:10px;"> 
                    <img id="report_{$report}" src="{$config->resourceBase}i/menu-expanded.png" />
                </div>
            </th>
		</tr>
	    </table>
	</div>
	<div id="{$report}" style="display:block;">
	    <table class="report-layout">
		{foreach from=$rows item=row}
		    <tr>
			<td style="width:35%;">
			    <a href="{$row.url}" title="{$row.description}">&raquo; <strong>{$row.title}</strong></a>
			    {if $row.instanceUrl}
				<div align="right">
				    <a href="{$row.instanceUrl}">{ts}Available Report(s){/ts}</a>
				</div>
			    {/if}
			</td>
			<td style="cursor:help;">
			    {$row.description}
			</td>
		    </tr>
		{/foreach}
	    </table>
	</div>
	<br />
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
{/strip}
{literal}
<script type="text/javascript">
    function toggle_visibility(id) {
    var basepath = '{/literal}{$config->resourceBase}{literal}';
	var e = document.getElementById(id);
    var i = document.getElementById('report_'+id);
	if (e.style.display == 'block') {
	    e.style.display = 'none';
        i.src =  basepath + 'i/menu-collapsed.png';
	} else {
	    e.style.display = 'block';
        i.src = basepath + 'i/menu-expanded.png';
	}
    }
</script>
{/literal}
