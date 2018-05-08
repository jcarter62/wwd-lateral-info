<?php

class wwd_auth
{
    // This is the required role for users allowed to view WWD Information.
    private $PluginRole = '';
    private $AdminRole = '';

    // Interal flag used to determine if the user is "authenticated",
    // based on being a member of the role above.
    private $isAuthenticated = false;
    private $username = '';

    public function __construct()
    {
        $this->isAuthenticated = false;
        $this->username = '';
        $this->PluginRole = strtoupper(get_option('wwd-role','wwdview'));
        $this->AdminRole = strtoupper('administrator');
    }

    public function isIsAuthenticated()
    {
        $this->checkAuth();
        return $this->isAuthenticated;
    }

    /**
     * Determine if currently logged in user is considered "authenticated"
     */
    private function checkAuth()
    {
        $this->isAuthenticated = false;
        $isLoggedIn = is_user_logged_in();

        if ($isLoggedIn) {
            $this->username = wp_get_current_user()->user_login;
            $id = wp_get_current_user()->ID;
            $meta = get_user_meta($id, 'wp_capabilities', false);
            foreach ($meta[0] as $key => $value) {
                $k = strtoupper($key);
                if (($k == $this->PluginRole) or
                    ($k == $this->AdminRole)) {
                    $this->isAuthenticated = true;
                }
            }
        }
    }

    /**
     * return user name, obtained from the checkAuth() method.
     * If the user is not authenticated, then the user name should
     * be blank.
     */
    public function getUsername()
    {
        return $this->username;
    }
}