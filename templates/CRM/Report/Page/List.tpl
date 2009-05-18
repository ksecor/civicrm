{strip}
    <fieldset>
        <legend>{ts}Report List{/ts}</legend>
        {if $list}
            {foreach from=$list item=row}
                <div class="action-link">
                    <a href="{$row.1}">&raquo; {$row.0}</a>
                </div>
            {/foreach}
        {else}
            <div class="messages status">
            <dl>
                <dt>
                    <img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/>
                </dt>
                <dd>
                    {ts}There are currently no Report.{/ts}
                </dd>
            </dl>
            </div>
        {/if}
    </fieldset>
{/strip}