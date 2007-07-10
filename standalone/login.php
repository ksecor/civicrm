<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">

<head>
  <title>CiviCRM User Authentication</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>
  <h1 class="title">CiviCRM Login</h1>
    <h2>Please enter your OpenID</h1>

    <div id="verify-form">
      <form method="get" action="try_auth.php">
        Identity&nbsp;URL:
        <input type="hidden" name="action" value="verify" />
        <input type="text" name="openid_url" value="" />
        <input type="submit" value="Verify" />
      </form>
    </div>
   <p>If you don't have an OpenID yet, go to <a href="http://www.myopenid.com/">MyOpenID to get one</a>.</p>
</body>
</html>
