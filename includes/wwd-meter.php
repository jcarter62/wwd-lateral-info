<?php

class wwdMeter {
    private $auth;
    private $isAuth = false;
    private $meterid = 0;

    public function __construct()
    {
        add_shortcode('wwd-meter', array( $this,'execute'));
    }

    public function execute()
    {
        $this->auth = new wwd_auth();
        $this->isAuth = $this->auth->isIsAuthenticated();
        $this->meterid = get_query_var('id', '0');

        return $this->render();
    }

    // Create address string, including 3 lines and comma where needed.
    private function fmtAddress($rec) {
        $a2 = trim($rec['Address2']);
        $a3 = trim($rec['Address3']);

        $result = $rec['Address1'];
        if ( strlen($a2) > 0 ) {
            $result .= ', ' . $a2;
        }
        if ( strlen($a3) > 0 ) {
            $result .= ', ' . $a3;
        }
        return $result;
    }

    private function meterDetailRow($name, $details) {
        $output = '<div class="row large">';
        $output .= '<div class="col">' . $name . '</div>';
        $output .= '<div class="col"><strong>' . $details . '</strong></div>';
        $output .= '</div>';
        return $output;
    }

    private function mapurl($lat, $lon) {
        $output = '<a href="https://www.google.com/maps?q='
            . $lat . ',' . $lon
            . '&z=22;t=h" '
            . 'target="_blank">'
            . 'Map'
            . '</a>';
        return $output;
    }

    private function getMeterDetails() {
        $output = '';
        $method = '/wp-v-wtr-asset(' . $this->meterid . ')';
        $curl = $this->curlOpts($method, 'GET');
        if ( $curl !== null ) {
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                $output = $err;
            }
            else {
                $data = json_decode($response, true);
                $output = '<div class="container">';
                //    "meter_id": "11005",
                $output .= $this->meterDetailRow('Meter ID', $data['meter_id']);
                //    "Geo": "11-P-0.01E",
                $output .= $this->meterDetailRow('Geo', $data['Geo']);
                //    "Area": "Service Area 04b",
                $output .= $this->meterDetailRow('Area', $data['Area']);
                //    "Lateral": "11L",
                $output .= $this->meterDetailRow('Lateral', $data['Lateral']);
                //    "status": "Active",
                $output .= $this->meterDetailRow('Status', $data['status']);
                //    "address": "Unnamed Road, Cantua Creek, CA 93608, USA",
                $output .= $this->meterDetailRow('Address', $data['address']);
                //    "serial_no": "20013556-12"
                $output .= $this->meterDetailRow('Serial No', $data['serial_no']);
                //    "Manufacturer": "Water Specialties",
                $output .= $this->meterDetailRow('Mfg', $data['Manufacturer']);
                //    "Longitude": "-120.4053",
                $output .= $this->meterDetailRow('Longitude', $data['Longitude']);
                //    "Latitude": "36.5449",
                $output .= $this->meterDetailRow('Latitude', $data['Latitude']);
                //    "specialaccess": "Select",
                $output .= $this->meterDetailRow('Special Access', $data['specialaccess']);
                // Map
                $output .= $this->mapurl( $data['Latitude'], $data['Longitude'] );

                $output .= '<hr>';

                $output .= '</div>'; // container
            }
        }
        return $output;
    }

//    "@odata.context": "https://cdata.api.wwddata.com/$metadata#wp-v-wtr-asset/$entity",
//    "specialaccess": "Select",

//    "meter_id": "11005",
//    "Geo": "11-P-0.01E",
//    "Area": "Service Area 04b",
//    "Lateral": "11L",
//    "status": "Active",
//    "address": "Unnamed Road, Cantua Creek, CA 93608, USA",
//    "serial_no": "20013556-12"

//    "metersize": "",
//    "Type": "",
//    "metertype": "Water",
//    "Manufacturer": "Water Specialties",
//    "Longitude": "-120.4053",
//    "Latitude": "36.5449",

    private function curlOpts($method, $PostOrGet) {
        $output = null;

        if ( $this->isAuth ) {
            // Load options, and present to users.
            $api = new wwd_api_info();
            $apikey = $api->getApikey();
            $apiurl = $api->getApiurl();

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $apiurl . $method,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $PostOrGet,
                CURLOPT_HTTPHEADER => array(
                    "Cache-Control: no-cache",
                    "x-cdata-authtoken: " . $apikey
                ),
                CURLOPT_SSL_VERIFYPEER => false,
            ));
            $output = $curl;

        }
        return $output;
    }

    public function render() {
        $output = '';

        if ( $this->isAuth ) {
            $output .= $this->getMeterDetails();

            $Result = $output;
        } else {
            $authMessage = new wwd_auth_msg();

            $Result = '<hr>' . $authMessage->notAuthorized() . '<hr>';
        }
        return $Result;
    }
}

$wwd_meter = new wwdMeter();
