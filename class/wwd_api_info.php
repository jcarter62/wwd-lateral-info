<?php
/**
 * @Author WWD
 * @Copyright (c) 2018. Westlands Water District. (https://wwd.ca.gov)
 * This code is released under the GPL licence version 3 or later, available here
 * http://www.gnu.org/licenses/gpl.txt
 *
 */

class wwd_api_info
{
    private $apikey = '';
    private $apiurl = '';

    /**
     * @return mixed|string|void
     */
    public function getApikey()
    {
        return $this->apikey;
    }

    /**
     * @return mixed|string|void
     */
    public function getApiurl()
    {
        return $this->apiurl;
    }

    /**
     * wwd_api_info constructor.
     */
    public function __construct()
    {
        $set = new wwd_settings();
        $this->apikey = $set->get('wwd-apikey', '');
        $this->apiurl = $set->get('wwd-apiurl', '');
    }

}