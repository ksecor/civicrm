{* Form elements for displaying and running action tasks on search results *}

 <div id="search-status">
  Found {$pager->_totalItems} contacts.
  {if $qill}{$qill}{/if}
 </div>

 <div class="form-item">
   <span>
     {* Hide export and print buttons in 'Add Members to Group' context. *}
     {if $context NEQ 'amtg'}
        {if $mode eq 64}
          {$form._qf_Advanced_next_print.html} &nbsp; {$form._qf_Advanced_refresh_export.html} &nbsp; &nbsp; &nbsp;
        {else}
          {$form._qf_Search_next_print.html} &nbsp; {$form._qf_Search_refresh_export.html} &nbsp; &nbsp; &nbsp;
        {/if}
        {$form.task.html}
     {/if}
     {if $mode eq 64}
       {$form._qf_Advanced_next_action.html}
     {else}
       {$form._qf_Search_next_action.html}
     {/if}
     <br />
     {$form.radio_ts.ts_sel.html} &nbsp; {$form.radio_ts.ts_all.html} {$pager->_totalItems} records
   </span>
   <span class="element-right">Select: 
<a onclick="changeCheckboxVals('mark_x_','select'  , Search ); return false;" name="select_all"  href="#">All</a> |
<a onclick="changeCheckboxVals('mark_x_','deselect', Search ); return false;" name="select_none" href="#">None</a></span>
 </div>  

