-- /*******************************************************
-- *
-- * Clean up the exisiting tables
-- *
-- *******************************************************/
{foreach from=$dropOrder item=name}
DROP TABLE IF EXISTS {$name};
{/foreach}

