{if isset($langSwitch) and $langSwitch|@count > 1}
  <form action="#">
    <select name="lcMessages" onchange="window.location='{$smarty.server.REQUEST_URI}&lcMessages='+this.value">
      {foreach from=$langSwitch item=language key=locale}
        <option value="{$locale}" {if $locale == $tsLocale}selected="selected"{/if}>{$language}</option>
      {/foreach}
    </select>
  </form>
{/if}
