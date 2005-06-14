      <div class="form-item">
	     <span class="horizontal-position"> 
              {foreach from=$AToZBar item=row }
	       {if $row}   
               <a href= "{crmURL p='civicrm/contact/search' q="&_qf_Search_display=true&force=1&sortByCharacter=`$row`"}" >{$row}</a>&nbsp;|&nbsp; 
               {/if}
              {/foreach}
	      { if $AToZBar} 
              <a href= "{crmURL p='civicrm/contact/search' q="&_qf_Search_display=true&force=1&sortByCharacter="}" >All </a>
              {/if}
            </span>
     </div>
