{* Custom Data view mode*}
{* Build left block*}
<div id="contactCardLeft">
{foreach from=$viewCustomData item=customValues key=customGroupId}
    {cycle name=buildLeft values="odd,even" assign=leftDiv}
    {if $leftDiv eq 'odd'}
        {foreach from=$customValues item=cd_edit key=cvID}
            <div class="customFieldGroup ui-corner-all">
                <table>
                  <tr>
                    <td colspan="2" class="grouplabel">{$cd_edit.title}</td>
                  </tr>
                  {foreach from=$cd_edit.fields item=element key=field_id}
                  {if $element.options_per_line != 0}
                  <tr>
                        <td class="label">{$element.field_title}</td>
                        <td>
                            {* sort by fails for option per line. Added a variable to iterate through the element array*}
                            {foreach from=$element.field_value item=val}
                                {$val}
                            {/foreach}
                        </td>
                    {else}
                        <td class="label">{$element.field_title}</td>
                        {if $element.field_type == 'File'}
                            {if $element.field_value.displayURL}
                                <td><a href="javascript:imagePopUp('{$element.field_value.displayURL}')" ><img src="{$element.field_value.displayURL}" height = "100" width="100"></a></td>
                            {else}
                                <td class="html-adjust"><a href="{$element.field_value.fileURL}">{$element.field_value.fileName}</a></td>
                            {/if}
                        {else}
                            <td class="html-adjust">{$element.field_value}</td>
                        {/if}
                  </tr>
                  {/if}
                {/foreach}
                </table>
            </div>
        {/foreach}
    {/if}
{/foreach}
</div>

{* Build right block*}
<div id="contactCardRight">
{foreach from=$viewCustomData item=customValues key=customGroupId}
    {cycle name=buildRight values="odd,even" assign=rightDiv}
    {if $rightDiv eq 'even'}
        {foreach from=$customValues item=cd_edit key=cvID}
            <div class="customFieldGroup ui-corner-all">
                <table>
                  <tr>
                    <td colspan="2" class="grouplabel">{$cd_edit.title}</td>
                  </tr>
                  {foreach from=$cd_edit.fields item=element key=field_id}
                  {if $element.options_per_line != 0}
                  <tr>
                        <td class="label">{$element.field_title}</td>
                        <td>
                            {* sort by fails for option per line. Added a variable to iterate through the element array*}
                            {foreach from=$element.field_value item=val}
                                {$val}
                            {/foreach}
                        </td>
                    {else}
                        <td class="label">{$element.field_title}</td>
                        {if $element.field_type == 'File'}
                            {if $element.field_value.displayURL}
                                <td><a href="javascript:imagePopUp('{$element.field_value.displayURL}')" ><img src="{$element.field_value.displayURL}" height = "100" width="100"></a></td>
                            {else}
                                <td class="html-adjust"><a href="{$element.field_value.fileURL}">{$element.field_value.fileName}</a></td>
                            {/if}
                        {else}
                            <td class="html-adjust">{$element.field_value}</td>
                        {/if}
                  </tr>
                  {/if}
                {/foreach}
                </table>
            </div>
        {/foreach}
    {/if}
{/foreach}
</div>