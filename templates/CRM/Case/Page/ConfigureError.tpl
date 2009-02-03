{* CiviCase Configuration Help - displayed when component is enabled but not yet configured. *}

{capture assign=docLink}{docURL page="CiviCase Admin" text="CiviCase Administration Documentation"}{/capture}

<div class="messages status">
      <dl>
      <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>      
      <dd><strong>{ts}You need to setup and load Case and Activity configuration files before you can begin using the CiviCase component.{/ts}</strong><dd>
      <dt>&nbsp;</dt>
      <dd>{ts 1=$docLink}Refer to the %1 to learn about this process.{/ts}</dd>
    </dl>
</div>
