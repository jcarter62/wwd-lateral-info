<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 4/21/18
 * Time: 9:28 PM
 */

class wwd_api_info {
    private $apikey = '';
    private $apiurl = '';

    /**
     * @return mixed|string|void
     */
    public function getApikey() {
        return $this->apikey;
    }

    /**
     * @return mixed|string|void
     */
    public function getApiurl() {
        return $this->apiurl;
    }

    /**
     * wwd_api_info constructor.
     */
    public function __construct() {
        $this->apikey = get_option('wwd-apikey', '');
        $this->apiurl = get_option('wwd-apiurl', '');
    }

}