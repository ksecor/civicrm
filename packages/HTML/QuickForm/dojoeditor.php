<?php

require_once('HTML/QuickForm/textarea.php');

/**
 * HTML Quickform element for dojoeditor
 *
 * @author       Jan Wagner <wagner@netsols.de>
 * @access       public
 */
class HTML_QuickForm_dojoeditor extends HTML_QuickForm_textarea
{
  
    
    /**
     * Class constructor
     *
     * @param   string  dojoeditor instance name
     * @param   string  dojoeditor instance label
     * @param   array   Config settings for dojoeditor
     * @param   string  Attributes for the textarea
     * @access  public
     * @return  void
     */
    function HTML_QuickForm_dojoeditor($elementName=null, $elementLabel=null, $options=array(), $attributes=null)
      {
	HTML_QuickForm_element::HTML_QuickForm_element($elementName, $elementLabel, $attributes);
	$this->_persistantFreeze = true;
	$this->_type = 'dojoeditor';
      }
    
    /**
     * Sets the input field name
     * 
     * @param     string    $name   Input field name attribute
     * @since     1.0
     * @access    public
     * @return    void
     */
    function setName($name)
    {
        $this->updateAttributes(array('name'=>$name));
    } //end func setName
    
    // }}}
    // {{{ getName()

    /**
     * Returns the element name
     * 
     * @since     1.0
     * @access    public
     * @return    string
     */
    function getName()
    {
        return $this->getAttribute('name');
    } //end func getName

    // }}}
    // {{{ setValue()

    /**
     * Sets value for textarea element
     * 
     * @param     string    $value  Value for textarea element
     * @since     1.0
     * @access    public
     * @return    void
     */
    function setValue($value)
    {
        $this->_value = $value;
    } //end func setValue
    
    // }}}
    // {{{ getValue()

    /**
     * Returns the value of the form element
     *
     * @since     1.0
     * @access    public
     * @return    string
     */
    function getValue()
    {
        return $this->_value;
    } // end func getValue

    // }}}
    // {{{ setWrap()

    /**
     * Sets wrap type for textarea element
     * 
     * @param     string    $wrap  Wrap type
     * @since     1.0
     * @access    public
     * @return    void
     */
    function setWrap($wrap)
    {
        $this->updateAttributes(array('wrap' => $wrap));
    } //end func setWrap
    
    // }}}
    // {{{ setRows()

    /**
     * Sets height in rows for textarea element
     * 
     * @param     string    $rows  Height expressed in rows
     * @since     1.0
     * @access    public
     * @return    void
     */
    function setRows($rows)
    {
        $this->updateAttributes(array('rows' => $rows));
    } //end func setRows

    // }}}
    // {{{ setCols()

    /**
     * Sets width in cols for textarea element
     * 
     * @param     string    $cols  Width expressed in cols
     * @since     1.0
     * @access    public
     * @return    void
     */ 
    function setCols($cols)
    {
        $this->updateAttributes(array('cols' => $cols));
    } //end func setCols

    // }}}
    // {{{ toHtml()

    /**
     * Return the htmlarea in HTML
     *
     * @access public
     * @return string
     */
    function toHtml()
    {   
	$this->_attributes = array_merge($this->_attributes,
					 array( 'dojoType'             => 'dijit.Editor',
						'height'               => '250 px',
						'class'                => 'tundra',
						'extraPlugins'         => '["createLink","foreColor","hiliteColor","formatBlock"]'
						)  
					 );
	
	if ($this->_flagFrozen) {
	  return $this->getFrozenHtml();
        } else {
	  return $this->_getTabs() .
	    '<textarea' . $this->_getAttrString($this->_attributes) . '>' .
	    // because we wrap the form later we don't want the text indented
	    preg_replace("/(\r\n|\n|\r)/", '&#010;', htmlspecialchars($this->_value)) .
	    '</textarea>';
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