<?php
/**
 * @Author WWD
 * @Copyright (c) 2018. Westlands Water District. (https://wwd.ca.gov)
 * This code is released under the GPL licence version 3 or later, available here
 * http://www.gnu.org/licenses/gpl.txt
 *
 */

class wwd_qvars
{

    public function __construct()
    {
        add_filter('query_vars', array($this, 'execute'));
    }

    //
    // Add Query Variables we need in our plugin.
    //
    public function execute($vars)
    {
        array_push($vars, "id", "page");
        return $vars;
    }
}

$wwd_qvars = new wwd_qvars();
