{* Form elements for displaying and running action tasks on search results *}

 <div id="search-status">
    {if $savedSearch.name}{$savedSearch.name} ({ts}smart group{/ts}) - {/if}
    {ts count=$pager->_totalItems plural='Found %count participants.'}Found %count participant.{/ts}

    {* Search criteria are passed to tpl in the $qill array *}
    {if $qill}
        {include file="CRM/common/displaySearchCriteria.tpl"}
    {/if}

 </div>

<div class="form-item"> 
  <div> 
     {$form._qf_Search_next_print.html}&nbsp;&nbsp;
     {$form.task.html}
     {$form._qf_Search_next_action.html} 
     <br /> 
     <label>{$form.radio_ts.ts_sel.html} {ts}selected records only{/ts}</label>&nbsp; <label>{$form.radio_ts.ts_all.html} {ts count=$pager->_totalItems plural='all %count records'}the found record{/ts}</label> 
   </div>
</div>
