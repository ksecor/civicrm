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
		    <table style="border:0;">
		        {foreach from=$rows item=row}
	                    <tr style="border-bottom:1px solid #E3E9ED;background-color:{cycle values="#FFFFFF;,#F4F6F8;" name="$report"}">
  		                <td style="color:#2F425C;width:200px;">
     		                    <a href="{$row.Url}" style="text-decoration:none;display:block;" title="{$row.info}">
                 		        <img alt="report" src="{$config->resourceBase}i/report.gif"/>&nbsp;&nbsp;
			            	<strong>{$row.title}</strong>
				    </a>
   				    {if $row.instance}
					<div align="right">
					    <a href="{$row.instance}">{ts}Instance{/ts}</a>
					</div>
				    {/if}
			        </td>
				<td style="cursor:help;width:350px;">
				    {$row.info}
				</td>
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
