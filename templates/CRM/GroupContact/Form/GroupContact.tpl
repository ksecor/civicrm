{* this template is used for managing groups *}

 {* Including the javascript from the GroupContact.js file *}
 <script type="text/javascript" src="{$config->resourceBase}js/GroupContact.js"></script>

<form {$form.attributes}>
<div class="form-item">
<fieldset><legend>{if $op eq 'add'}New{else}Edit{/if} Contact Group(s)</legend>
	<div class="data-group">
      	<span><label>{$displayName} </label></span>
	</div>
	<div>
	{*if $op eq 'add'*}
	  <div class="form-item">
    	  <fieldset>
	    <table>
		<tr><td><b>Not a member of</b></td><td></td><td><b>Member of</b></td></tr>
              <tr>
                <td>{$form.allgroups.html} </td>
                <td>
		   {$form.add.html}<br>{$form.remove.html}
                </td>
                <td> {$form.contactgroups.html}</td>
              </tr>
            </table>
    	  </fieldset>
	  </div>
	{*/if*}

	<div class="horizontal-position">
	<span class="two-col1">
        <span class="fields">{$form.buttons.html}</span>
	</span>
	<div class="spacer"></div>
	</div>

    </fieldset>
</div>
</form>
