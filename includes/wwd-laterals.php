<?php
/**
 * @Author WWD
 * @Copyright (c) 2018. Westlands Water District. (https://wwd.ca.gov)
 * This code is released under the GPL licence version 3 or later, available here
 * http://www.gnu.org/licenses/gpl.txt
 *
 */

class wwdLateralList
{
    private $auth;
    private $isAuth = false;

    /**
     * wwdLateralList constructor.
     */
    public function __construct()
    {
        add_shortcode('wwd-lat-list', array($this, 'execute'));
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

    private function fmt($x)
    {
        //
        // Add P to end of $x if appropriate, and prepend 000's
        //
        $firstChar = substr($x, 0, 1);
        $str = $x;

        if ($firstChar == 'P') {
            $lastChar = substr($x, count($x) - 1, 1);
            if ($lastChar <> 'P') {
                $str = $str . 'P';
            }
        }

        $str = '000000' . $str;
        $str = substr($str, count($str) - 6, 5);
        return $str;
    }

    private function cmp($a, $b)
    {
        $a0 = $this->fmt($a['LatName']);
        $b0 = $this->fmt($b['LatName']);

        return strcmp($a0, $b0);
    }

    //
    // Format data in $rows to be displayed
    // in table.
    //
    private function formatTable($rows)
    {
        $result = '<div id="wwd_table" class="container">';
        $result .= '<div class="row large"><div class="col">Lateral</div></div>';

        $oddrow = new wwd_oddrow('oddrow');
        foreach ($rows as $row) {
            $link = '/lateral?id=' . $row["id"];
            $onclick = 'onclick="wwd_gotoLink(\'' . $link . '\');"';
            $class = 'class="row large ' . $oddrow->getClass() . '"';

            $result .= '<div ' . $class . '><div class="col" ' . $onclick . '>'
                . $row["LatName"]
                . '</div></div>';
        }

        $result .= '</div>';
        return $result;
    }

    private function render()
    {

        if ($this->isAuth) {
            $method = '/wp-lat/';

            $response = new wwd_data($method, 'GET', null);

            if ( $response->Err() ) {
                $message = $response->ErrorMessage();
            } else {
                $data = json_decode($response -> get_data() , true);
                $rows = $data["value"];

                usort($rows, array('wwdLateralList', 'cmp'));

                $message = $this->formatTable($rows);
            }

            $Result = $message;
        } else {
            $authMessage = new wwd_auth_msg();
            $Result = '<hr>' . $authMessage->notAuthorized() . '<hr>';
        }
        return $Result;
    }
}

$wwd_laterals = new wwdLateralList();
