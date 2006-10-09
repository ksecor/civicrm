<div class="form-item">
<fieldset>
    <legend>{$profileTitle}</legend>
         <table>
            <tr class="columnheader">
             <th>Name</th>
             {foreach from=$fields item=field key=name}
                <th>{$field.title}</th>
             {/foreach}
            </tr>
            {foreach from=$contributionIds item=cid}
             <tr class="{cycle values="odd-row,even-row"}">
              <td>{$sortName.$cid}</td> 
              {foreach from=$fields item=field key=name}
                {assign var=n value=$field.name}
                <td class="compressed">{$form.field.$cid.$n.html}</td> 
              {/foreach}
             </tr>
            {/foreach}
           </tr>
         </table>
        <dl>
            <dt></dt><dd>{if $fields}{$form._qf_Batch_refresh.html}{/if} &nbsp; {$form.buttons.html}</dd>
        </dl>
</fieldset>
</div>

