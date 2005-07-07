{$license}

{$database.comments}

/*******************************************************
*
* DROP TABLES IN REVERSE ORDER OF CREATION
*
*******************************************************/
{foreach from=$dropOrder item=name}
DROP TABLE IF EXISTS {$name};
{/foreach}

/*******************************************************
*
* CREATE TABLES
*
*******************************************************/

{foreach from=$tables item=table}
/*******************************************************
*
* {$table.name}
{if $table.comment}
*
* {$table.comment}
{/if}
*
*******************************************************/
CREATE TABLE {$table.name} (
{assign var='first' value=true}

{foreach from=$table.fields item=field}
{if ! $first},{/if}
{assign var='first' value=false}

     {$field.name} {$field.sqlType} {if $field.required}NOT NULL{/if} {if $field.autoincrement}AUTO_INCREMENT{/if} {if $field.default|count_characters}DEFAULT {$field.default}{/if} {if $field.comment}COMMENT '{$field.comment}'{/if}
{/foreach} {* table.fields *}

{if $table.primaryKey}
{if ! $first},{/if}
{assign var='first' value=false}

    PRIMARY KEY ( {$table.primaryKey.name} )
{/if} {* table.primaryKey *}

{if $table.index}
  {foreach from=$table.index item=index}
  {if ! $first},{/if}
  {assign var='first' value=false}
  {if $index.unique} UNIQUE{/if} INDEX {$index.name}(
  {assign var='firstIndexField' value=true}
  {foreach from=$index.field item=fieldName}
    {if $firstIndexField}{assign var='firstIndexField' value=false}{else}, {/if}{$fieldName}
  {/foreach}
)
{/foreach} {* table.index *}
{/if} {* table.index *}

{if $table.foreignKey}
{foreach from=$table.foreignKey item=foreign}
{if ! $first},{/if}
{assign var='first' value=false}

     FOREIGN KEY ({$foreign.name}) REFERENCES {$foreign.table}({$foreign.key})
{/foreach} {* table.foreignKey *}
{/if} {* table.foreignKey *}

) {$table.attributes};


{/foreach} {* tables *}