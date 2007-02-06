{* fatal.tpl: Display page for fatal errors. Provides complete HTML doc.*}
{* minimize any external calls etc, since we are in an unknown state *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">

<head>
  <title>CiviCRM Fatal Error</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>

<div id="crm-container">

<div style="border: solid 2px black; padding: 1em 1em 1em 1em;">
      <p style="color: red; font-weight: bold;">Sorry. A non-recoverable error has occurred.</p>
      <p>Please review the <a href="http://wiki.civicrm.org/confluence//x/mQ8" target="_blank">CiviCRM Installation Guide</a> and try searching
      the <a href="http://www.nabble.com/CiviCRM-Community-Mailing-List-Archives-f15986.html" target="_blank">CiviCRM Mailing List Archives</a> for information on the error below.</p>
{if $message}
    <hr style="solid 1px" />
    <p>{$message}</p>
{/if}
{if $code}
    <hr style="solid 1px" />
    <p>Error Code: {$code}</p>
{/if}
{if $mysql_code}
    <hr style="solid 1px" />
    <p>Database Error Code: {$mysql_code}</p>
{/if}
{if $error}
    <hr style="solid 1px" />
    <p>Error Details:</p>
    <p>{$error.to_string}</p>
{/if}
</div>

</div> {* end crm-container div *}

</body>
</html>
