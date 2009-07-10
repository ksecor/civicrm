<h3 class="head"> 
    <span class="ui-icon ui-icon-triangle-1-e"></span><a href="#">{ts}{$title}{/ts}</a>
</h3>

<div id="demographics" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom">
  <div class="form-item">
        <span class="labels">{$form.gender_id.label}</span>
        
	<span class="fields">
        {$form.gender_id.html}
        &nbsp;&nbsp;(&nbsp;<a href="#" title="unselect" onclick="unselectRadio('gender_id', '{$form.formName}'); return false;">{ts}unselect{/ts}</a>&nbsp;)
        </span>
  </div>
  <div class="form-item">
        <span class="labels">{$form.birth_date.label}</span>
        <span class="fields">{$form.birth_date.html} &nbsp;

        {if $showCalender}          
        {include file="CRM/common/calendar/desc.tpl" trigger=trigger_demographics_1}
        {include file="CRM/common/calendar/body.tpl" dateVar=birth_date startDate=1905 endDate=currentYear trigger=trigger_demographics_1 }
        {/if}
        </span>
  </div>
  <div class="form-item">
       {$form.is_deceased.html}
       {$form.is_deceased.label}
  </div>
  <div id="showDeceasedDate" class="form-item">
       <span class="labels">
       {$form.deceased_date.label}
       </span>
       <span class="fields">
		{$form.deceased_date.html} &nbsp;
       {if $showCalender}
       {include file="CRM/common/calendar/desc.tpl" trigger=trigger_demographics_2}
       {include file="CRM/common/calendar/body.tpl" dateVar=deceased_date startDate=1905 endDate=currentYear trigger=trigger_demographics_2 }
       {/if}
       </span>
  </div> 
</div>

{literal}
<script type="text/javascript">
    showDeceasedDate( );    
    function showDeceasedDate( )
    {
        if (document.getElementsByName("is_deceased")[0].checked) {
      	    show('showDeceasedDate');
        } else {
	    hide('showDeceasedDate');
        }
    }     
</script>
{/literal}