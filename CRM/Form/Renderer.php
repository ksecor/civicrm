<?php

require_once 'HTML/QuickForm/Renderer/ArraySmarty.php';

class CRM_Form_Renderer extends HTML_QuickForm_Renderer_ArraySmarty {

  function _elementToArray(&$element, $required, $error) {
    $el = parent::_elementToArray($element, $required, $error);

    // add label html
    $el['label_orig'] = $element->getLabel();
    if ( isset($el['label']) and $el['label'] ) {
      $el['label_html'] = "<label for=\"$el[name]\">$el[label]</label>";
      $el['html_labelled'] = $el['label_html'] . $el['html'];
    }

    $el['required'] = $required ? theme('mark') : null;
    $el['theme']    = theme( 'form_element', $element->getLabel(), $el['html'], null, $element->getName(), $req, $el['error'] );
                       
    return $el;
  }
  
} // end CRM_Form_Renderer
