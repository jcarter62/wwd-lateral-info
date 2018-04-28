<?php
/**
 * Created by PhpStorm.
 * User: jcarter
 * Date: 4/26/18
 * Time: 9:22 AM
 */

class wwd_auth_msg
{
    public function notAuthorized() {
        $Result = 'You are not authorized to view this content. '
            . '<a href="/wp-login.php">Please Login.</a>';
        return Result;
    }
}
