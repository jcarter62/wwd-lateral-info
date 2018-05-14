<?php
/**
 * @Author WWD
 * @Copyright (c) 2018. Westlands Water District. (https://wwd.ca.gov)
 * This code is released under the GPL licence version 3 or later, available here
 * http://www.gnu.org/licenses/gpl.txt
 *
 */

class wwd_auth_msg
{
    /**
     * Present message for user, why they are not authorized.
     */
    public function notAuthorized()
    {
        $set = new wwd_settings();
        $thisUserId = get_current_user_id();
        if ($thisUserId <= 0) {
            $UserMessage = 'You are not logged in.';
        } else {
            $UserSettingsData = get_userdata(get_current_user_id());
            $thisUserRole = $UserSettingsData->roles[0];
            $UserMessage = 'Your role is: ' . $thisUserRole;
        }

        $role = $set->get('wwd-role', 'undefined');
        $result = 'You are not authorized to view ths content. <br>'
            . 'Users need to be logged in, as well as be a member of the group '
            . '"' . $role . '".<br> '
            . $UserMessage . '<br>'
            . '<a href="/wp-login.php">Please login</a>, and check your role settings.';
        return $result;
    }
}
