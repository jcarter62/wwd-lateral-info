<?php
/**
 * @Author WWD
 * @Copyright (c) 2018. Westlands Water District. (https://wwd.ca.gov)
 * This code is released under the GPL licence version 3 or later, available here
 * http://www.gnu.org/licenses/gpl.txt
 *
 */

class wwd_data
{
    private $response;
    private $error;
    private $data;

    /**
     * wwd_data constructor.
     * @param $method
     * @param $PostOrGet
     * @param $data
     */
    public function __construct($method, $PostOrGet, $data)
    {
        $auth = new wwd_auth();
        $result = null;
        $this->response = null;
        $this->error = null;

        if ( $auth->isIsAuthenticated() ) {
            // set
            $api = new wwd_api_info();
            $fullUrl = esc_html( $api->getApiurl() . $method );

            $header = array(
                "x-cdata-authtoken" => $api->getApikey()
            );
            $args = array(
                'method'=>$PostOrGet,
                'headers'=>$header,
                'body'=>$data
            );
            // Reference class-http.php -> method request.
            // @return array|WP_Error Array containing
            // 'headers', 'body', 'response', 'cookies', 'filename'.
            //         A WP_Error instance upon error.
            $result = wp_remote_request($fullUrl, $args);

            $this->response = $result['response'];

            if ( $this->responseOK($this->response) ) {
                $httpResp = $result['http_response'];
                $this->data = $httpResp->get_data();
                $this->error = null;
            } else {
                $this->data = null;
                $this->error = $this->response;
            }
        }
    }

    private function responseOK($resp) {
        if ( $resp['code'] >= 200 and $resp['code'] < 400 ) {
            return true;
        } else {
            return false;
        }
    }

    public function Err() {
        return $this->error;
    }

    public function ErrorMessage() {
        return $this->error['message'];
    }

    public function get_data() {
        return $this->data;
    }
}