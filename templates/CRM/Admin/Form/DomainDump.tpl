{* this template is for domain dump (backup data) *}

<div id="help">
    <p>{ts}Backup Database{/ts}</p>
</div>
   
<div class="messages status">
  <dl>
      <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
      <dd>    
        <span class="label">{ts}Backup Your Data:{/ts}</span> {ts}CiviCRM will create an SQL dump file with all of your existing data, and allow you to download it to your local computer. This process may take a long time and generate a very large file if you have a large number of records.{/ts} {ts}Do you want to continue?{/ts}
      </dd>
   </dl>
</div>
<div>
   <dl>   
     <dt></dt><dd>{$form.buttons.html}</dd>
   </dl>
</div>
 
