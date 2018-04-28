<?php

class wwdAccount {
    private $auth;
    private $isAuth = false;
    private $account = 0;

    public function __construct()
    {
        add_shortcode('wwd-account', array( $this,'execute'));
    }

    public function execute()
    {
        $this->auth = new wwd_auth();
        $this->isAuth = $this->auth->isIsAuthenticated();
        $this->account = get_query_var('id', '0');

        return $this->render();
    }

    // Create address string, including 3 lines and comma where needed.
    private function fmtAddress($rec) {
        $a2 = trim($rec['Address2']);
        $a3 = trim($rec['Address3']);

        $result = $rec['Address1'];
        if ( strlen($a2) > 0 ) {
            $result .= ', ' . $a2;
        }
        if ( strlen($a3) > 0 ) {
            $result .= ', ' . $a3;
        }
        return $result;
    }

    private function getAccountAddress() {
        $output = '';
        $method = '/wmis-account(' . $this->account . ')';
        $curl = $this->curlOpts($method, 'GET');
        if ( $curl !== null ) {
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                $output = $err;
            }
            else {
                $data = json_decode($response, true);
                $output = '<div class="container">';

                $output .= '<div class="row">'
                    . '<div class="col">Account</div>'
                    . '<div class="col"><strong>' . $data['NAME_ID'] . '</strong></div>'
                    . '</div>';

                $output .= '<div class="row">'
                    . '<div class="col">Name</div>'
                    . '<div class="col"><strong>' . $data['FullName'] . '</strong></div>'
                    . '</div>';

                $output .= '<div class="row">'
                    . '<div class="col">Address</div>'
                    . '<div class="col"><strong>'
                    . $this->fmtAddress($data)
                    . '</strong></div>'
                    . '</div>';

                $output .= '<div class="row">'
                    . '<div class="col">City State Zip</div>'
                    . '<div class="col"><strong>'
                    . $data['City'] . ' '
                    . $data['State'] . ' '
                    . $data['Zip'] . ' '
                    . '</strong></div>'
                    . '</div>';

                $output .= '<hr>';

                $output .= '</div>'; // container
            }
        }
        return $output;
    }

    private function curlOpts($method, $PostOrGet) {
        $output = null;

        if ( $this->isAuth ) {
            // Load options, and present to users.
            $api = new wwd_api_info();
            $apikey = $api->getApikey();
            $apiurl = $api->getApiurl();

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $apiurl . $method,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $PostOrGet,
                CURLOPT_HTTPHEADER => array(
                    "Cache-Control: no-cache",
                    "x-cdata-authtoken: " . $apikey
                ),
                CURLOPT_SSL_VERIFYPEER => false,
            ));
            $output = $curl;

        }
        return $output;
    }

    public function render() {
        $output = '';

        if ( $this->isAuth ) {
            $output .= $this->getAccountAddress();

            $Result = $output;
        } else {
            $authMessage = new wwd_auth_msg();

            $Result = '<hr>' . $authMessage->notAuthorized() . '<hr>';
        }
        return $Result;
    }
}

$wwd_account = new wwdAccount();
