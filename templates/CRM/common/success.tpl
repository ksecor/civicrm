{* success.tpl: Display page for Upgrades. Provides complete HTML doc.*}
{if $config->userFramework neq 'Drupal'}
    <h2>{$pageTitle}</h2>
{/if}
{if !$upgraded}
    <div style="margin-top: 2em; padding: 1em; background-color: #0C0; border: 1px #070 solid; color: white; font-weight: normal">
    <form method="post">
        <p>{ts 1=$currentVersion 2=$newVersion}Use this utility to upgrade your CiviCRM database from %1 to %2.{/ts}</p>
        <p><strong>{ts}Back up your database before continuing.{/ts}</strong>
            {capture assign=docLink}{docURL page="Installation and Upgrades" text="Upgrade Documentation" style="color: white; text-decoration: underline;"}{/capture}
            {ts 1=$docLink}This process may change your database structure and values.
            In case of emergency you may need to revert to a backup. For more detailed information, refer to the %1.{/ts}</p>
        <p>{ts}Click 'Upgrade Now' if you are ready to proceed. Otherwise click 'Cancel' to return to the CiviCRM home page.{/ts}</p>
        <input type="submit" value="{ts}Upgrade Now{/ts}" name="upgrade" onclick="return confirm('{ts}Are you sure you are ready to upgrade now?{/ts}');" /> &nbsp;&nbsp;
        <input type="button" value="{ts}Cancel{/ts}" onclick="window.location='{$cancelURL}';" />
    </form>
    </div>

{else}
    <div style="margin-top: 3em; padding: 1em; background-color: #0C0; border: 1px #070 solid; color: white; font-weight: bold">
        <p>{$message}</p>
        <p><a href="{$menuRebuildURL}" title="{ts}CiviCRM home page{/ts}" style="color: white; text-decoration: underline;">{ts}Return to CiviCRM home page.{/ts}</a></p>
    </div>
{/if}