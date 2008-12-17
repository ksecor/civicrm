{* error.tpl: Display page for fatal errors. Provides complete HTML doc.*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">

<head>
  <title>{$pageTitle}</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <base href="{$config->resourceBase}" />
  <style type="text/css" media="screen">@import url({$config->resourceBase}css/civicrm.css);</style>
</head>

<body>

<div id="crm-container" lang="{$config->lcMessages|truncate:2:"":true}" xml:lang="{$config->lcMessages|truncate:2:"":true}">

<div class="messages status">
  <dl>
  <dt><img src="{$config->resourceBase}i/Error.gif" alt="{ts}UNRECOVERABLE ERROR{/ts}" /></dt>
  <dd>
      <p>{$message}</p>
      <p><a href="{$dashboardURL}" title="{ts}Main Menu{/ts}">{ts}Return to home page.{/ts}</a></p>
  </dd>
  </dl>
</div>

</div> {* end crm-container div *}

</body>
</html>
