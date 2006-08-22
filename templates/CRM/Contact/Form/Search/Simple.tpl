{literal}
<script type="text/javascript">
  djConfig = {
	isDebug: true
  };
</script>
<script type="text/javascript" src="/~lobo/dojo/dojo.js"></script>
<script type="text/javascript">
  dojo.require("dojo.widget.Select");
  dojo.require("dojo.widget.ComboBox");
</script>
{/literal}

<table>
        <tr> 
            <td class="label">{$form.sort_name.label}</td> 
            <td>{$form.sort_name.html}</td>
        </tr>
        <tr> 
            <td class="label">{$form.state_province.label}</td> 
            <td>{$form.state_province.html}</td>
        </tr>
        <tr> 
            <td class="label">{$form.country.label}</td> 
            <td>{$form.country.html}</td>
        </tr>
        <tr> 
            <td colspan=2>{$form.buttons.html}</td>
        </tr>

</table>

{if $rows}
<table>
<tr><th>Contact ID</th><th>Sort Name</th></tr>
{foreach from=$rows key=id item=name}
<tr><td>{$id}</td><td>{$name}</td></tr>
{/foreach}
</table>
{/if}

