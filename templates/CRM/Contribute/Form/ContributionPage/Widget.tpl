{include file="CRM/common/WizardHeader.tpl"}
{if $widget_id} {* If we have a widget for this page, construct the embed code.*}
    {capture assign=widgetVars}serviceUrl={$config->resourceBase}packages/amfphp/gateway.php&amp;contributionPageID={$id}&amp;widgetId=CiviCRM.Contribute.1{/capture}
    {capture assign=widget_code}
<div style="text-align: center;width:260px">
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="550" height="400" id="widget" align="middle">
<param name="allowScriptAccess" value="sameDomain" />
<param name="FlashVars" value="{$widgetVars}">
<param name="movie" value="widget.swf" />
<param name="quality" value="high" />
<param name="bgcolor" value="#ffffff" />
<embed flashvars="{$widgetVars}" src="{$config->resourceBase}extern/Widget/widget.swf" quality="high" bgcolor="#ffffff" width="220" height="220" name="widget" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</object></div>{/capture}
{/if}

<div id="form" class="form-item">
    <fieldset><legend>{ts}Configure Widget{/ts}</legend>
    <div id="help">
        {ts}Enabling a widget for this contribution page allows you and your supporters to embed a dynamic
        Flash widget in any page. Configure the title, descriptive text and colors of the widget using the fields
        below. Then copy and paste the "Widget Code" into any web page. Embedded widgets dynamically display
        progress toward your fund-raising goals, and include a "Contribute" button.{/ts}
    </div>
    <dl>
    	<dt></dt><dd>{$form.is_active.html}&nbsp;{$form.is_active.label}</dd>
    </dl>
    
    <div id="widgetFields">
        <dl>
        {foreach from=$fields item=field key=name}
          <dt>{$form.$name.label}</dt><dd>{$form.$name.html}</dd>   
        {/foreach}
        </dl>
        
        {* Include "get widget code" section if widget has been created for this page and is_active. *}
        {if $widget_id}
        <div id="id-get_code">
            <fieldset>
            <legend>{ts}Preview Widget and Get Code{/ts}</legend>
            <div class="col1" style="padding-right: 10px">
                <strong>{ts}Add this widget to any web page by copying and pasting the code below.{/ts}</strong><br />
                <textarea rows="8" cols="50" name="widget_code" id="widget_code">{$widget_code}</textarea>
                <br />
                <strong><a href="#" onclick="Widget.widget_code.select(); return false;">&raquo; Select Code</a></strong>
            </div>
            <div class="col2"> 
                <strong>{ts}Preview {/ts}</strong><br />
                {$widget_code} <br /> {$form.preview.html}
            </div>
            </fieldset>
        </div>
        {/if}

        
        <div id="id-colors-show" class="section-hidden section-hidden-border" style="clear: both;">
            <a href="#" onclick="hide('id-colors-show'); show('id-colors'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}Edit Widget Colors{/ts}</label><br />
        </div>
        <div id="id-colors" class="section-shown">
        <fieldset>
        <legend><a href="#" onclick="hide('id-colors'); show('id-colors-show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}Widget Colors{/ts}</legend>
        <dl>
        {foreach from=$colorFields item=field key=name}
          <dt>{$form.$name.label}</dt><dd>{$form.$name.html}</dd>   
        {/foreach}
        </dl>
        </fieldset>
        </div>

    </div>

    {if $action ne 4}
    <div id="crm-submit-buttons">
        <dl><dt></dt><dd>{$form.buttons.html}</dd></dl>  
    </div>
    {else}
    <div id="crm-done-button">
         <dl><dt></dt><dd>{$form.buttons.html}<br></dd></dl>
    </div>
    {/if} {* $action ne view *}
    </fieldset>

</div>      
{include file="CRM/common/showHide.tpl"}

{literal}
<script type="text/javascript">
	var is_act = document.getElementsByName('is_active');
  	if ( ! is_act[0].checked) {
           hide('widgetFields');
	}
    function widgetBlock(chkbox) {
        if (chkbox.checked) {
	      show('widgetFields');
	      return;
        } else {
	      hide('widgetFields');
          return;
	   }
    }
</script>
{/literal}