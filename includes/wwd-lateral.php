<?php
/**
 * @Author WWD
 * @Copyright (c) 2018. Westlands Water District. (https://wwd.ca.gov)
 * This code is released under the GPL licence version 3 or later, available here
 * http://www.gnu.org/licenses/gpl.txt
 *
 */

/**
 * Class wwdLateralInfo
 */
class wwdLateralInfo
{
    private $auth;
    private $isAuth = false;
    private $id = '';
    private $accountSlug = '';
    private $meterSlug = '';
    private $colums = Array();

    /**
     * wwdLateralInfo constructor.
     */
    public function __construct()
    {
        add_shortcode('wwd-lateral', array($this, 'execute'));
        $this->colums = Array(
            "Lateral"=>1,
            "Meter"=>2,
            "Geo"=>3,
            "Account"=>2,
            "Name"=>4
        );
    }

    /**
     * Execute shortcode method, and return results.
     * This shortcode generates a list of meters associated
     * with the lateral id.
     */
    public function execute()
    {
        $this->auth = new wwd_auth();
        $this->isAuth = $this->auth->isIsAuthenticated();

        $this->accountSlug = get_option('wwd-page-account');
        $this->meterSlug = get_option('wwd-page-meter');

        $this->id = get_query_var('id', '');
        $info = $this->render();

        return $info;
    }

    private function getCol($col) {
        $out = 'col-';
        $out .= $this->colums[$col];
        return $out;
    }

    private function wwd_header()
    {
        $result = '<div class="row medium">'
            . '<div class="'.$this->getCol("Lateral").'">Lateral</div>'
            . '<div class="'.$this->getCol("Meter").'">Meter</div>'
            . '<div class="'.$this->getCol("Geo").'">Geo</div>'
            . '<div class="'.$this->getCol("Account").'">Account</div>'
            . '<div class="'.$this->getCol("Name").'">Name</div>'
            . '</div>';
        return $result;
    }

    private function wwd_cell($data, $slug, $column)
    {
        if ($slug > '') {
            $link = $slug . '/?id=' . $data;
            $output = '<div class="'.$this->getCol($column).'" '
                . 'onclick="wwd_gotoLink(\'' . $link . '\')">'
                . '<strong>';
        } else {
            $output = '<div class="'.$this->getCol($column).'">';
        }
        $output .= $data;
        if ($slug > '') {
            $output .= '</strong>';
        }
        $output .= '</div>';
        return $output;
    }

    //
    // Format data in $rows to be displayed
    // in table.
    //
    private function formatTable($rows)
    {
        $result = '<div id="wwd_table" class="container">';

        $result .= $this->wwd_header();
        $oddrow = new wwd_oddrow('oddrow');
        foreach ($rows as $row) {
            $result .= '<div class="row medium ' . $oddrow->getClass() . '">'
                . $this->wwd_cell($row['lateral'], '', 'Lateral')
                . $this->wwd_cell($row['meter'], $this->meterSlug, 'Meter')
                . $this->wwd_cell($row['geo'], '', 'Geo')
                . $this->wwd_cell($row['account'], $this->accountSlug, 'Account')
                . $this->wwd_cell($row['fullname'], '', 'Name')
                . '</div>';
        }

        $result .= '</div>';
        return $result;
    }

    private function render()
    {
        if ($this->isAuth) {
            $method = '/wp-meterbylat/';
            $data = ['lateral' => $this->id];

            $curl = new wwd_db($method, 'POST', $data);
            $response = $curl->exec();
            $err = $curl->error();
            $curl->close();

            if ($err) {
                $message = $err;
            } else {
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
