<?php

abstract class samclass
{
    static $num;
    var $a;
    
    const a=10;
   
    function samfun() extends CRM_Core_IMP
    {
        
        $object1 = new CRM_Core_BAO_Activity();
        CRM_Core_Page::syz();
        CRM_Core_Form::syz();
        CRM_Core_Form::bbb();
        CRM_Core_Contact::aaa();
        parent::bb();
        self::zzz();
        
        parent::__construct();
        self::$num =100;

        foreach($this->tokens as &$a)
            {
            }
        
    } 
   

}
