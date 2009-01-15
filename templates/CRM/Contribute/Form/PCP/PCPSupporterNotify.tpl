{* Template for sending notfication email to supporter when they initially create a Personal Campaign Page. *}
Dear supporter,
Thanks for creating a personal campaign page in support of {$contribPageTitle}

{* Approved message *}
{if $pcpStatus eq 'Approved'}
====================
Promoting Your Page
====================
You can begin your fundraising efforts using our "Tell a Friend" form:

1. Login to your account at:
{$config->userFrameworkBaseURL}

{if $isTellFriendEnabled}
2. Click or paste this link into your browser and follow the prompts:
{$pcpTellFriendURL}
{/if}

OR you can just copy this link to your page and email it to folks:
{$pcpInfoURL}

===================
Managing Your Page
===================
When you view your campaign page WHILE LOGGED IN, the page includes all the links you need to edit your page, tell friends, and update your contact info.

{* Waiting Review message *}
{elseif $pcpStatus EQ 'Waiting Review'}
Your page requires administrator review before you can begin your fundraising efforts.

A notification email has been sent to the site administrator, and you will receive another notification from them as soon as the review process is complete.

You can still preview your page prior to approval:
1. Login to your account at:
{$config->userFrameworkBaseURL}

2. Click or paste this link into your browser:
{$pcpInfoURL}

{/if}
Questions? Send email to:
{$pcpNotifyEmailAddress}
