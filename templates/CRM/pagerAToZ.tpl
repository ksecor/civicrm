      <div class="form-item">
	     <span class="horizontal-position"> 
              {foreach from=$AToZBar item=row }
               <a href= "{crmURL p='civicrm/contact/search' q="&_qf_Search_display=true&force=1&sortByCharacter=`$row`"}" >{$row}</a>&nbsp;|&nbsp; 
              {/foreach}
              <a href= "{crmURL p='civicrm/contact/search' q="&_qf_Search_display=true&force=1&sortByCharacter="}" >All </a>
            </span>
     </div>
