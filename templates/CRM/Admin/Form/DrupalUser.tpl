{* this template is for synchronizing druapl user*}

<div id="help">
    <p>{ts}Synchronize Drupal Users{/ts}</p>
</div>
   
<div class="messages status">
  <dl>
      <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
      <dd>    
        <span class="label">{ts}Synchronize Users to Contacts:{/ts}</span> {ts}CiviCRM will check each user record for a contact record. A new contact record will be created for each user where one does not already exist.{/ts} {ts}Do you want to continue?{/ts}
      </dd>
   </dl>
</div>
<div>
   <dl>   
     <dt></dt><dd>{$form.buttons.html}</dd>
   </dl>
</div>
 
