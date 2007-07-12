{* Form elements for displaying and running action tasks on search results *}

 <div id="search-status">
  {if $savedSearch.name}{$savedSearch.name} ({ts}smart group{/ts}) - {/if}
    {if $context EQ 'smog' OR $ssID GT 0}
      {ts count=$pager->_totalItems plural='Found %count group members'}Found %count group member{/ts}
   {else}
      {ts count=$pager->_totalItems plural='Found %count contacts'}Found %count contact{/ts}
  {/if}
  {* Search criteria are passed to tpl in the $qill array *}
  {if $qill}
      {include file="CRM/common/displaySearchCriteria.tpl"}
  {/if}
 </div>

 <div class="form-item">
   <div>
     {* Hide export and print buttons in 'Add Members to Group' context. *}
     {if $context NEQ 'amtg'}
        {if $action eq 512}
          {$form._qf_Advanced_next_print.html}&nbsp;&nbsp;
        {elseif $action eq 8192}
          {$form._qf_Builder_next_print.html}&nbsp;&nbsp;
        {else}
          {$form._qf_Search_next_print.html}&nbsp;&nbsp;
        {/if}
        {$form.task.html}
     {/if}
     {if $action eq 512}
       {$form._qf_Advanced_next_action.html}
     {elseif $action eq 8192}
       {$form._qf_Builder_next_action.html}&nbsp;&nbsp;
     {else}
       {$form._qf_Search_next_action.html}
     {/if}
     <br/>
     <label>{$form.radio_ts.ts_sel.html} {ts}selected records only{/ts}</label>&nbsp; <label>{$form.radio_ts.ts_all.html} {ts count=$pager->_totalItems plural='all %count records'}the found record{/ts}</label>
   </div>
 </div>  

