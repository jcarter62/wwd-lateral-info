<?php
/**
 * @Author WWD
 * @Copyright (c) 2018. Westlands Water District. (https://wwd.ca.gov)
 * This code is released under the GPL licence version 3 or later, available here
 * http://www.gnu.org/licenses/gpl.txt
 *
 */

class wwdAccounts
{
    private $auth;
    private $isAuth = false;
    private $search = '';
    private $accountSlug = '';

    public function __construct()
    {
        add_shortcode('wwd-accounts', array($this, 'execute'));
    }

    public function execute()
    {
        $set = new wwd_settings();
        $this->auth = new wwd_auth();
        $this->isAuth = $this->auth->isIsAuthenticated();
        $this->accountSlug = $set->get('wwd-page-account');

        if (array_key_exists('accounts_search', $_POST)) {
            $this->search = $_POST['searchterm'];
        }

        return $this->render();
    }

    /**
     * @param Account Number
     * @return String, with '000' prepended to allow for better sorting.
     */
    private function fmt($x)
    {
        $str = '0000000000' . $x;
        $str = substr($str, count($str) - 10, 9);
        return $str;
    }

    private function cmp($a, $b)
    {
        $a0 = $this->fmt($a['id']);
        $b0 = $this->fmt($b['id']);
        return strcmp($a0, $b0);
    }

    //
    // Format data in $rows to be displayed
    // in table.
    //
    private function formatTable($rows, $pg)
    {
        $result = '<div id="wwd_table" class="container">'
            . '<div class="row large">'
            . '<div class="col-3">Account</div>'
            . '<div class="col-8">Account Name</div>'
            . '</div>';

        $oddrow = new wwd_oddrow('oddrow');
        foreach ($rows as $row) {
            $link = '/' . $this->accountSlug . '/?id=' . $row["id"];
            $onclick = 'onclick="wwd_gotoLink(\'' . $link . '\');"';
            $result .= '<div class="row large ' . $oddrow->getClass() . '" ' . $onclick . '>'
                . '<div class="col-3">' . $row["id"] . '</div>'
                . '<div class="col-8">' . $row["FullName"] . '</div>'
                . '</div>';
        }

        $result .= '</div>';

        return $result;
    }

    private function myFilter($r, $term)
    {
        $upterm = strtoupper($term);
        $output = array();
        foreach ($r as $row) {
            $x = strtoupper($row['id'] . $row['FullName']);
            if (strpos($x, $upterm) !== false) {
                // found it.
                array_push($output, $row);
            }
        }
        return $output;
    }

    public function render()
    {
        $output = '';

        if ($this->isAuth) {
            $method = '/wmis-accountlist/';

            $response = new wwd_data($method, 'GET', null);

            if ( $response->Err() ) {
                $output = $response->ErrorMessage();
            } else {
                $data = json_decode($response->get_data(), true);
                $rows = $data["value"];

                if ($this->search > '') {
                    $rows = $this->myFilter($rows, $this->search);
                }

                usort($rows, array('wwdAccounts', 'cmp'));

                $searchForm = $this->generateSearchForm($this->search);

                if (count($searchForm) > 0) {
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

    private function generateSearchForm($term)
    {
        $output = '';
        $output .= '<form method="post" action=""><span>';
        $output .= '<input type="text" value="' . $term . '" name="searchterm">';

        $output .= '<input type="submit" name="accounts_search" 
                class="button button-primary" 
                value="Search" onclick="wwd_dimTable();">';

        $output .= '</span></form>';
        return $output;
    }
}

$wwd_accounts = new wwdAccounts();
