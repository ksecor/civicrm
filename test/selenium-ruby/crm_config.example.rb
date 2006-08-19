class CRMConfig

  def initialize
    @config = {
        # which browser should Selenium use
        # this is a nice default for Debian/Ubuntu
        'browser'      => '*firefox /usr/lib/firefox/firefox-bin',

        # what's the root of the Drupal's framework
        'uf_root'      => 'http://drupal/',

        # where's the login URL
        'login_url'    => '/',

        # Drupal login details
        'user'         => 'username',
        'pass'         => 'password',

        # what should be the domain of login emails
        'email_domain' => 'localhost',
    }
  end

  # allow accessing config settings via CRMConfig#setting
  def method_missing method
    @config[method.to_s]
  end

end
