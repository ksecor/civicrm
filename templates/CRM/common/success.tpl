{* success.tpl: Display page for Upgrades. Provides complete HTML doc.*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">

<head>
  <title>{$pageTitle}</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <base href="{$config->resourceBase}" />
  <style type="text/css" media="screen">@import url({$config->resourceBase}css/civicrm.css);</style>
</head>

<body style="border: 1px #CCC solid;
             margin: 3em;
             padding: 8em 1em 1em 1em;
             font-family: arial, verdana, sans-serif;
             background: url({$config->userFrameworkResourceURL}i/block_small.png) 4.2em 4em no-repeat #fff;">

<div id="crm-container" lang="{$config->lcMessages|truncate:2:"":true}" xml:lang="{$config->lcMessages|truncate:2:"":true}">

{if !$upgraded}
    <h2>{$upgradeTitle}</h2>

    <div style="padding: 1em; background-color: #0C0; border: 1px #070 solid; color: white; font-weight: normal">
    <form method="post">
    <fieldset>
        <p>{ts 1=$currentVersion 2=$newVersion}Use this utility to upgrade your CiviCRM database from %1 to %2.{/ts}</p>
        <p><strong>{ts}Back up your database before continuing.{/ts}</strong>
            {ts 1="http://wiki.civicrm.org/confluence/x/mQ8"}This process may change your database structure and values.
            In case of emergency you may need to revert to a backup. For more detailed information, refer to the <a href="%1" target="_blank" style="color: white;">2.2 Upgrade documentation</a>.{/ts}</p>
        <p>{ts}Click 'Upgrade Now' if you are ready to proceed. Otherwise click 'Cancel' to return to the CiviCRM home page.{/ts}</p>
        <input type="submit" value="{ts}Upgrade Now{/ts}" name="upgrade" onclick="return confirm('{ts}Are you sure you are ready to upgrade now?{/ts}');" /> &nbsp;&nbsp;
        <input type="button" value="{ts}Cancel{/ts}" onclick="window.location='{$cancelURL}';" />
    </fieldset>
    </form>
    </div>

{else}
    <h2>{$pageTitle}</h2>

    <div style="padding: 1em; background-color: #0C0; border: 1px #070 solid; color: white; font-weight: bold">
        <p>{$message}</p>
        <p><a href="{$menuRebuildURL}" title="{ts}CiviCRM home page{/ts}">{ts}Return to CiviCRM home page.{/ts}</a></p>
    </div>
{/if}

</div> {* end crm-container div *}

</body>
</html>
