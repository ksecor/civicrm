{* fatal.tpl: Display page for fatal errors. Provides complete HTML doc.*}
{* minimize any external calls etc, since we are in an unknown state *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">

<head>
  <title>CiviCRM Fatal Error</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <base href="{$config->resourceBase}" />
  <style type="text/css" media="screen">@import url({$config->resourceBase}css/civicrm.css);</style>
</head>

<body>

<div id="crm-container">

<div class="messages status">
  <dl>
  <dt><img src="{$config->resourceBase}i/Error.gif" alt="unrecoverable error" /></dt>
  <dd>
      Sorry. A non-recoverable error has occurred.
      <p>{$message}</p>
{if $code}
      <p>Error Code: {$code}</p>
{/if}
{if $mysql_code}
      <p>Database Error Code: {$mysql_code}</p>
{/if}
  </dd>
  </dl>
</div>

</div> {* end crm-container div *}

</body>
</html>
