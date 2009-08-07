{if $action eq 8}
  {include file="CRM/Contribute/Form/PCP/Delete.tpl"}
{else}
  <fieldset><legend>{ts}Filter the list below by:{/ts}</legend>
     <div id="pcp" class="form-item">
         <table class="form-layout">
	    <tr>
		<td>{$form.status_id.label}<br />{$form.status_id.html}</td>
		<td>{$form.contibution_page_id.label}<br />{$form.contibution_page_id.html}</td>
		<td><br />{$form.buttons.html}</td>
	    </tr>
         </table>
     </div>
  </fieldset>
{/if}