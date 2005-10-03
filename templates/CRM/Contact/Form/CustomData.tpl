<div class="form-item">
{strip}
{foreach from=$groupTree item=cd_edit key=group_id}

    <div id="{$cd_edit.title}[show]" class="data-group">
    <a href="#" onclick="hide('{$cd_edit.title}[show]'); show('{$cd_edit.title}'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}{$cd_edit.title}{/ts}</label><br />
    </div>

    <div id="{$cd_edit.title}">
    <p></p>
    <fieldset><legend><a href="#" onclick="hide('{$cd_edit.title}'); show('{$cd_edit.title}[show]'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}{$cd_edit.title}{/ts}</legend>
    {if $cd_edit.help_pre}<div class="messages help">{$cd_edit.help_pre}</div><br />{/if}
    <dl>
    {foreach from=$cd_edit.fields item=element key=field_id}
	{if $element.options_per_line > 1 }
	{assign var="element_name" value=$group_id|cat:_|cat:$field_id|cat:_|cat:$element.name}			
	<dt>{$element.label}</dt>
	<dd>
	{assign var="count" value="1"}
	{strip}
    <table class="form-layout-compressed">
    <tr>
        {* sort by fails for option per line. Added a variable to iterate through the element array*}
        {assign var="index" value="1"}
        {foreach name=outer key=key item=item from=$form.$element_name}
        {if $index < 10}
            {assign var="index" value=`$index+1`}
        {else}
    	    <td class="label font-light">{$form.$element_name.$key.html}</td>
                {if $count == $element.options_per_line}
    	        </tr>
                <tr>
                {assign var="count" value="1"}
    	        {else}
    		    {assign var="count" value=`$count+1`}
    	        {/if}
            {/if}
        {/foreach}
    </tr>
	</table>
    {/strip}
	</dd>
	{else}
              {assign var="name" value=`$element.name`} 
              {assign var="element_name" value=$group_id|cat:_|cat:$field_id|cat:_|cat:$element.name}
              <dt>{$form.$element_name.label}</dt><dd>&nbsp;{$form.$element_name.html}</dd>
        	{if $element.help_post}
            	<dt>&nbsp;</dt><dd class="description">{$element.help_post}</dd>
        	{/if}
	{/if}
    {/foreach}
    </dl>
    </fieldset>
    </div>
{/foreach}
{/strip}

<dl>
  <dt></dt><dd>{$form.buttons.html}</dd>
</dl>  
</div>
