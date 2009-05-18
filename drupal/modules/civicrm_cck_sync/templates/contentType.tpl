<?php

$content['type'] =
      array( 
            'name'                     => {$cck.groupName},
            'type'                     => {$cck.groupCCKName},
            'description'              => {$cck.description},
            'title_label'              => 'Title',
            'body_label'               => 'Body',
            'min_word_count'           => '0',
            'help'                     => '',
            'node_options'             => array (
                                                 'status' => true,
                                                 'promote' => false,
                                                 'sticky' => false,
                                                 'revision' => false,
                                                 ),
            'upload'                   => '1',
            'old_type'                 => '',
            'orig_type'                => '',
            'module'                   => 'node',
            'custom'                   => '1',
            'modified'                 => '1',
            'locked'                   => '0',
            'comment'                  => '0',
            'comment_default_mode'     => '4',
            'comment_default_order'    => '1',
            'comment_default_per_page' => '50',
            'comment_controls'         => '3',
            'comment_anonymous'        => 0,
            'comment_subject_field'    => '1',
            'comment_preview'          => '1',
            'comment_form_location'    => '0',
             );

$content['fields']  = array (
{foreach from=$cck.fields item=field key=fieldNum}
{if $field.type == 'text'}
{include file="drupal/modules/civicrm_cck_mirror/text.tpl}
{else if $field.type == 'integer'}
{include file="drupal/modules/civicrm_cck_mirror/integer.tpl}
{/if}
{/foreach}
);
