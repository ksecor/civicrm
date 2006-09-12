<div class="form-item">
<fieldset>
    <legend>{ts}Batch Update via Profile{/ts}</legend>
    <dl>
        <dt>{$form.uf_group_id.label}</dt><dd>{$form.uf_group_id.html}
            {$form._qf_BatchUpdateProfile_refresh.html}</dd>
        <dt></dt><dd>{include file="CRM/Contact/Form/Task.tpl"}</dd>

        <table class="form-layout-compressed">
           <tr>
             <td class="label">Name</td>
             {foreach from=$fields item=field key=name}
                <td class="label">{$field.title}</td>
             {/foreach}
           </tr>
            {foreach from=$contactIds item=cid}
             <tr>
              <td>{$sortName.$cid}</td> 
              {foreach from=$fields item=field key=name}
                {assign var=n value=$field.name}
                <td>{$form.field.$cid.$n.html}</td> 
	        {if $form.$n.type eq 'file'}
	        {* <tr><td class="label"></td><td>{$customFiles.$n}</td></tr>*}
	        {/if}
              {/foreach}
             </tr>
           {/foreach}
          </tr>
        </table> 
        <dt></dt><dd>{$form.buttons.html}</dd>
    </dl>
</fieldset>
</div>

