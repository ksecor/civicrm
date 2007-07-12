class CRMConfig

  def initialize
    @config = {
      # which browser should Selenium use
      # a nice default for Debian/Ubuntu is
      # '*firefox /usr/lib/firefox/firefox-bin'
      'browser'      => '*firefox /path/to/firefox-bin',
      
      # what's the root of the Drupal's framework
      'uf_root'      => 'http://localhost/drupal/',
      
      # where's the login URL
      'login_url'    => '/',
      
      # Drupal login details
      'user'         => 'username',
      'pass'         => 'password',
      
      # Domain Name
      'domain_name'  => 'Domain Name 1',
      
      # what should be the domain of login emails
      'email_domain' => 'FIXME.ORG',
      
    }
  end
  
  # allow accessing config settings via CRMConfig#setting
  def method_missing method
    @config[method.to_s]
  end
  
end
