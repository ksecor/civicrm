<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
  <title>{$title}</title>
  <meta http-equiv="Content-Style-Type" content="text/css" />
  {$head}
  {$styles}

  <!-- Add our own specific styles etc for rms, need to figure out if rms is invoked -->
  {include file="CRM/core/head.tpl"}  

</head>
<body 
{php}
  print theme("onload_attribute"); 
{/php}
>
<div id="header">
  {if $search_box}
	<form action="{php} print url("search") {/php}" method="post">
		<div id="search">
			<input class="form-text" type="text" size="15" value="" name="keys" /><input class="form-submit" type="submit" value="{$search_button_text}" />
		</div>
	</form>
  {/if}
  {if $logo}
    <a href="{php} print url() {/php}" title="Index Page"><img src="{$logo}" alt="Logo" /></a>
  {/if}
  {if $site_name}
    <h1 id="site-name"><a href="{php} print url() {/php}" title="Index Page">{$site_name}</a></h1>
  {/if}
  {if $site_slogan}
    <span id="site-slogan">{$site_slogan}</span>
  {/if}
  <br class="clear" />
</div>
<div id="top-nav">
  {if $secondary_links}
    <ul id="secondary">
    {foreach from=$secondary_links item=link}
      <li>{$link}</li>
    {/foreach}
    </ul>
  {/if}
	
  {if $primary_links}
    <ul id="primary">
    {foreach from=$primary_links item=link}
      <li>{$link}</li>
    {/foreach}
    </ul>
  {/if}
</div>
<table id="content">
	<tr>
		{if $sidebar_left ne ''}
			<td class="sidebar" id="sidebar-left">
				{$sidebar_left}
			</td>
		{/if}
				<td class="main-content" id="content-{$layout}">
				{if $breadcrumb ne ''}
				  <div> {$breadcrumb} </div>
				{/if}
				{if $title ne ''}
				   <div class="title_menu">{$title}</div>
				   <hr size=1><br>	
				{/if}
				{if $tabs ne ''}
					{$tabs}
				{/if}
				
				{if $mission ne ''}
					<p id="mission">{$mission}</p>
				{/if}
				
				{if $help ne ''}
					<p id="help">{$help}</p>
				{/if}
				
				{if $messages ne ''}
					<div id="message">{$messages}</div>
				{/if}
				<!-- start main content -->
				{$content}
				<!-- end main content -->
				</td><!-- mainContent -->		
		{if $sidebar_right ne ''}
		<td class="sidebar" id="sidebar-right">
				{$sidebar_right}
		</td>
		{/if}
	</tr>
</table>
<div id="footer">
  {if $footer_message}
    <p>{$footer_message}</p>
  {/if}
Validate <a href="http://validator.w3.org/check/referer">XHTML</a> or <a href="http://jigsaw.w3.org/css-validator/check/referer">CSS</a>.
</div><!-- footer -->	
 {$closure}
</body>
</html>

