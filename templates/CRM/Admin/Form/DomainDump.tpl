{* this template is for domain dump (backup data) *}

<div id="help">
    {ts}<p>Backup Database</p>{/ts}
</div>
   
<div class="messages status">
  <dl>
      <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"></dt>
      <dd>    
        {ts}<span class="label">Backup Your Data:</span> CiviCRM will create an SQL 'dump' file with all of your existing data, and allow you to download it to your local computer. This process may take a long time and generate a very large file if you have a large number of records. Do you want to continue?{/ts}
      </dd>
   </dl>
</div>
<div>
   <dl>   
     <dt></dt><dd>{$form.buttons.html}</dd>
   </dl>
</div>
 
