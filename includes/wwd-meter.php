<?php
/**
 * @Author WWD
 * @Copyright (c) 2018. Westlands Water District. (https://wwd.ca.gov)
 * This code is released under the GPL licence version 3 or later, available here
 * http://www.gnu.org/licenses/gpl.txt
 *
 */

class wwdMeter
{
    private $auth;
    private $isAuth = false;
    private $meterid = 0;
    private $accountSlug = '';

    public function __construct()
    {
        add_shortcode('wwd-meter', array($this, 'execute'));
    }

    public function execute()
    {
        $this->auth = new wwd_auth();
        $this->isAuth = $this->auth->isIsAuthenticated();
        $this->meterid = get_query_var('id', '0');
        $this->accountSlug = get_option('wwd-page-account');

        return $this->render();
    }

    // Create address string, including 3 lines and comma where needed.
    private function fmtAddress($rec)
    {
        $a2 = trim($rec['Address2']);
        $a3 = trim($rec['Address3']);

        $result = $rec['Address1'];
        if (strlen($a2) > 0) {
            $result .= ', ' . $a2;
        }
        if (strlen($a3) > 0) {
            $result .= ', ' . $a3;
        }
        return $result;
    }

    private function meterDetailRow($name, $details, $class)
    {
        $rowClass = 'row large ' . $class;
        $output = '<div class="' . $rowClass . '">';
        $output .= '<div class="col-3">' . $name . '</div>';
        $output .= '<div class="col-8"><strong>' . $details . '</strong></div>';
        $output .= '</div>';
        return $output;
    }

    private function mapurl($lat, $lon)
    {
        $output = '<a href="https://www.google.com/maps?q='
            . $lat . ',' . $lon
            . '&z=22;t=h" '
            . 'target="_blank">'
            . 'Map'
            . '</a>';
        return $output;
    }

    private function getMeterDetails()
    {
        $output = '';
        $method = '/wp-v-wtr-asset(' . $this->meterid . ')';

        $curl = new wwd_db($method, 'GET', []);
        $response = $curl->exec();
        $err = $curl->error();
        $curl->close();

        if ($err) {
            $output = $err;
        } else {
            $odd = new wwd_oddrow('oddrow');
            $data = json_decode($response, true);
            $output = '<div class="container">';
            //    "meter_id": "11005",
            $output .= $this->meterDetailRow('Meter ID', $data['meter_id'], $odd->getClass());
            //    "Geo": "11-P-0.01E",
            $output .= $this->meterDetailRow('Geo', $data['Geo'], $odd->getClass());
            //    "Area": "Service Area 04b",
            $output .= $this->meterDetailRow('Area', $data['Area'], $odd->getClass());
            //    "Lateral": "11L",
            $output .= $this->meterDetailRow('Lateral', $data['Lateral'], $odd->getClass());
            //    "status": "Active",
            $output .= $this->meterDetailRow('Status', $data['status'], $odd->getClass());
            //    "address": "Unnamed Road, Cantua Creek, CA 93608, USA",
            $output .= $this->meterDetailRow('Address', $data['address'], $odd->getClass());
            //    "serial_no": "20013556-12"
            $output .= $this->meterDetailRow('Serial No', $data['serial_no'], $odd->getClass());
            //    "Manufacturer": "Water Specialties",
            $output .= $this->meterDetailRow('Mfg', $data['Manufacturer'], $odd->getClass());
            //    "Longitude": "-120.4053",
            $output .= $this->meterDetailRow('Longitude', $data['Longitude'], $odd->getClass());
            //    "Latitude": "36.5449",
            $output .= $this->meterDetailRow('Latitude', $data['Latitude'], $odd->getClass());
            //    "specialaccess": "Select",
            $output .= $this->meterDetailRow('Access', $data['specialaccess'], $odd->getClass());
            // Map
            $output .= $this->mapurl($data['Latitude'], $data['Longitude'], $odd->getClass());

            $output .= '<hr>';

            $output .= '</div>'; // container
        }
        return $output;
    }

    private function getMeterAccounts()
    {
        $output = '';
        $method = '/wp-sp-meteraccounts/';
        $formData = ['meterid' => $this->meterid];

        $curl = new wwd_db($method, 'POST', $formData);
        $response = $curl->exec();
        $err = $curl->error();
        $curl->close();

        if ($err) {
            $output = $err;
        } else {
            $json = json_decode($response, true);

            $data = $json['value'];

            $output = '<div class="container">';
            $output .= '<div class="row large">'
                . '<div class="col-3">Accounts</div>'
                . '<div class="col-8"></div>'
                . '</div>';


            $oddrow = new wwd_oddrow('oddrow');
            foreach ($data as $row) {
                $class = $oddrow->getClass();
//                $link = '<a href="/' . $this->accountSlug . '/?id=' . $row['account'] . '">';
                $link = '/' . $this->accountSlug . '/?id=' . $row['account'];
                $onclick = 'onclick="wwd_gotoLink(\'' . $link . '\')"';
                $output .= '<div class="row ' . $class . ' large" '. $onclick . ' >'
                    . '<div class="col-3">' . $row['account'] . '</div>'
                    . '<div class="col-8">' . $row['fullname'] . '</div>'
                    . '</div></a>';

            }
            $output .= '</div>'; // container
        }
        return $output;
    }

    public function render()
    {
        $output = '';

        if ($this->isAuth) {
            $output .= '<div id="wwd_table">';
            $output .= $this->getMeterDetails();
            $output .= $this->getMeterAccounts();
            $output .= '</div>';

            $Result = $output;
        } else {
            $authMessage = new wwd_auth_msg();

            $Result = '<hr>' . $authMessage->notAuthorized() . '<hr>';
        }
        return $Result;
    }
}

$wwd_meter = new wwdMeter();
