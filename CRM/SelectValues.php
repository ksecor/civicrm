<?php
class CRM_SelectValues {
    

    static $prefixName = array(
                          ' '    => '-title-',
                               'Mrs.' => 'Mrs.',
                               'Ms.'  => 'Ms.',
                               'Mr.'  => 'Mr.',
                               'Dr'   => 'Dr.',
                               'none' => '(none)',
                               );
    
    static $suffixName = array(
                               ' '    => '-suffix-',
                               'Jr.'  => 'Jr.',
                               'Sr.'  => 'Sr.', 
                               '||'   =>'||',
                               'none' => '(none)',
                               );
    
    static $greeting = array(
                          'Formal'    => 'default - Dear [first] [last]',
                                 'Informal'  => 'Dear [first]', 
                                 'Honorific' => 'Dear [title] [last]',
                                 'Custom'    => 'Customized',
                                 );

    static $date = array(
                      'language'  => 'en',
                              'format'    => 'dMY',
                              'minYear'   => 1900,
                              'maxYear'   => 2001,
                              );  
    
    
    static $context = array(
                         1 => 'Home', 
                                    'Work', 
                                    'Main',
                                    'Other'
                                    );
    
    static $im = array( 
                               1 => 'Yahoo', 
                               'MSN', 
                               'AIM', 
                               'Jabber',
                               'Indiatimes'
                               );
    
    static $phone = array(
                       'Phone' => 'Phone', 
                                  'Mobile' => 'Mobile', 
                                  'Fax' => 'Fax', 
                                  'Pager' => 'Pager'
                                  );
    
    
    static $state = array( 
                       1004 => 'California', 
                                  1036 => 'Oregon', 
                                  1046 => 'Washington'
                                  );
    
    
    static $country  = array( 
                                    1039 => 'Canada', 
                                    1101 => 'India', 
                                    1172 => 'Poland', 
                                    1128 => 'United States'
                                    );
        
    static $pcm = array(
                        ' '      => '-no preference-',
                        'Phone'  => 'by phone', 
                        'Email'  => 'by email', 
                        'Post' => 'by postal email',
                        );  
    
    static $contactid = array(
                              'Individual' => 'Individual',
                              'Organization' => 'Organization',
                              'Household' => 'Household'
                              );
     



}
?>