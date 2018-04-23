<?php
/**
 * Created by PhpStorm.
 * User: jcarter
 * Date: 4/23/18
 * Time: 10:55 AM
 */


//
// Add Query Variables we need in our plugin.
//
function wwd_queryvars( $vars ) {
    array_push($vars, "id","page");
    return $vars;
}

add_filter( 'query_vars', 'wwd_queryvars' );
