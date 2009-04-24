{* Advanced Search Criteria Fieldset *}
{literal}
<script type="text/javascript">
var showPane = "";
cj(function() {
  cj('.accordion .head').addClass( "ui-accordion-header ui-helper-reset ui-state-default ui-corner-all ");
  cj('.ui-accordion .ui-accordion-header').css( 'width', '98%' );
  cj('.accordion .head').hover( function() { cj(this).addClass( "ui-state-hover");
                             }, function() { cj(this).removeClass( "ui-state-hover");
               }).bind('click', function() { 
		                 var checkClass = cj(this).find('span').attr( 'class' );
					     var len        = checkClass.length;
					     if( checkClass.substring( len - 1, len ) == 's' ) {
					       cj(this).find('span').removeClass().addClass('ui-icon ui-icon-triangle-1-e');
					     } else {
					       cj(this).find('span').removeClass().addClass('ui-icon ui-icon-triangle-1-s');
					     }
					     cj(this).next().toggle('blind'); return false; }).next().hide();
  if ( showPane.length > 1 ) {
    eval("showPane =[ '" + showPane.substring( 0,showPane.length - 2 ) +"]");
    cj.each( showPane, function( index, value ) {
      cj('span#'+value).removeClass().addClass('ui-icon ui-icon-triangle-1-s');
      cj('div#'+value).attr( 'display', 'block');
    });
  }
});
</script>
{/literal}
<fieldset>
    <legend><span id="searchForm_hide"><a href="#" onclick="hide('searchForm','searchForm_hide'); show('searchForm_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}" /></a></span>
        {if $context EQ 'smog'}{ts}Find Members within this Group{/ts}
        {elseif $context EQ 'amtg'}{ts}Find Contacts to Add to this Group{/ts}
        {elseif $savedSearch}{ts 1=$savedSearch.name}%1 Smart Group Criteria{/ts} &nbsp; {help id='id-advanced-smart'}
        {else}{ts}Search Criteria{/ts}{/if}
    </legend>

<div class="form-item">
{strip}
    <div id="basicCriteria" class="content-pane">
        {include file="CRM/Contact/Form/Search/Criteria/Basic.tpl"}
    </div>
    <div class="accordion ui-accordion ui-widget ui-helper-reset">
      {foreach from=$allPanes key=paneName item=paneValue}
       <h3 class="head"><span class="ui-icon ui-icon-triangle-1-e" id="{$paneValue.id}"></span><a href="#">{$paneName}</a></h3>
    {include file=$paneValue.template}
    {if $paneValue.open eq 'true'}
        {literal}<script type="text/javascript"> showPane += "{/literal}{$paneValue.id}{literal}"+"','";</script>{/literal}
    {/if}
    {/foreach}
    </div>
    <div class="spacer"></div>

    <table class="form-layout">
        <tr>
            <td>{$form.buttons.html}</td>
        </tr>
    </table>
{/strip}
</div>
</fieldset>
