{* this template is used for adding/editing ACL  *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New ACL{/ts}{elseif $action eq 2}{ts}Edit ACL{/ts}{else}{ts}Delete ACL{/ts}{/if}</legend>

{if $action eq 8}
  <div class="messages status">
    <dl>
      <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
      <dd>    
        {ts}WARNING: Deleting this ACL will remove this permission from all ACL Roles.{/ts} {ts}Do you want to continue?{/ts}
      </dd>
    </dl>
  </div>
{else}
  <dl>
    <dt>{$form.operation.label}</dt><dd>{$form.operation.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}What type of operation (action) is being permitted?{/ts}</dd>
    <dt>{$form.object_type.label}</dt><dd>{$form.object_type.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Select the type of data this ACL operates on. Warning: you need to know that "access all custom groups" and "profile listings and forms" override and disable specific ACL settings for custom fields groups and profiles respectively. You need to disable them in order to get an access control effect with above ACL settings!{/ts}</dd>
  </dl>
  <div id="id-group-acl">
    <dl>
    <dt>{$form.group_id.label}</dt><dd>{$form.group_id.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Select a specific group of contacts, OR apply this permission to ALL groups.{/ts}</dd>
    </dl>
  </div>
  <div id="id-profile-acl">
    <dl>
    <dt>{$form.uf_group_id.label}</dt><dd>{$form.uf_group_id.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Select a specific profile, OR apply this permission to ALL profiles.{/ts}</dd>
    </dl>
  </div>
  <div id="id-custom-acl">
    <dl>
    <dt>{$form.custom_group_id.label}</dt><dd>{$form.custom_group_id.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Select a specific group of custom fields, OR apply this permission to ALL custom fields.{/ts}</dd>
    </dl>
  </div>
  <dl>
    <dt>{$form.entity_id.label}</dt><dd>{$form.entity_id.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Select a Role to assign (grant) this permission to.{/ts}</dd>
    <dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
    <dt>{$form.is_active.label}</dt><dd>{$form.is_active.html}</dd>
  </dl>
{/if}
  <dl> 
    <dt></dt><dd>{$form.buttons.html}</dd>
  </dl> 
</fieldset>
</div>

{include file="CRM/common/showHide.tpl"}
{literal}
<script type="text/javascript">
 function showObjectSelect( ) {
    var ot = document.getElementsByName('object_type');
    for (var i = 0; i < ot.length; i++) {
        if ( ot[i].checked ) {
            switch(ot[i].value) {
                case "1":
                    show('id-group-acl');
                    hide('id-profile-acl');
                    hide('id-custom-acl');
                    break;
                case "2":
                    show('id-profile-acl');
                    hide('id-group-acl');
                    hide('id-custom-acl');
                    break;
                case "3":
                    show('id-custom-acl');
                    hide('id-group-acl');
                    hide('id-profile-acl');
                    break;
            }
        }
    }
 return;
}
</script>
{/literal}