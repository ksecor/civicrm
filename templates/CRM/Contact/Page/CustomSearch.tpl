{strip}
<fieldset>
{if $rows}
    {foreach from=$rows item=customTitle key=csid}
        <div class="action-link">
            <a href="{crmURL p="civicrm/contact/search/custom" q="csid=`$csid`&reset=1"}" title="{ts}Use this search{/ts}">&raquo; {$customTitle}</a>
        </div>
    {/foreach}
{else}
    <div class="messages status">
      <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        <dd>
            {ts}There are currently no Custom Searches.{/ts}
        </dd>
      </dl>
    </div>
{/if}
</fieldset>
{/strip}
