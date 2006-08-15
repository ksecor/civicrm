{* Print.tpl: wrapper for Print views. Provides complete HTML doc. Includes print media stylesheet.*}
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

<div id="crm-container" lang="{$config->lcMessages|truncate:2:"":true}" xml:lang="{$config->lcMessages|truncate:2:"":true}">

{* Check for Status message for the page (stored in session->getStatus). Status is cleared on retrieval. *}
{if $session->getStatus(false)}
<div class="messages status">
  <dl>
  <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
  <dd>{$session->getStatus(true)}</dd>
  </dl>
</div>
{/if}

<!-- .tpl file invoked: {$tplFile}. Call via form.tpl if we have a form in the page. -->
{if $isForm}
    {include file="CRM/Form/$formTpl.tpl"}
{else}
    {include file=$tplFile}
{/if}


</div> {* end crm-container div *}
</body>
</html>
