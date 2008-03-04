<?php

function smarty_block_ts($params, $text, &$smarty)
{
    static $config = null;
    static $locale = null;
    static $i18n   = null;

    if (!$config) {
        $config =& CRM_Core_Config::singleton();
    }

    if ($locale != $config->lcMessages) {
        $locale = $config->lcMessages;
        $i18n = new CRM_Core_I18n();
    }

    return $i18n->crm_translate($text, $params);
}


