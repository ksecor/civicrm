{* this template is for synchronizing druapl user*}

<div id="help">
    <p>{ts}Synchronize Drupal Users{/ts}</p>
</div>
   
<div class="messages status">
  <dl>
      <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"></dt>
      <dd>    
        {ts}<span class="label">Synchronize Users to Contacts:</span> CiviCRM will check each user record for a contact record. A new contact records will be created for each user where on exist. Do you want to continue?{/ts}
      </dd>
   </dl>
</div>
<div>
   <dl>   
     <dt></dt><dd>{$form.buttons.html}</dd>
   </dl>
</div>
 
