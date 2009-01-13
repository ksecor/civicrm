{if $groupTree}
{foreach from=$groupTree item=cd_edit key=group_id}
{if $showHideLinks}
  <div id="{$cd_edit.name}_show" class="section-hidden section-hidden-border">
    <a href="#" onclick="hide('{$cd_edit.name}_show'); show('{$cd_edit.name}'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}" /></a><label>{ts}{$cd_edit.title}{/ts}</label><br />
  </div>
{/if}

  <div id="{$cd_edit.name}" class="form-item">
  <fieldset><legend>
{if $showHideLinks}
<a href="#" onclick="hide('{$cd_edit.name}'); show('{$cd_edit.name}_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}" /></a>
{/if}
{ts}{$cd_edit.title}{/ts}</legend>
    <dl>
    {foreach from=$cd_edit.fields item=element key=field_id}
      {assign var="element_name" value='custom_'|cat:$field_id}
      {if $element.options_per_line != 0}
         <dt>{$form.$element_name.label}</dt>
         <dd>
            {assign var="count" value="1"}
            {strip}
            <table class="form-layout-compressed">
            <tr>
                {* sort by fails for option per line. Added a variable to iterate through the element array*}
                {assign var="index" value="1"}
                {foreach name=outer key=key item=item from=$form.$element_name}
                {if $index < 10} {* Hack to skip QF field properties that aren't checkbox elements. *}
                    {assign var="index" value=`$index+1`}
                {else}
                    {if $element.html_type EQ 'CheckBox' AND  $smarty.foreach.outer.last EQ 1} {* Put 'match ANY / match ALL' checkbox in separate row. *}
                        </tr>
                        <tr>
                        <td class="op-checkbox" colspan="{$element.options_per_line}" style="padding-top: 0px;">{$form.$element_name.$key.html}</td>
                    {else}
                        <td class="labels font-light">{$form.$element_name.$key.html}</td>
                        {if $count EQ $element.options_per_line}
                          </tr>
                          <tr>
                          {assign var="count" value="1"}
                        {else}
                          {assign var="count" value=`$count+1`}
                        {/if}
                    {/if}
                {/if}
                {/foreach}
            </tr>
            {if $element.html_type eq 'Radio'}
                <tr style="line-height: .75em; margin-top: 1px;">
                    <td> &nbsp; <a href="#" title="unselect" onclick="unselectRadio('{$element_name}', '{$form.formName}'); return false;">{ts}unselect{/ts}</a></td>
                </tr>
            {/if}
            </table>
            {/strip}
            </dd>
        {else}
            {assign var="type" value=`$element.html_type`}
            {assign var="element_name" value='custom_'|cat:$field_id}
            {if $element.is_search_range}
                {assign var="element_name_from" value=$element_name|cat:"_from"}
                {assign var="element_name_to" value=$element_name|cat:"_to"}
                <dt>{$form.$element_name_from.label}</dt><dd>
                {$form.$element_name_from.html|crmReplace:class:six}
                    &nbsp;&nbsp;{$form.$element_name_to.label}&nbsp;&nbsp;{$form.$element_name_to.html|crmReplace:class:six}
            {else}
                <dt>{$form.$element_name.label}</dt><dd>&nbsp;{$form.$element_name.html}
            {/if}
            {if $element.html_type eq 'Radio'}
                &nbsp; <a href="#" title="unselect" onclick="unselectRadio('{$element_name}', '{$form.formName}'); return false;">{ts}unselect{/ts}</a>
            {/if}
            </dd>
	    {/if}
	    {/foreach}
	    </dl>
	 </fieldset>
    </div>
  {/foreach}
{/if}

