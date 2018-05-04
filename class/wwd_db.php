<?php

class wwd_db
{
    private $auth;
    private $method;
    private $PostOrGet;
    private $FormData;
    public $curl;

    /**
     * wwd_db constructor.
     */
    public function __construct($method, $PostOrGet, $FormData)
    {
        $this->auth = new wwd_auth();
        $this->method = $method;
        $this->PostOrGet = $PostOrGet;
        $this->FormData = $FormData;

        $this->curlOpts();

        return $this->curl;
    }

    private function curlOpts()
    {
        $this->curl = null;

        if ($this->auth->isIsAuthenticated()) {
            // Load options, and present to users.
            $api = new wwd_api_info();
            $apikey = $api->getApikey();
            $apiurl = $api->getApiurl();

            $this->curl = curl_init();

            if ($this->FormData == null) {
                $data = [];
            } else {
                $data = $this->FormData;
            }

            curl_setopt_array($this->curl, array(
                CURLOPT_URL => $apiurl . $this->method,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_CUSTOMREQUEST => $this->PostOrGet,
                CURLOPT_HTTPHEADER => array(
                    "Cache-Control: no-cache",
                    "x-cdata-authtoken: " . $apikey
                ),
                CURLOPT_SSL_VERIFYPEER => false,
            ));
        }
    }

    public function exec()
    {
        return curl_exec($this->curl);
    }

    public function error()
    {
        return curl_error($this->curl);
    }

    public function close()
    {
        curl_close($this->curl);
    }
}