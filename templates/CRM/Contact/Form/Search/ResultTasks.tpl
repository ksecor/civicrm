{* Form elements for displaying and running action tasks on search results *}

 <div id="search-status">
  {if $savedSearch.name}{$savedSearch.name} {ts}(saved search){/ts} - {/if}
    {if $context EQ 'smog'}
      {ts count=$pager->_totalItems plural='Found %count group members'}Found %count group member{/ts}
   {else}
      {ts count=$pager->_totalItems plural='Found %count contacts'}Found %count contact{/ts}
  {/if}
  {if $qill}
    <ul>
    {foreach from=$qill item=criteria}
      <li>{$criteria}</li>
    {/foreach}
    </ul>
  {/if}
  {if $savedSearch.name}
    <div class="element-right">
        <a href="">&raquo; Start a new search</a>
    <div>
 </div>

 <div class="form-item">
   <div>
     {* Hide export and print buttons in 'Add Members to Group' context. *}
     {if $context NEQ 'amtg'}
        {if $action eq 512}
          {$form._qf_Advanced_next_print.html} &nbsp; {$form._qf_Advanced_refresh_export.html} &nbsp; &nbsp; &nbsp;
        {else}
          {$form._qf_Search_next_print.html} &nbsp; {$form._qf_Search_refresh_export.html} &nbsp; &nbsp; &nbsp;
        {/if}
        {$form.task.html}
     {/if}
     {if $action eq 512}
       {$form._qf_Advanced_next_action.html}
     {else}
       {$form._qf_Search_next_action.html}
     {/if}
     <br />
     {$form.radio_ts.ts_sel.html} <b>{ts}selected records only{/ts}</b>&nbsp; {$form.radio_ts.ts_all.html} <b>{ts count=$pager->_totalItems plural='all %count records'}the found record{/ts}</b>
   </div>
   <div class="float-right">{ts}Select:{/ts} 
    <a onclick="changeCheckboxVals('mark_x_','select'  , {$form.formName} ); return false;" name="select_all"  href="#">{ts}All{/ts}</a> |
    <a onclick="changeCheckboxVals('mark_x_','deselect', {$form.formName} ); return false;" name="select_none" href="#">{ts}None{/ts}</a>
  </div>
 </div>  
 <p>
