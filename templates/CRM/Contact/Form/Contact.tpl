{* This form is for Contact Add/Edit interface *}
{if $addBlock}
{include file="CRM/Contact/Form/Edit/$blockName.tpl"}
{else}
<div class="crm-submit-buttons">
   {$form.buttons.html}
</div>
<span style="float:right;"><a href="#expand" id="expand">{ts}Expand all tabs{/ts}</a></span>
<br/>
<div class="accordion ui-accordion ui-widget ui-helper-reset">
    <h3 class="head"> 
        <span class="ui-icon ui-icon-triangle-1-e" id='contact'></span><a href="#">{ts}Contact Details{/ts}</a>
    </h3>
    <div id="contact-details">
        {include file="CRM/Contact/Form/Edit/$contactType.tpl"}
        <br/>
        <table class="form-layout-compressed">
            {foreach from=$blocks item="status" key="block"}
                {if $status }
                    {include file="CRM/Contact/Form/Edit/$block.tpl"}
                {/if}
            {/foreach}            
        </table>
    </div>
    
    {foreach from = $editOptions item = "title" key="name"}
            {include file="CRM/Contact/Form/Edit/$name.tpl"}
    {/foreach}
    
</div>
<br/>
<div class="crm-submit-buttons">
   {$form.buttons.html}
</div>

{literal}
<script type="text/javascript" >
cj(function( ) {
    cj('.accordion .head').addClass( "ui-accordion-header ui-helper-reset ui-state-default ui-corner-all");

    cj('.accordion .head').hover( function( ) { 
        cj(this).addClass( "ui-state-hover");
    }, function() { 
        cj(this).removeClass( "ui-state-hover");
    }).bind('click', function( ) { 
        var checkClass = cj(this).find('span').attr( 'class' );
        var len        = checkClass.length;
        if ( checkClass.substring( len - 1, len ) == 's' ) {
            cj(this).find('span').removeClass( ).addClass('ui-icon ui-icon-triangle-1-e');
        } else {
            cj(this).find('span').removeClass( ).addClass('ui-icon ui-icon-triangle-1-s');
        }
        cj(this).next( ).toggle('blind'); return false; 
    }).next( ).hide( );
    
    cj('span#contact').removeClass( ).addClass('ui-icon ui-icon-triangle-1-s');
    cj("#contact-details").show( );
});

cj( function( ) {
{/literal}
{if $loadDefaultBlocks} 
    {foreach from=$defaultBlocksCount key="blockName" item="count"}
      //reset count to 1 since each time counter get increamented.
      cj( "#hidden_" + "{$blockName}" + "_Count" ).val( 1 );
      {section name=blockCount loop=$count-1}
      buildAdditionalBlocks( '{$blockName}', '{$contactType}' );
      {/section}
    {/foreach}
{/if}
{literal}
});

function buildAdditionalBlocks( blockName, contactType ) {


var previousBlockCount = cj( "#hidden_" + blockName + "_Count" ).val( );
var currentBlockCount  = parseInt( previousBlockCount ) + 1; 

  var dataUrl = {/literal}"{crmURL p='civicrm/contact/add' h=0 q='snippet=4&ct='}"{literal} + contactType + '&block=' + blockName + '&count=' + currentBlockCount;
	
  {/literal}{if $action eq 2}
  dataUrl += "&action=update&cid={$entityID}";
  {/if}{literal}
   
  blockId = (cj('#' + blockName + '_Block_'+ previousBlockCount ).html()) ? previousBlockCount : 1;  
  var fname = '#' + blockName + '_Block_'+ blockId;

  cj('#addMore' + blockName ).hide();
  cj.ajax({ 
            url     : dataUrl,   
            async   : false,
            success : function(html){
                         var html = html.split('<!-Add->',2);
                         cj(fname).after(html[1]);
                      }
         });
  cj( "#hidden_" + blockName + "_Count" ).val( currentBlockCount );
  if ( blockName == 'Address' ) cj("#addressBlock").show( );
 
}

cj('a#expand').click( function( ){
     if( cj(this).attr('href') == '#expand') {   
          var message = {/literal}{ts}"Collapse all tabs"{/ts}{literal};
          var class   = 'ui-icon ui-icon-triangle-1-s';
          var event   = 'show';
          cj(this).attr('href', '#collapse');
     } else {
          var message = {/literal}{ts}"Expand all tabs"{/ts}{literal};
          var class   = 'ui-icon ui-icon-triangle-1-e';
          var event   = 'hide';
          cj(this).attr('href', '#expand');
     }
          cj(this).html(message);
          cj('div.accordion div').each(function() {
             cj(this).parent().find('h3 span').removeClass( ).addClass(class);
                 eval( " var me = cj(this)." + event + "();" ); 
          }); 
});

//current employer default setting
var employerId = "{/literal}{$currentEmployer}{literal}";
if( employerId ) {
   var dataUrl = "{/literal}{crmURL p='civicrm/ajax/search' h=0 q="org=1&id=" }{literal}" + employerId ;
    cj.ajax({ 
        url     : dataUrl,   
        async   : false,
        success : function(html){
                    //fixme for showing address in div
                    htmlText = html.split( '|' , 2);
                    cj('input#current_employer').val(htmlText[0]);
                    cj('input#current_employer_id').val(htmlText[1]);
                  }
            }); 
}

//select single is_bulk & is_primary
function singleSelect( object, element ){
cj('input#'+element).each(function() { 
    if( cj(object).attr('name') != cj(this).attr('name') ) {
        cj(this).attr( 'checked', false );
    }
});
}
</script>
{/literal}
{/if}