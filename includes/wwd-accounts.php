<?php

class wwdAccounts {
    private $auth;
    private $isAuth = false;
    private $pagesize;
    private $page;

    public function __construct()
    {
        $this->auth = new wwd_auth();
        $this->isAuth = $this->auth->isIsAuthenticated();
        $this->pagesize = get_option('wwd-pagesize',5);
    }

    /**
     * @param Account Number
     * @return String, with '000' prepended to allow for better sorting.
     */
    private function fmt($x) {
        $str = '0000000000' . $x ;
        $str = substr($str, count($str) - 10, 9);
        return $str;
    }

    private function cmp($a,$b) {
        $a0 = $this->fmt($a['id']);
        $b0 = $this->fmt($b['id']);
        return strcmp( $a0, $b0 );
    }


    //
    // Format data in $rows to be displayed
    // in table.
    //
    private function formatTable($rows, $pg) {
        $rownum = 0;
        $result = '<table class="zebra" border="0">'
            . '<tr><th>Account</th><th>Account Name</th></tr>';

        if ( $pg < 0 ) {
            $page = 1;
        } else {
            $page = $pg - 1;
        }

        $pageStart = $page * $this->pagesize;
        $pageEnd = ($page + 1) * $this->pagesize;

        foreach( $rows as $row ) {
            $rownum  += 1;

            if ( ( $pageStart <= $rownum ) && ( $rownum < $pageEnd ) ) {
                $link = '/account?id=' . $row["id"];

                $onclick = 'onclick="location.href=\'' . $link . '\'";';
                $result .= '<tr ' . $onclick . '>'
                    . '<td '. '>' . $row["id"] . '</td>'
                    . '<td '. '>' . $row["FullName"] . '</td>'
                    . '</tr>';
            }
        }

        $result .= '</table>';

        // add footer for page links.
        $prefix = '/accounts/?page=';
        $pages = round( $rownum / $this->pagesize, 0 );
        $foot = '<hr>';
//        for ( $i = 0; i< $pages; $i++ ) {
//            $link = '/accounts/?page=' . $i ;
//            $onclick = 'onclick="location.href=\'' . $link . '\'";';
//
//            $foot .= '<span ' . $onclick . '> ' . ($i+1) . ' </span>';
//        }

        $result = $result . $foot;

        return $result;
    }

    public function render($pg) {

        if ( $pg == 0 ) {
            $pg = 1;
        }

        if ( $this->isAuth ) {

            // Load options, and present to users.
            $api = new wwd_api_info();
            $apikey = $api->getApikey();
            $apiurl = $api->getApiurl();
            $method = '/wmis-accountlist/';

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

                usort($rows, array('wwdAccounts','cmp'));

                $message = $this->formatTable($rows, $pg);
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

function wwd_accounts()
{
    $page = get_query_var('page', '0');

    $list = new wwdAccounts();
    return $list->render($page);
}

add_shortcode('wwd-accounts', 'wwd_accounts');
