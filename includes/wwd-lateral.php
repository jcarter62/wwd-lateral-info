<?php

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

    /**
     * wwdLateralInfo constructor.
     */
    public function __construct()
    {
        add_shortcode('wwd-lateral', array($this, 'execute'));
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

    private function wwd_header()
    {
        $result = '<div class="row medium">'
            . '<div class="col">Lateral</div>'
            . '<div class="col">Meter</div>'
            . '<div class="col">Geo</div>'
            . '<div class="col">Account</div>'
            . '<div class="col">Name</div>'
            . '</div>';
        return $result;
    }

    private function wwd_cell($data, $slug)
    {
        if ($slug > '') {
            $link = $slug . '/?id=' . $data;
            $output = '<div class="col" '
                . 'onclick="wwd_gotoLink(\'' . $link . '\')">'
                . '<strong>';
        } else {
            $output = '<div class="col">';
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
