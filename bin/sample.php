<?php

class samclass
{
    var $a;
    
    function samfun() extends CRM_Core_IMP
    {
        
        $object1 = new CRM_Core_BAO_Activity();


        CRM_Core_Page::syz();
        CRM_Core_Form::syz();
        CRM_Core_Form::bbb();
        CRM_Core_Contact::bbb();
        self::samfun();
    } 


}
