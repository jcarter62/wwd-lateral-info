<?php

class wwdLateralList {
    private $auth;
    private $isAuth = false;

    /**
     * wwdLateralList constructor.
     */
    public function __construct()
    {
        add_shortcode('wwd-lat-list', array( $this,'execute') );
    }


    /**
     * execute: Execute shortcode for this class.
     */
    public function execute()
    {
        $this->auth = new wwd_auth();
        $this->isAuth = $this->auth->isIsAuthenticated();
        return $this->render();
    }

    private function fmt($x) {
        //
        // Add P to end of $x if appropriate, and prepend 000's
        //
        $firstChar = substr($x,0,1);
        $str = $x;

        if ( $firstChar == 'P' ) {
            $lastChar = substr($x, count($x) - 1, 1 );
            if ( $lastChar <> 'P' ) {
                $str = $str . 'P';
            }
        }

        $str = '000000' . $str ;
        $str = substr($str, count($str) - 6, 5);
        return $str;
    }

    private function cmp($a,$b) {
        $a0 = $this->fmt($a['LatName']);
        $b0 = $this->fmt($b['LatName']);

        return strcmp( $a0, $b0 );
    }

    //
    // Format data in $rows to be displayed
    // in table.
    //
    private function formatTable($rows) {
        $rownum = 0;
        $result = '<table class="zebra" border="0"><tr><th>Lateral</th></tr>';

        foreach( $rows as $row ) {
            $rownum  += 1;
            $link = '/lateral?id=' . $row["id"];

            $onclick = 'onclick="location.href=\'' . $link . '\'";';
            $result .= '<tr ><td '. $onclick . '>'
                . $row["LatName"]
                . '</td></tr>';
        }

        $result .= '</table>';
        return $result;
    }

    private function render() {

        if ( $this->isAuth ) {

            // Load options, and present to users.
            $api = new wwd_api_info();
            $apikey = $api->getApikey();
            $apiurl = $api->getApiurl();
            $method = '/wp-lat/';

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $apiurl . $method,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "Cache-Control: no-cache",
                    "x-cdata-authtoken: " . $apikey
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

                usort($rows, array('wwdLateralList','cmp'));

                $message = $this->formatTable($rows);
            }

            $Result = $message;
        } else {
            $Result =
                '<hr>'
                . 'You are not authorized to view this content. <br>'
                . '<a href="/wp-login.php">Please Login.</a>'
                . '<hr>';
        }
        return $Result;
    }
}

$wwd_laterals = new wwdLateralList();
