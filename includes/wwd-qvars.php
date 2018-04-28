<?php
/**
 * Created by PhpStorm.
 * User: jcarter
 * Date: 4/23/18
 * Time: 10:55 AM
 */

class wwd_qvars {

    public function __construct()
    {
        add_filter('query_vars', array($this, 'execute'));
    }

    //
    // Add Query Variables we need in our plugin.
    //
    public function execute($vars) {
        array_push($vars, "id","page");
        return $vars;
    }
}

$wwd_qvars = new wwd_qvars();
