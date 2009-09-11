-- CRM-5030

    UPDATE civicrm_navigation
        SET url = 'civicrm/activity/add?atype=3&action=add&reset=1&context=standalone'
        WHERE civicrm_navigation.name= 'New Email';
    UPDATE civicrm_navigation
        SET url = 'civicrm/contribute/add&reset=1&action=add&context=standalone'
        WHERE civicrm_navigation.name= 'New Contribution';
    UPDATE civicrm_navigation
        SET url = 'civicrm/pledge/add&reset=1&action=add&context=standalone'
        WHERE civicrm_navigation.name= 'New Pledge';
    UPDATE civicrm_navigation
        SET url = 'civicrm/participant/add&reset=1&action=add&context=standalone'
        WHERE civicrm_navigation.name= 'Register Event Participant';
    UPDATE civicrm_navigation
        SET url = 'civicrm/member/add&reset=1&action=add&context=standalone'
        WHERE civicrm_navigation.name= 'New Membership';
    UPDATE civicrm_navigation
        SET url = 'civicrm/case/add&reset=1&action=add&atype=13&context=standalone'
        WHERE civicrm_navigation.name= 'New Case';
    UPDATE civicrm_navigation
        SET url = 'civicrm/grant/add&reset=1&action=add&context=standalone'
        WHERE civicrm_navigation.name= 'New Grant';