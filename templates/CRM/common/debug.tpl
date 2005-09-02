{if $smarty.get.smartyDebug}
{debug}
{/if}

{if $smarty.get.sessionReset}
{$session->reset()}
{/if}

{if $smarty.get.sessionDebug}
{$session->debug($smarty.get.sessionDebug)}
{/if}

{if $smarty.get.directoryCleanup} 
{$config->cleanup($smarty.get.directoryCleanup)}
{/if}
