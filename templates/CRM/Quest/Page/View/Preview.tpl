{* Preview.tpl: wrapper for Quest Applications in with action=preview. Provides complete HTML doc. Can includes print media stylesheet.*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">

<head>
  <title>{$pageTitle}</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <base href="{$config->resourceBase}" />
  <style type="text/css" media="screen, print">@import url({$config->resourceBase}css/civicrm.css);</style>
  <style type="text/css" media="print">@import url({$config->resourceBase}css/print.css);</style>
</head>

<body>

{if $smarty.get.smartyDebug}
{debug}
{/if}
{if $smarty.get.sessionReset}
{$session->reset()}
{/if}
{if $smarty.get.sessionDebug}
{$session->debug($smarty.get.sessionDebug)}
{/if}

<div id="crm-container">

{foreach from=$pageHTML key=pageTitle item=page}
    {$page}
    <hr size=1 noshade>
{/foreach}

</div> {* end crm-container div *}
</body>
</html>
