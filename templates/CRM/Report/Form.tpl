{* this div is being used to apply special css *}
<div id="searchForm">

{if !$printOnly} {* NO print section starts *}
 <div id="id_{$formTpl}_show" class="section-hidden section-hidden-border">
   <a href="#" onclick="hide('id_{$formTpl}_show');show('id_{$formTpl}'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}Report Criteria{/ts}</label><br /></div>
 <div id="id_{$formTpl}"> {* search section starts *}
   <fieldset><legend><a href="#" onclick="hide('id_{$formTpl}'); show('id_{$formTpl}_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}Report Criteria{/ts}</legend>

   <fieldset><legend>{ts}Display Columns{/ts}</legend>
      {foreach from=$colGroups item=grp key=dnc}
         {if $dnc neq 0}<br/>{/if}
         <table class="form-layout"><tr><td width="25%">{$form.select_columns[$grp].html}</td></tr></table>
      {/foreach}
   </fieldset>

   {if $groupByElements}
   <fieldset><legend>{ts}Group by Columns{/ts}</legend>
      <table class="form-layout"><tr>
      {foreach from=$groupByElements item=gbElem key=dnc}
         <td width="25%">{$form.group_bys[$gbElem].html}{if $form.group_bys_freq[$gbElem].html},&nbsp;&nbsp;{$form.group_bys_freq[$gbElem].label}&nbsp;{$form.group_bys_freq[$gbElem].html}{/if}</td>
      {/foreach}
      </table></tr>
   </fieldset>
   {/if}

   {if $form.options.html}
   <fieldset><legend>{ts}Other Options{/ts}</legend>
      <table class="form-layout">
         <tr><td width="25%">{$form.options.html}</td></tr>
      </table>
   </fieldset>
   {/if}

   <fieldset><legend>{ts}Set Filters{/ts}</legend>
      <table class="form-layout">
      {foreach from=$filters     item=table key=tableName}
      {foreach from=$table       item=field key=fieldName}
         {assign var=fieldOp     value=$fieldName|cat:"_op"}
         {assign var=filterVal   value=$fieldName|cat:"_value"}
         {assign var=filterMin   value=$fieldName|cat:"_min"}
         {assign var=filterMax   value=$fieldName|cat:"_max"}
         {if $field.type eq 12}
            <tr><td style="vertical-align: top;">{$field.title}</td><td colspan=2>{include file="CRM/Core/DateRange.tpl" fieldName=$fieldName}</td></tr>
         {else}
            <tr><td width="20%">{$field.title}</td>
                <td width="20%">{$form.$fieldOp.html}</td>
                <td id="{$filterVal}_cell">{$form.$filterVal.label}:&nbsp;{$form.$filterVal.html}</td>
                <td id="{$filterMin}_max_cell">&nbsp;&nbsp;&nbsp;{$form.$filterMin.label}:&nbsp;{$form.$filterMin.html}&nbsp;&nbsp;{$form.$filterMax.label}:&nbsp;{$form.$filterMax.html}</td>
            </tr>
         {/if}
      {/foreach}
      {/foreach}
      </table>
   </fieldset>

   {literal}
   <script type="text/javascript">
      {/literal}
      {foreach from=$filters     item=table key=tableName}
      {foreach from=$table       item=field key=fieldName}
         {literal}showHideMaxMinVal( "{/literal}{$fieldName}{literal}", "dnc" );{/literal}
      {/foreach}
      {/foreach}
      {literal}

      function showHideMaxMinVal( field, val ) {
         var fldVal    = field + "_value_cell";
         var fldMinMax = field + "_min_max_cell";
         if ( val == "bw" || val == "nbw" ) {
           cj('#' + fldVal ).hide();
           cj('#' + fldMinMax ).show();
         } else {
           cj('#' + fldVal ).show();
           cj('#' + fldMinMax ).hide();
         }
      }
   </script>
   {/literal}
 </div> {* search div section ends *}


   {if $instanceForm} {* settings section starts *}
      <div id="id_{$instanceForm}_show" class="section-hidden section-hidden-border">
         <a href="#" onclick="hide('id_{$instanceForm}_show'); show('id_{$instanceForm}'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}Report Settings{/ts}</label><br /></div>

      <div id="id_{$instanceForm}">
         <fieldset><legend><a href="#" onclick="hide('id_{$instanceForm}'); show('id_{$instanceForm}_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}Report Settings{/ts}</legend>
         {include file="CRM/Report/Form/Instance.tpl"}
      </div>

   {/if} {* settings section ends *}


   {* all the buttons *}
   <div id="crm-submit-buttons">{$form.buttons.html}</div>

   <script type="text/javascript">
      var showBlocks = [];
      var hideBlocks = [];

      {if $rows}
         showBlocks[0] = "id_{$formTpl}_show";
	 hideBlocks[0] = "id_{$formTpl}";
      {else}
	 hideBlocks[0] = "id_{$formTpl}_show";
         showBlocks[0] = "id_{$formTpl}";
      {/if}

      {if $instanceForm and $rows}
	 hideBlocks[1] = "id_{$instanceForm}";
         showBlocks[1] = "id_{$instanceForm}_show";
      {/if}

      {* hide and display the appropriate blocks as directed by the php code *}
      on_load_init_blocks( showBlocks, hideBlocks );
   </script>

{/if} {* NO print section ends *}


{* search result listing *}
{include file="CRM/Report/Form/Layout/Table.tpl"}

</div>
{* special div ends *}
