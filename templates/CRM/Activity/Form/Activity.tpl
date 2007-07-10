{* this template is used for caseactivity  *}

   <dl>     
    <dt>{$form.from_contact.label}</dt>
    
    {if $from_contact_value}
     
        <script type="text/javascript">
        dojo.addOnLoad( function( ) {ldelim}
        dojo.widget.byId( 'from_contact' ).setAllValues( '{$from_contact_value}', '{$from_contact_value}' )
        {rdelim} );
        </script>
   
    {/if}
    <dd>{if $action eq 4} {$from_contact_value} {else}{$form.from_contact.html}{/if}</dd>
    <dt>{$form.to_contact.label}</dt>
    {if $to_contact_value}
    <script type="text/javascript">
    dojo.addOnLoad( function( ) {ldelim}
    dojo.widget.byId( 'to_contact' ).setAllValues( '{$to_contact_value}', '{$to_contact_value}' )
    {rdelim} );
    </script>
    {/if}
    <dd>{if $action eq 4}{$to_contact_value}{else}{$form.to_contact.html}{/if}</dd>
    <dt>{$form.regarding_contact.label}</dt>
    {if $regard_contact_value}
    <script type="text/javascript">
    dojo.addOnLoad( function( ) {ldelim}
    dojo.widget.byId( 'regarding_contact' ).setAllValues( '{$regard_contact_value}', '{$regard_contact_value}' )
    {rdelim} );
    </script>
    {/if}  
    <dd>{if $action eq 4}{$regard_contact_value}{else}{$form.regarding_contact.html}{/if}</dd>
	<dt>{$form.case_subject.label}</dt>
    {if $subject_value}
    <script type="text/javascript">
    dojo.addOnLoad( function( ) {ldelim}
    dojo.widget.byId( 'case_subject' ).setAllValues( '{$subject_value}', '{$subject_value}' )
    {rdelim} );
    </script>
    {/if}  
    <dd>{if $action eq 4}{$subject_value}{else}{$form.case_subject.html}{/if}</dd>
   
    <dt>{$form.activity_tag1_id.label}</dt><dd>{$form.activity_tag1_id.html}</dd>
	<dt>{$form.activity_tag2_id.label}</dt><dd>{$form.activity_tag2_id.html}</dd>
	<dt>{$form.activity_tag3_id.label}</dt><dd>{$form.activity_tag3_id.html}</dd>
   </dl>