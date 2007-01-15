<div class="form-item">  
<fieldset>
      <legend>{ts}View Participant{/ts}</legend>
      <dl>  
        <dt class="font-size12pt">{ts}From{/ts}</dt><dd class="font-size12pt"><strong>{$displayName}</strong>&nbsp;</dd>
        <dt>{ts}Event{/ts}</dt><dd>{$event}&nbsp;</dd>
        <dt>{ts}Participant Role{/ts}</dt><dd>{$role}&nbsp;</dd>
        <dt>{ts}Registration Date{/ts}</dt><dd>{$register_date|truncate:10:''|crmDate}&nbsp;</dd>
        <dt>{ts}Participant Status{/ts}</dt><dd>{$status}&nbsp;</dd>
        <dt>{ts}Event Source{/ts}</dt><dd>{$source}&nbsp;</dd>
        <dt>{ts}Event Level{/ts}</dt><dd>{$event_level}&nbsp;</dd>
        <dt>{ts}Note{/ts}</dt><dd>{$note}&nbsp;</dd>
        {include file="CRM/Contact/Page/View/InlineCustomData.tpl" mainEditForm=1}  
        <dl>
           <dt></dt><dd>{$form.buttons.html}</dd>
        </dl>
    </dl>

</fieldset>  
</div>