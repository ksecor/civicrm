{include file="CRM/common/jquery.tpl"}
<div class="menu">
    <ul class="indented">
        {foreach from=$menu item=menuItem}
            {if $menuItem.start}<li class="no-display"><ul class="indented">{/if}
            <li class="{$menuItem.class}"><a href="{$menuItem.url}" {$menuItem.active}>{$menuItem.title}</a></li>
            {if $menuItem.end}</ul></li>{/if}
        {/foreach}
    </ul>
</div>

<link type="text/css" rel="stylesheet" href="{$config->resourceBase}packages/jquery/css/jquery.civicrmMenu.css"/>
{if $config->userFramework eq 'Drupal'}
    {literal}<script type="text/javascript">var menuId = "#header-region";</script>{/literal}
{elseif $config->userFramework eq 'Joomla'}
    {literal}
    <script type="text/javascript">var menuId = "#header-box";</script>
    <style type="text/css">#border-top { margin-top: 25px; } ul.civicrm_menu ul li{ margin: 5px 0 5px 0; } </style>
    {/literal}
{else}
    //FIXME:if called by standalone
    {literal}<script type="text/javascript">var menuId = "";</script>{/literal}
{/if}
{literal}
<script type="text/javascript">
cj(function(){
   cj('ul.civicrm_menu li').hover( function(){
        cj(this).find('ul:first').show();
    }, function(){
        cj(this).find('ul:first').hide();
    }); 
});

if ( !cj("#civicrm_menu").text() ) {
  cj.ajax({
    type        : "POST",
    contentType : "application/json; charset=utf-8",
    dataType    : "json",
    url         : {/literal}"{crmURL p='civicrm/ajax/adminmenu' h=0 }"{literal},
    data        : "{}",
    success     : function( link ) {
                    html  = '<div id="civicrm_menu"><ul class="civicrm_menu civicrm_menu_slate"><li>';
  				    html += '<img src={/literal}{$config->resourceBase}i/widget/favicon.png{literal}';
				    html += ' width="20px"/></li>'+link+'</ul></div>';
				    cj(menuId).before(html);
                  }
  });
}

// Track scrolling.
cj(window).scroll( function () {
    var scroll = document.documentElement.scrollTop || document.body.scrollTop;
    cj("#civicrm_menu").css('top', scroll);
});
</script>
{/literal}
