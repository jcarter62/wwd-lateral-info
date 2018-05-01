<?php

class wwdAccounts {
    private $auth;
    private $isAuth = false;
    private $pagesize;
    private $page;
    private $search = '';
    private $accountSlug = '';

    public function __construct()
    {
        add_shortcode('wwd-accounts', array( $this,'execute'));
    }

    public function execute()
    {
        $this->auth = new wwd_auth();
        $this->isAuth = $this->auth->isIsAuthenticated();
        $this->pagesize = get_option('wwd-pagesize',5);
        $this->page = get_query_var('page', '0');
        $this->accountSlug = get_option('wwd-page-account');

        if ( array_key_exists('accounts_search', $_POST ) ) {
            $this ->search = $_POST['searchterm'];
        }

        return $this->render($this->page);
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
        $result = '<div class="container">'
            . '<div class="row large">'
            . '<div class="col">Account</div>'
            . '<div class="col">Account Name</div>'
            . '</div>';

        $oddrow = new wwd_oddrow('oddrow');
        foreach ($rows as $row) {
            $link = '/' . $this->accountSlug . '/?id=' . $row["id"];
            $onclick = 'onclick="location.href=\'' . $link . '\'";';
            $result .= '<div class="row large '.$oddrow->getClass().'" ' . $onclick . '>'
                . '<div class="col-3">' . $row["id"] . '</div>'
                . '<div class="col-8">' . $row["FullName"] . '</div>'
                . '</div>';
        }

        $result .= '</div>';

        return $result;
    }

    private function myFilter($r, $term) {
        $upterm = strtoupper($term);
        $output = array();
        foreach( $r as $row ) {
            $x = strtoupper( $row['id'] . $row['FullName'] );
            if ( strpos($x,$upterm) !== false ) {
                // found it.
                array_push($output, $row);
            }
        }
        return $output;
    }

    public function render($pg) {
        $output = '';

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

                if ( $this->search > '' ) {
                    $rows = $this->myFilter($rows, $this->search);
                }

                usort($rows, array('wwdAccounts','cmp'));

                $searchForm = $this->generateSearchForm($this->search);

                if ( count($searchForm) > 0 ) {
                    $output .= $searchForm;
                }
                $output .= $this->formatTable($rows, $pg);
            }

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

        $output .= '<input type="submit" name="accounts_search" class="button button-primary" value="Search">';

        $output .= '</span></form>';
        return $output;
    }
}

$wwd_accounts = new wwdAccounts();
