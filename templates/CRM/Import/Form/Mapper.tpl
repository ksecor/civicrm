<fieldset>
<legend>Mapper</legend>

<div class="form-item">
   <dl>
{foreach from=$items key=count item=value}
<dt>{$count}:{$value}</dt><dd>
<span name="{$value}[{$count}]" dojoType="civicrm.HierSelect" url1="{crmURL p='civicrm/ajax/mapper/select' q='index=1'}" url2="{crmURL p='civicrm/ajax/mapper/select' q='index=2'}" firstInList=true jsMethod1="showHideNextSelector(this.name,e)"></span><span class="tundra" id="id_map_{$value}[{$count}]_1"><span id="id_{$value}[{$count}]_1"></span></span></dd>

{literal}
<script type="text/javascript">
    var selId = "id_map_" + {/literal}"{$value}[{$count}]"{literal} + "_1";
    document.getElementById(selId).style.display = "none";
</script>
{/literal}

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
      function showHideNextSelector( sel1Name, sel1Val ) {
            var sel1Id = "id_map_" + sel1Name + "_1";
            if (sel1Val) {
                document.getElementById(sel1Id).style.display = "inline";
            } else {
                document.getElementById(sel1Id).style.display = "none";
            }
      }
</script>
{/literal}
