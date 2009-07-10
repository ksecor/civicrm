<h3 class="head"> 
    <span class="ui-icon ui-icon-triangle-1-e"></span><a href="#">{ts}{$title}{/ts}</a>
</h3>
<div id="tag-group" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom">

<table class="form-layout-compressed" style="width:98%">
    <tr>
        {foreach key=key item=item from=$tagGroup}
        <td width={cycle name=tdWidth values="70%","30%"}><span class="label">{$form.$key.label}</span>
            <table>
            {foreach key=k item=it from=$form.$key}
                {if $k|is_numeric}
                    <tr class={cycle values="'odd-row','even-row'" name=$key}><td><strong>{$it.html}</strong><br/>
                     {if $item.$k.description}<div style="font-size:10px;padding-left:20px;">{$item.$k.description}</div>{/if}</td></tr>
                {/if}
             {/foreach}   
            </table>
        </td>
        {/foreach}
    </tr>
</table>    
</div>