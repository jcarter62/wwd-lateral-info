<?php
/**
 * @Author WWD
 * @Copyright (c) 2018. Westlands Water District. (https://wwd.ca.gov)
 * This code is released under the GPL licence version 3 or later, available here
 * http://www.gnu.org/licenses/gpl.txt
 *
 */

class wwdMeters
{
    private $auth;
    private $isAuth = false;
    private $meterSlug = '';
    private $search = '';

    public function __construct()
    {
        add_shortcode('wwd-meters', array($this, 'execute'));
    }

    public function execute()
    {
        $set = new wwd_settings();
        $this->auth = new wwd_auth();
        $this->isAuth = $this->auth->isIsAuthenticated();
        $this->meterSlug = $set->get('wwd-page-meter');

        if (array_key_exists('meter_search', $_POST)) {
            $this->search = $_POST['searchterm'];
        }

        return $this->render();
    }

    private function myFilter($r, $term)
    {
        $upTerm = strtoupper($term);
        $sep = '/';
        $output = array();
        foreach ($r as $row) {
            $x = strtoupper($row['meter'] . $sep . $row['geo'] . $sep . $row['latname']);
            if (strpos($x, $upTerm) !== false) {
                // found it.
                array_push($output, $row);
            }
        }
        return $output;
    }

    private function getMeterList()
    {
        $method = '/wp-sp-meters/';
        $formData = [];

        $response = new wwd_data($method, 'POST', $formData);

        if ( $response->Err()) {
            $output = $response->ErrorMessage();
        } else {
            $json = json_decode($response ->get_data(), true);
            $data = $json['value'];

            $output = '<div id="wwd_table" class="container">';
            $output .= '<div class="row large">'
                . '<div class="col-3">Meter ID</div>'
                . '<div class="col-6">Geo</div>'
                . '<div class="col-3">Lateral</div>'
                . '</div>';


            if ($this->search > '') {
                $data = $this->myFilter($data, $this->search);
            }

            $oddrow = new wwd_oddrow('oddrow');
            foreach ($data as $row) {
                $class = $oddrow->getClass();

                $link = '/' . $this->meterSlug . '/?id=' . $row["meter"];
                $onclick = 'onclick="wwd_gotoLink(\'' . $link . '\');"';

                $output .= '<div class="row ' . $class . ' large" '. $onclick . '>'
                    . '<div class="col-3">' . $row['meter'] . '</div>'
                    . '<div class="col-6">' . $row['geo'] . '</div>'
                    . '<div class="col-3">' . $row['latname'] . '</div>'
                    . '</div></a>';
            }
            $output .= '</div>'; // container
        }
//        }
        return $output;
    }

    public function render()
    {
        $output = '';

        if ($this->isAuth) {
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

    private function generateSearchForm($term)
    {
        $output = '';
        $output .= '<form method="post" action=""><span>';
        $output .= '<input type="text" value="' . $term . '" name="searchterm">';

        $output .= '<input type="submit" 
                name="meter_search" 
                class="button button-primary" 
                value="Search" onclick="wwd_dimTable();"  >';

        $output .= '</span></form>';
        return $output;
    }

    private function generateSpinner()
    {
        $output = '<div class="spinner"></div>';
        return $output;
    }
}

$wwd_meters = new wwdMeters();
