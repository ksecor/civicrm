{* error.tpl: Display page for fatal errors. Provides complete HTML doc.*}
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
<div style="padding: 1em; background-color: #0C0; border: 1px #070 solid; color: white; font-weight: bold">
<form method="post">
<fieldset><legend>{$upgradeTitle}</legend>
    <p>{$upgradeMessage}&nbsp;<input type="submit" value="Upgrade Now" name="upgrade" onclick="return confirm('Are you sure you have taken complete backup of current database and ready to Upgrade CiviCRM?');"/></p>
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
