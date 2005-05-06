{if $smarty.get.smartyDebug}
{debug}
{/if}
{if $smarty.get.sessionReset}
{$session->reset()}
{/if}
{if $smarty.get.sessionDebug}
{$session->debug($smarty.get.sessionDebug)}
{/if}

<!-- .tpl file invoked: {$tplFile} -->

<div id="crm-container">
<script type="text/javascript" src="{$config->resourceBase}js/Common.js"></script>

{* Check for Status message for the page (stored in session->getStatus). Status is cleared on retrieval. *}
{if $session->getStatus(false)}
    {assign var="status" value=$session->getStatus(true)}
    <div class="messages status">
      <dl>
      <dt><img src="{$config->resourceBase}i/Inform.gif" alt="status"></dt>
      <dd>
        {if is_array($status)}
            {foreach name=statLoop item=statItem from=$status}
                {if $foreach.statLoop.first}
                    <h3>{$statItem}</h3>
                    <ul>
                {else}
                    <li>{$statItem}
                {/if}
                </ul>
            {/foreach}
        {else}
            {$status}
        {/if}
      </dd>
      </dl>
    </div>
{/if}

{include file=$tplFile}

<div class="message status" id="feedback-request">
     Please add your comments on the look and feel of these pages along, with workflow issues on the
     <a href="http://objectledge.org/confluence/display/CRM/Demo">CiviCRM Comments Page</a>.
     <p>Please do not file bug reports at this time.</p>
</div>

</div> {* end crm-container div *}
