<?php

class wwdMeters {
    private $auth;
    private $isAuth = false;
    private $meterSlug = '';
    private $search = '';

    public function __construct()
    {
        add_shortcode('wwd-meters', array( $this,'execute'));
    }

    public function execute()
    {
        $this->auth = new wwd_auth();
        $this->isAuth = $this->auth->isIsAuthenticated();
        $this->meterSlug = get_option('wwd-page-meter');

        if ( array_key_exists('meter_search', $_POST)) {
            $this->search = $_POST['searchterm'];
        }

        return $this->render();
    }

    private function curlOpts($method, $PostOrGet, $FormData ) {
        $output = null;

        if ( $this->isAuth ) {
            // Load options, and present to users.
            $api = new wwd_api_info();
            $apikey = $api->getApikey();
            $apiurl = $api->getApiurl();

            $curl = curl_init();

            if ( $FormData == null ) {
                $data = [];
            } else {
                $data = $FormData;
            }

            curl_setopt_array($curl, array(
                CURLOPT_URL => $apiurl . $method,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_POSTFIELDS => $data,
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

    private function myFilter($r, $term) {
        $upTerm = strtoupper($term);
        $sep = '/';
        $output = array();
        foreach( $r as $row ) {
            $x = strtoupper($row['meter'].$sep.$row['geo'].$sep.$row['latname'] );
            if ( strpos($x,$upTerm) !== false ) {
                // found it.
                array_push($output, $row);
            }
        }
        return $output;
    }

    private function getMeterList() {
        $output = '';
        $method = '/wp-sp-meters/';
        $formData = [];

//        $curl = $this->curlOpts($method, 'POST', $formData);

        $curl = new wwd_db($method, 'POST', $formData);
        $response = $curl->exec();
        $err = $curl->error();
        $curl->close();

//
//        if ( $curl !== null ) {
//            $response = curl_exec($curl);
//            $err = curl_error($curl);
//            curl_close($curl);

            if ($err) {
                $output = $err;
            }
            else {
                $json = json_decode($response, true);

                $data = $json['value'];

                $output = '<div class="container">';
                $output .= '<div class="row large">'
                    . '<div class="col-3">Meter ID</div>'
                    . '<div class="col-6">Geo</div>'
                    . '<div class="col-3">Lateral</div>'
                    . '</div>';


                if ( $this->search > '' ) {
                    $data = $this->myFilter($data, $this->search);
                }

                $oddrow = new wwd_oddrow('oddrow');
                foreach ($data as $row) {
                    $class = $oddrow->getClass();
                    $link = '<a href="/' . $this->meterSlug . '/?id=' . $row['meter'] . '">';
                    $output .= $link
                        . '<div class="row '. $class . ' large">'
                        .'<div class="col-3">'. $row['meter'] .'</div>'
                        .'<div class="col-6">'. $row['geo'] .'</div>'
                        .'<div class="col-3">'. $row['latname'] .'</div>'
                        .'</div></a>';

                }
                $output .= '</div>'; // container
            }
//        }
        return $output;
    }

    public function render() {
        $output = '';

        if ( $this->isAuth ) {
            $output .= $this->generateSpinner();
            $output .= $this->generateSearchForm($this->search);
            $output .= $this->getMeterList();
            $Result = $output;
        } else {
            $authMessage = new wwd_auth_msg();

            $Result = '<hr>' . $authMessage->notAuthorized() . '<hr>';
        }
        return $Result;
    }

    private function generateSearchForm($term) {
        $output = '';
        $output .= '<form method="post" action=""><span>';
        $output .= '<input type="text" value="' . $term . '" name="searchterm">';

        $output .= '<input type="submit" name="meter_search" class="button button-primary" value="Search" >';

        $output .= '</span></form>';
        return $output;
    }

    private function generateSpinner() {
        $output = '<div class="spinner"></div>';
        return $output;
    }
}

$wwd_meters = new wwdMeters();
