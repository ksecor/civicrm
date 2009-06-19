{if $outputMode eq 'html' && !$rows}
    <div class="messages status">
        <dl>
            <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
            <dd>{ts}Sorry. No results found.{/ts}</dd>
        </dl>
    </div>
{/if}
