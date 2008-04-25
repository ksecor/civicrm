<fieldset>
<legend>Mapper</legend>

<div class="form-item">
   <dl>
{foreach from=$items key=count item=value}
<dt>{$count}:{$value}</dt><dd>
<span name="{$value}[{$count}]" dojoType="civicrm.HierSelect" url1="{crmURL p='civicrm/ajax/mapper/select' q='index=1'}" url2="{crmURL p='civicrm/ajax/mapper/select' q='index=2'}" firstInList=true jsMethod1="showHideNextSelector"></span><span class="tundra" id="id_map_2"><span id="id_{$value}_1"></span></span></dd>
{/foreach}
   </dl>    
</div>
    
<div id="crm-submit-buttons" class="form-item">
<dl>
   <dt>&nbsp;</dt><dd>{$form.buttons.html}</dd>
</dl>
</div>

</fieldset>

{literal}
<script type="text/javascript">
      function showHideNextSelector( ) {
            data_type = document.getElementsByName("mapper[0]")[0].value;
            if (data_type) {
                document.getElementById("id_map_2").style.display = "inline";
            } else {
                document.getElementById("id_map_2").style.display = "none";
            }
      }
      document.getElementById("id_map_2").style.display = "none";
</script>
{/literal}
