{* Form elements for displaying and running action tasks on search results *}
{capture assign=advSearchURL}
{if $context EQ 'smog'}
     {crmURL p='civicrm/group/search/advanced' q="gid=`$group.id`&reset=1&force=1"}
{elseif $context EQ 'amtg'}
     {crmURL p='civicrm/contact/search/advanced' q="context=amtg&amtgID=`$group.id`&reset=1&force=1"}
{else}
    {crmURL p='civicrm/contact/search/advanced' q="reset=1"}
{/if}
{/capture}
{capture assign=searchBuilderURL}
    {crmURL p='civicrm/contact/search/builder' q="reset=1"}
{/capture}

 <div id="search-status">
  <div class="float-right right">
    {if $action eq 256}
        <a href="{$advSearchURL}">&raquo; {ts}Advanced Search{/ts}</a><br />
        {if $context eq 'search'} {* Only show Search Builder link for basic search. *}
            <a href="{$searchBuilderURL}">&raquo; {ts}Search Builder{/ts}</a><br />
        {/if}
        {if $context eq 'smog'}
            {help id="id-smog-criteria"}
        {elseif $context eq 'amtg'}
            {help id="id-amtg-criteria"}
        {else}
            {help id="id-basic-criteria"}
        {/if}
    {elseif $action eq 512}
        <a href="{$searchBuilderURL}">&raquo; {ts}Search Builder{/ts}</a><br />
    {elseif $action eq 8192}
        <a href="{$advSearchURL}">&raquo; {ts}Advanced Search{/ts}</a><br />
    {/if}
  </div>

  <table class="form-layout-compressed">
  <tr>
    <td class="font-size12pt" style="width: 30%;">
    {if $savedSearch.name}{$savedSearch.name} ({ts}smart group{/ts}) - {/if}
    {if $context EQ 'smog' OR $ssID GT 0}
        {ts count=$pager->_totalItems plural='%count Group Members'}%count Group Member{/ts}
    {else}
      {ts count=$pager->_totalItems plural='%count Results'}%count Result{/ts}
    {/if}
    </td>
    
    {* Search criteria are passed to tpl in the $qill array *}
    <td class="nowrap">
    {if $qill}
      {include file="CRM/common/displaySearchCriteria.tpl"}
    {/if}
    </td>
  </tr>
  <tr>
    <td class="font-size11pt"> {ts}Select Records{/ts}:</td>
    <td class="nowrap">
        {$form.radio_ts.ts_all.html} {ts count=$pager->_totalItems plural='All %count records'}The found record{/ts} &nbsp; {$form.radio_ts.ts_sel.html} {ts}Selected records only{/ts}
    </td>
  </tr>
  <tr>
    <td colspan="2">
     {* Hide export and print buttons in 'Add Members to Group' context. *}
     {if $context NEQ 'amtg'}
        {if $action eq 512}
          {$form._qf_Advanced_next_print.html}&nbsp; &nbsp;
        {elseif $action eq 8192}
          {$form._qf_Builder_next_print.html}&nbsp; &nbsp;
        {elseif $action eq 16384}
          {* since this does not really work for a non standard search
          {$form._qf_Custom_next_print.html}&nbsp; &nbsp;
          *}
        {else}
          {$form._qf_Basic_next_print.html}&nbsp; &nbsp;
        {/if}
        {$form.task.html}
     {/if}
     {if $action eq 512}
       {$form._qf_Advanced_next_action.html}
     {elseif $action eq 8192}
       {$form._qf_Builder_next_action.html}&nbsp;&nbsp;
     {elseif $action eq 16384}
       {$form._qf_Custom_next_action.html}&nbsp;&nbsp;
     {else}
       {$form._qf_Basic_next_action.html}
     {/if}
     </td>
  </tr>
  </table>
 </div>

{literal}
<script type="text/javascript">
toggleTaskAction( );
</script>
{/literal}

