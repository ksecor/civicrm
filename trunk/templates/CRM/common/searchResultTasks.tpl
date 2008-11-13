{* Form elements for displaying and running action tasks on search results for all component searches. *}

<div id="search-status">
  <table class="form-layout-compressed">
  <tr>
    <td class="font-size12pt" style="width: 30%;">
    {if $savedSearch.name}{$savedSearch.name} ({ts}smart group{/ts}) - {/if}
    {ts count=$pager->_totalItems plural='%count Results'}%count Result{/ts}
    </td>
    <td class="nowrap">
        {* Search criteria are passed to tpl in the $qill array *}
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
     {$form._qf_Search_next_print.html} &nbsp; &nbsp;
     {$form.task.html}
     {$form._qf_Search_next_action.html} 
    </td>
  </tr>
  </table>
</div>
{literal}
<script type="text/javascript">
toggleTaskAction( );
</script>
{/literal}
