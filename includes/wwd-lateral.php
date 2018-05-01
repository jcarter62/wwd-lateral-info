<?php

/**
 * Class wwdLateralInfo
 */
class wwdLateralInfo {
    private $auth;
    private $isAuth = false;
    private $id = '';
    private $accountSlug = '';
    private $meterSlug = '';

    /**
     * wwdLateralInfo constructor.
     */
    public function __construct()
    {
        add_shortcode('wwd-lateral', array( $this, 'execute')) ;
    }

    /**
     * Execute shortcode method, and return results.
     * This shortcode generates a list of meters associated
     * with the lateral id.
     * @return List of meters
     */
    public function execute() {
        $this->auth = new wwd_auth();
        $this->isAuth = $this->auth->isIsAuthenticated();

        $this->accountSlug = get_option('wwd-page-account');
        $this->meterSlug = get_option('wwd-page-meter');

        $this->id = get_query_var('id', '');
        $info = $this->render();

        return $info;
    }

    private function wwd_header() {
        $result = '<div class="row medium">'
            .'<div class="col">Lateral</div>'
            .'<div class="col">Meter</div>'
            .'<div class="col">Geo</div>'
            .'<div class="col">Account</div>'
            .'<div class="col">Name</div>'
            .'</div>';
        return $result;
    }

    private function wwd_cell($data, $slug) {
        $output = '<div class="col">';
        if ( $slug > '' ) {
            $output .= '<a href="/' . $slug . '/?id=' . $data . '">';
        }
        $output .= $data;
        if ( $slug > '' ) {
            $output .= '</a>';
        }
        $output .= '</div>';
        return $output;
    }

    //
    // Format data in $rows to be displayed
    // in table.
    //
    private function formatTable($rows) {
        $result = '<div class="container">';

        $result .= $this->wwd_header();
        $oddrow = new wwd_oddrow('oddrow');
        foreach( $rows as $row ) {
            $result .= '<div class="row medium '. $oddrow->getClass() .'">'
                . $this->wwd_cell($row['lateral'], '')
                . $this->wwd_cell($row['meter'], $this->meterSlug)
                . $this->wwd_cell($row['geo'], '')
                . $this->wwd_cell($row['account'], $this->accountSlug)
                . $this->wwd_cell($row['fullname'], '')
                . '</div>';
        }

        $result .= '</div>';
        return $result;
    }

    /**
     * @return Table of meters for lateral id.
     */
    private function render() {
        if ( $this->isAuth ) {

            // Load options, and present to users.
            $api = new wwd_api_info();
            $apikey = $api->getApikey();
            $apiurl = $api->getApiurl();
            $method = '/wp-meterbylat/';


            $curl = curl_init();

            $data = ['lateral'=> $this->id];

            curl_setopt_array($curl, array(
                CURLOPT_URL => $apiurl . $method,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_HTTPHEADER => array(
                    "Cache-Control: no-cache",
                    "x-cdata-authtoken:" . $apikey
                ),
                CURLOPT_SSL_VERIFYPEER => false,
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                $message = $err;
            }
            else {
                $data = json_decode($response, true);
                $rows = $data["value"];
                $results = $this->formatTable($rows);

                $message = $results;
            }

            $info = $message;

        } else {
            $authMessage = new wwd_auth_msg();

            $info = '<hr>' . $authMessage->notAuthorized() . '<hr>';
        }

        return $info;
    }
}

$wwd_lateral = new wwdLateralInfo();
