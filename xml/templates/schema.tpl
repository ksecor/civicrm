{$license}

{$database.comments}

DROP DATABASE IF EXISTS {$database.name};

CREATE DATABASE {$database.name} {$database.attributes};
use {$database.name};

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
DROP TABLE IF EXISTS {$table.name};
CREATE TABLE {$table.name} (
{assign var='first' value=true}

{foreach from=$table.fields item=field}
{if ! $first},{/if}
{assign var='first' value=false}

     {$field.name} {$field.sqlType} {if $field.required}NOT NULL{/if} {if $field.autoincrement}AUTO_INCREMENT{/if} {if $field.default}DEFAULT {$field.default}{/if} {if $field.comment}COMMENT '{$field.comment}'{/if}
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

{if $table.UNIQUE} UNIQUE{/if} INDEX {$index.name}({$index.fieldName})
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