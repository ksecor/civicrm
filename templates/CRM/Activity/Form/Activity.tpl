{* this template is used for caseactivity  *}

   <dl>     
    <dt>{$form.from_contact.label}</dt>
    {if $from_contact_val}
    <script type="text/javascript">
    dojo.addOnLoad( function( ) {ldelim}
    dojo.widget.byId( 'from_contact' ).setAllValues( '{$from_contact_val}', '{$from_contact_val}' )
    {rdelim} );
    </script>
    {/if}
    <dd>{$form.from_contact.html}</dd>
    <dt>{$form.to_contact.label}</dt><dd>{$form.to_contact.html}</dd>
    <dt>{$form.regarding_contact.label}</dt>
    {if $regard_contact_val}
    <script type="text/javascript">
    dojo.addOnLoad( function( ) {ldelim}
    dojo.widget.byId( 'regarding_contact' ).setAllValues( '{$regard_contact_val}', '{$regard_contact_val}' )
    {rdelim} );
    </script>
    {/if}  
    <dd>{$form.regarding_contact.html}</dd>
	<dt>{$form.case_subject.label}</dt><dd>{$form.case_subject.html}</dd>
    <dt>{$form.activitytag1_id.label}</dt><dd>{$form.activitytag1_id.html}</dd>
	<dt>{$form.activitytag2_id.label}</dt><dd>{$form.activitytag2_id.html}</dd>
	<dt>{$form.activitytag3_id.label}</dt><dd>{$form.activitytag3_id.html}</dd>
   </dl>