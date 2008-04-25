<fieldset>
<legend>Mapper</legend>

<div class="form-item">
   <dl>
{foreach from=$items key=count item=value}
<dt>{$count}:{$value}</dt><dd>
<span name="{$value}[{$count}]" dojoType="civicrm.HierSelect" url1="{crmURL p='civicrm/ajax/mapper/select' q='index=1'}" url2="{crmURL p='civicrm/ajax/mapper/select' q='index=2'}" firstInList=true freezeAll={$freezeAll}></span><span class="tundra" id="id_{$value}_{$count}_1"></span><span id="id_{$value}_{$count}_2"></span><span></span></dd>
{/foreach}
   </dl>    
</div>
    
<div id="crm-submit-buttons" class="form-item">
<dl>
   <dt>&nbsp;</dt><dd>{$form.buttons.html}</dd>
</dl>
</div>

</fieldset>
