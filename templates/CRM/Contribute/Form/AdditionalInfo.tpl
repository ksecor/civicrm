{* Additional Detail / Honoree Information / Premium Information  Fieldset *}
<fieldset>
   <div class="tundra">
      {foreach from=$allPanes key=paneName item=paneValue}
        {if $paneValue.open eq 'true'}
           <div id="{$paneValue.id}" href="{$paneValue.url}" dojoType="civicrm.TitlePane"  title="{$paneName}" open="{$paneValue.open}" width="200" executeScript="true"></div>
        {else}
           <div id="{$paneValue.id}" dojoType="civicrm.TitlePane"  title="{$paneName}" open="{$paneValue.open}" href ="{$paneValue.url}" executeScript="true"></div>
        {/if}
      {/foreach}
   </div>
</fieldset>
