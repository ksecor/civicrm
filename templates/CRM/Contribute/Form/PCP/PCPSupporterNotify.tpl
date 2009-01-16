{* Template for sending notfication email to supporter when they initially create a Personal Campaign Page. *}
{ts}Dear supporter{/ts},
{ts}Thanks for creating a personal campaign page in support of {/ts}{$contribPageTitle}

{* Approved message *}
{if $pcpStatus eq 'Approved'}
====================
{ts}Promoting Your Page{/ts}
====================
{ts}You can begin your fundraising efforts using our "Tell a Friend" form{/ts}:

1. {ts}Login to your account at{/ts}:
{$config->userFrameworkBaseURL}

{if $isTellFriendEnabled}
2. {ts}Click or paste this link into your browser and follow the prompts{/ts}:
{$pcpTellFriendURL}
{/if}

{ts}OR you can just copy this link to your page and email it to folks{/ts}:
{$pcpInfoURL}

===================
{ts}Managing Your Page{/ts}
===================
{ts}When you view your campaign page WHILE LOGGED IN, the page includes all the links you need to edit your page, tell friends, and update your contact info.{/ts}

{* Waiting Review message *}
{elseif $pcpStatus EQ 'Waiting Review'}
{ts}Your page requires administrator review before you can begin your fundraising efforts.{/ts}

{ts}A notification email has been sent to the site administrator, and you will receive another notification from them as soon as the review process is complete.{/ts}

{ts}You can still preview your page prior to approval{/ts}:
1. {ts}Login to your account at{/ts}:
{$config->userFrameworkBaseURL}

2. {ts}Click or paste this link into your browser{/ts}:
{$pcpInfoURL}

{/if}
{if $pcpNotifyEmailAddress}
{ts}Questions? Send email to{/ts}:
{$pcpNotifyEmailAddress}
{/if}