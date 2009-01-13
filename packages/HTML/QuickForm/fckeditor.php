<?php

require_once('HTML/QuickForm/textarea.php');

/**
 * HTML Quickform element for FCKeditor
 *
 * FCKeditor is a WYSIWYG HTML editor which can be obtained from
 * http://www.fckeditor.com. I tried to resemble the integration instructions
 * as much as possible, so the examples from the docs should work with this one.
 * 
 * @author       Jan Wagner <wagner@netsols.de>
 * @access       public
 */
class HTML_QuickForm_FCKeditor extends HTML_QuickForm_textarea
{
    /**
     * The width of the editor in pixels or percent
     *
     * @var string
     * @access public
     */
    var $Width = '95%';
    
    /**
     * The height of the editor in pixels or percent
     *
     * @var string
     * @access public
     */
    var $Height = '400';
    
    /**
     * The Toolbar set to use
     *
     * @var string
     * @access public
     */
    var $ToolbarSet = 'Default';
    
    /**
     * The path where to find the editor
     *
     * @var string
     * @access public
     */
    var $BasePath = 'packages/fckeditor/';
    
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
     * @param   string  FCKeditor instance name
     * @param   string  FCKeditor instance label
     * @param   array   Config settings for FCKeditor
     * @param   string  Attributes for the textarea
     * @access  public
     * @return  void
     */
    function HTML_QuickForm_fckeditor($elementName=null, $elementLabel=null, $attributes=null, $options=array())
    {
        HTML_QuickForm_element::HTML_QuickForm_element($elementName, $elementLabel, $attributes);
        $this->_persistantFreeze = true;
        $this->_type = 'FCKeditor';
        
        if (is_array($options)) {
            $this->Config = $options;
        }
    }
    
    /**
     * Set config variable for FCKeditor
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
     * Make a string of the configuration to pass along in a hidden field
     * 
     * @access private
     * @return string
     */
    function _getConfigFieldString()
    {
        $value = '';
        $first = true;
        foreach ($this->Config as $k => $v) {
            if (!$first) {
                $value .= '&amp;';
            } else {
                $first = false;
            }
            $value .= $this->_encodeValue($k) . '=';
            if ($v === true) {
                $value .= 'true';
            } elseif ($v === false) {
                $value .= 'false';
            } else {
                $value .= $this->_encodeValue($v);
            }
        }
        return $value;
    }
    
    /**
     * Encode the given string so it can be used inside a value attribute
     * 
     * @access private
     * @param string
     * @return string
     */
    function _encodeValue($value)
    {
        $chars = array('&' => '%26D', '=' => '%3D', '"' => '%22');
        return (strtr($value, $chars));
    }

    /**
     * Return the htmlarea in HTML
     *
     * @access public
     * @return string
     */
    function toHtml()
    {
        if ($this->_flagFrozen) {
            return $this->getFrozenHtml();
        } elseif (!$this->IsCompatible()) {
            return parent::toHtml();
        } else {
            $name = $this->getAttribute('name');
            $this->updateAttributes(array('id' => $name));
            $cname = $name . '___Config';
            $html = '';
            if (!defined('HTML_QUICKFORM_FCKEDITOR_LOADED')) {                
                // load FCKeditor
                $config = CRM_Core_Config::singleton( );
                $html  = sprintf(
                    '<script type="text/javascript" src="%s"></script>',
                    $config->resourceBase . $this->BasePath . 'fckeditor.js'
                );                
                define('HTML_QUICKFORM_FCKEDITOR_LOADED', true);
            }

            $config =& CRM_Core_Config::singleton();

            // make link for iframe src
            $link = $config->resourceBase . $this->BasePath . 'editor/fckeditor.html?InstanceName=' . $name;

            if (strlen($this->ToolbarSet)) {
                $link .= '&amp;Toolbar=' . $this->ToolbarSet;
            }

            // render the linked hidden field
            $html .= sprintf('<input type="hidden" id="%s" name="%s" value="%s" />',
                             $name, $name, htmlspecialchars($this->getValue()));
            // render the config hidden field
            $html .= sprintf('<input type="hidden" id="%s" name="%s" value="%s" />',
                             $cname, $cname, $this->_getConfigFieldString());
             // render the editor iframe
            $html .= sprintf(
                '<iframe id="%s" src="%s" width="%s" height="%s" framborder="no" scrolling="no"></iframe>',
                $name . '___Frame', $link, $this->Width, $this->Height
            );
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
