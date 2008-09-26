<?php

require_once('HTML/QuickForm/textarea.php');

/**
 * HTML Quickform element for TinyMCE Editor
 *
 * TinyMCE is a WYSIWYG HTML editor which can be obtained from
 * http://tinymce.moxiecode.com/.
 *
 * @access       public
 */
class HTML_QuickForm_TinyMCE extends HTML_QuickForm_textarea
{
    /**
     * The width of the editor in pixels or percent
     *
     * @var string
     * @access public
     */
    var $Width = '75%';
    
    /**
     * The height of the editor in pixels or percent
     *
     * @var string
     * @access public
     */
    var $Height = '200';
    
    /**
     * The path where to find the editor files
     *
     * @var string
     * @access public
     */
    var $BasePath = 'packages/tinymce/jscripts/tiny_mce/';
    
    /**
     * Check for browser compatibility
     *
     * @var boolean
     * @access public
     */
    var $CheckBrowser = true;

    /**
     * Configuration settings for the editor
     *
     * @var array
     * @access public
     */
    var $Config = array();
    
    /**
     * Class constructor
     *
     * @param   string  TinyMCE instance name
     * @param   string  TinyMCE instance label
     * @param   array   Config settings for TinyMCE
     * @param   string  Attributes for the textarea
     * @access  public
     * @return  void
     */
    function HTML_QuickForm_TinyMCE($elementName=null, $elementLabel=null, $attributes=null, $options=array())
    {
        HTML_QuickForm_element::HTML_QuickForm_element($elementName, $elementLabel, $attributes);
        $this->_persistantFreeze = true;
        $this->_type = 'TinyMCE';
        
        if (is_array($options)) {
            $this->Config = $options;
        }
    }
    
    /**
     * Set config variable for TinyMCE
     *
     * @param mixed Key of config setting
     * @param mixed Value of config settinh
     * @access public
     * @return void     
     */
    function SetConfig($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->Config[$k] = $v;
            }
        } else {
            $this->Config[$key] = $value;
        }
    }
    
    /**
     * Check if the browser is compatible (IE 5.5+, Gecko > 20030210)
     *
     * @access public
     * @return boolean
     */
    function IsCompatible()
    {
        if (isset($_SERVER['HTTP_USER_AGENT']))
        {
            $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
            if (($msie = strpos($agent, 'msie')) !== false &&
                strpos($agent, 'opera') === false &&
                strpos($agent, 'mac') === false)
            {                
                return ((float) substr($agent, $msie + 5, 3) >= 5.5);
            } elseif (($gecko = strpos($agent, 'gecko/')) !== false) {
                return ((int) substr($agent, $gecko + 6, 8 ) >= 20030210);
            }             
            return true;
        }   
        return false;
    }

    
    /**
     * Return the htmlarea in HTML
     *
     * @access public
     * @return string
     */
    function toHtml()
    {
        $html = null;
        // return frozen state
        if ($this->_flagFrozen) {
            return $this->getFrozenHtml();
        // return textarea if incompatible
        } elseif (!$this->IsCompatible()) {
            return parent::toHtml();
        // return textarea
        } else {
            //FIX for multiple editors in a form, initialize once (CRM-3559)
            if ( !defined('HTML_QUICKFORM_TINYMCEEDITOR_LOADED' ) ) {                
                // load tinyMCEeditor
                $config = CRM_Core_Config::singleton( );
                $html   = sprintf( '<script type="text/javascript" src="%s"></script>',
                                   $config->resourceBase . $this->BasePath . 'tiny_mce.js'
                                   );
                
                $html .= sprintf( '<script type="text/javascript">
tinyMCE.init({
mode : "textareas",
theme : "advanced",
editor_selector : "form-TinyMCE",
plugins : "safari,spellchecker,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,pagebreak,imagemanager,filemanager",
theme_advanced_buttons1_add : "fontselect,fontsizeselect",
theme_advanced_buttons2_add : "separator,insertdate,inserttime,preview,separator,forecolor,backcolor",
theme_advanced_buttons2_add_before: "cut,copy,paste,pastetext,pasteword,separator,search,replace,separator",
theme_advanced_buttons3_add_before : "tablecontrols,separator",
theme_advanced_buttons3_add : "emotions,iespell,media,advhr,separator,print,separator,ltr,rtl,separator,fullscreen",
theme_advanced_toolbar_location : "top",
theme_advanced_toolbar_align : "left",
theme_advanced_statusbar_location : "bottom",
theme_advanced_resize_horizontal : false,
theme_advanced_resizing : true,
apply_source_formatting : true,
spellchecker_languages : "+English=en,Danish=da,Dutch=nl,Finnish=fi,French=fr,German=de,Italian=it,Polish=pl,Portuguese=pt,Spanish=es,Swedish=sv"
});
</script>' );

                define('HTML_QUICKFORM_TINYMCEEDITOR_LOADED', true);
            }
                        
            // include textarea as well (TinyMCE transforms it)
            $html .=  parent::toHTML();
            return $html;
        }
    }
    
    /**
     * Returns the htmlarea content in HTML
     * 
     * @access public
     * @return string
     */
    function getFrozenHtml()
    {
        return $this->getValue();
    }
}

?>