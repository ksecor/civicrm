{* this template is used for adding/editing Premium Information *} 
 <div id="id-premium" class="section-shown">
     
      <fieldset>
           <dl>
           <dt class="label">{$form.product_name.label}</dt><dd>{$form.product_name.html}</dd>
           </dl>

           <div id="premium_contri">
            <dl>
            <dt class="label">{$form.min_amount.label}</dt><dd>{$form.min_amount.html|crmReplace:class:texttolabel|crmMoney:$currency}</dd>
            </dl>
            <div class="spacer"></div>
           </div>

           <dl>
           <dt class="label">{$form.fulfilled_date.label}</dt><dd>{$form.fulfilled_date.html}
           {include file="CRM/common/calendar/desc.tpl" trigger=trigger_contribution_5}
           {include file="CRM/common/calendar/body.tpl" dateVar=fulfilled_date startDate=currentYear endDate=endYear offset=10 trigger=trigger_contribution_5}      
           </dd>
           </dl>

      </fieldset>
      
</div>

      {literal}
        <script type="text/javascript">
            var min_amount = document.getElementById("min_amount");
            min_amount.readOnly = 1;
    	    function showMinContrib( ) {
               var product = document.getElementsByName("product_name[0]")[0];
               var product_id = product.options[product.selectedIndex].value;
               var min_amount = document.getElementById("min_amount");
 	 
	       
               var amount = new Array();
               amount[0] = '';  
	
               if( product_id > 0 ) {  
		  show('premium_contri');	      	
               } else {
	          hide('premium_contri');	      
             }
	
      {/literal}
		
      var index = 1;
      {foreach from= $mincontribution item=amt key=id}
            {literal}amount[index]{/literal} = "{$amt}"
            {literal}index = index + 1{/literal}
      {/foreach}
      {literal}
          if(amount[product_id]) {  
              min_amount.value = amount[product_id];
          } else {
              min_amount.value = "";
          }           
     }  
     </script> 
     {/literal}
{if $action eq 1 or $action eq 2 or $action eq null }
    <script type="text/javascript">
       showMinContrib( );
    </script>            
{/if}
{if $action ne 2 or $showOption eq true}
    {$initHideBoxes}
{/if}
