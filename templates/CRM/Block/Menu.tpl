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
function getSearchURLValue( )
{
    var contactId =  cj( '#contact_id' ).val();
    if ( ! contactId || isNaN( contactId ) ) {
        var sortValue = cj( '#sort_name' ).val();
        if ( sortValue ) { 
            //using xmlhttprequest check if there is only one contact and redirect to view page
            var dataUrl = {/literal}"{crmURL p='civicrm/ajax/contact' h=0 q='name='}"{literal} + sortValue;

            var response = cj.ajax({
                url: dataUrl,
                async: false
                }).responseText;

            contactId = response;
        }
    }

    
    if ( contactId ) {
        var url = {/literal}"{crmURL p='civicrm/contact/view' h=0 q='reset=1&cid='}"{literal} + contactId;
        document.getElementById('id_search_block').action = url;
    }
}
cj( function( ) {
   cj('ul.civicrm_menu li').hover( function(){
        cj(this).find('ul:first').show();
    }, function(){
        cj(this).find('ul:first').hide();
    }); 
    
    var contactUrl = {/literal}"{crmURL p='civicrm/ajax/contactlist' h=0 }"{literal};

    cj( '#sort_name' ).autocomplete( contactUrl, {
        width: 200,
        selectFirst: false 
    }).result(function(event, data, formatted) {
        cj("#contact_id").val(data[1]);
    });	
});

if ( !cj("#civicrm_menu").text() ) {
    cj.ajax({
        type        : "POST",
        contentType : "application/json; charset=utf-8",
        dataType    : "json",
        url         : {/literal}"{crmURL p='civicrm/ajax/adminmenu' h=0 }"{literal},
        success     : function( link ) {
                        var postURL = {/literal}"{crmURL p='civicrm/contact/search/basic' h=0 q='reset=1'}"{literal};
                        html  = '<div id="civicrm_menu"><ul class="civicrm_menu civicrm_menu_slate"><li id="crm-qsearch"><form action="'+ postURL +'" name="search_block" id="id_search_block" method="post" onsubmit="getSearchURLValue( );"><input type="text" class="form-text" id="sort_name" name="sort_name" style="width: 12em;"/><input type="hidden" id="contact_id" value=""><input type="submit" value="{ts}Go{/ts}" name="_qf_Basic_refresh" class="form-submit default" style="display: none;"/></form></li>'+link+'</ul></div>';
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
