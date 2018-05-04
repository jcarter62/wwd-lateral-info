<?php

class wwdAccount
{
    private $auth;
    private $isAuth = false;
    private $account = 0;
    private $lateralSlug = '';
    private $meterSlug = '';

    public function __construct()
    {
        add_shortcode('wwd-account', array($this, 'execute'));
    }

    public function execute()
    {
        $this->auth = new wwd_auth();
        $this->isAuth = $this->auth->isIsAuthenticated();
        $this->account = get_query_var('id', '0');

        $this->lateralSlug = get_option('wwd-page-lateral');
        $this->meterSlug = get_option('wwd-page-meter');

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

    private function detailRow($name, $details, $class)
    {
        $rowClass = 'row large ' . $class;
        $output = '<div class="' . $rowClass . '">';
        $output .= '<div class="col-3">' . $name . '</div>';
        $output .= '<div class="col-8"><strong>' . $details . '</strong></div>';
        $output .= '</div>';
        return $output;
    }

    private function lateralRow($lateral, $class)
    {
        $qt = '"';
        $link = '<a href=' . $qt . '/' . $this->lateralSlug . '/?id=' . $lateral . $qt . '>';
        $rowClass = 'row large ' . $class;
        $output = $link . '<div class="' . $rowClass . '">';
        $output .= '<div class="col">' . $lateral . '</div>';
        $output .= '</div></a>';
        return $output;
    }

    private function contactRow($data, $class, $name)
    {
        $rowClass = 'row large ' . $class;
        $output = '<div class="' . $rowClass . '">';
        $output .= '<div class="col">' . $name . '</div>';
        $output .= '<div class="col">' . $data['method'] . '</div>';
        $output .= '<div class="col">' . $data['type'] . '</div>';
        $output .= '</div>';
        return $output;
    }

    private function meterRow($data, $class)
    {
        $qt = '"';
        $mlink = '<a href=' . $qt . '/' . $this->meterSlug . '/?id=' . $data['meterid'] . $qt . '>';
        $rowClass = 'row large ' . $class;
        $output = '<div class="' . $rowClass . '">';
        $output .= '<div class="col">' . $data['lateralname'] . '</div>';
        $output .= '<div class="col">' . $mlink . $data['meterid'] . '</a></div>';
        $output .= '<div class="col">' . $data['geo'] . '</div>';
        $output .= '</div>';
        return $output;
    }

    private function getAccountAddress()
    {
        $output = '';
        $method = '/wmis-account(' . $this->account . ')';

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

            $csz = $data['City'] . ' ' . $data['State'] . ' ' . $data['Zip'] . ' ';

            $output .= $this->detailRow('Account', $data['NAME_ID'], $odd->getClass());
            $output .= $this->detailRow('Name', $data['FullName'], $odd->getClass());
            $output .= $this->detailRow('Address', $this->fmtAddress($data), $odd->getClass());
            $output .= $this->detailRow('CSZ', $csz, $odd->getClass());

            $output .= '<hr>';
            $output .= '</div>'; // container
        }
        return $output;
    }

    private function getAccountContacts()
    {
        $output = '';
        $method = '/wp-sp-api-acctcontacts/';
        $formData = ['account' => $this->account];

        $curl = new wwd_db($method, 'POST', $formData);
        $response = $curl->exec();
        $err = $curl->error();
        $curl->close();

        if ($err) {
            $output = $err;
        } else {
            $odd = new wwd_oddrow('oddrow');
            $json = json_decode($response, true);
            $data = $json["value"];

            $output = '<div class="container">';
            $output .= '<div class="row large"><div class="col">Contacts:</div></div>';
            $lastname = '';
            foreach ($data as $row) {
                if ($row['fullname'] != $lastname) {
                    $lastname = $row['fullname'];
                    $output .= $this->contactRow($row, $odd->getClass(), $lastname);
                } else {
                    $output .= $this->contactRow($row, $odd->getClass(), '');
                }
            }

            $output .= '</div><hr>'; // container
        }
        return $output;
    }

    private function getAccountLaterals()
    {
        $output = '';
        $method = '/wp-sp-accountlats/';

        $formData = ['account' => $this->account];

        $curl = new wwd_db($method, 'POST', $formData);
        $response = $curl->exec();
        $err = $curl->error();
        $curl->close();

        if ($err) {
            $output = $err;
        } else {
            $odd = new wwd_oddrow('oddrow');
            $json = json_decode($response, true);
            $data = $json["value"];

            $output = '<div class="container">';
            $output .= '<div class="row large"><div class="col">Laterals:</div></div>';
            foreach ($data as $row) {
                $output .= $this->lateralRow($row['lateralname'], $odd->getClass());
            }
            $output .= '</div>'; // container
        }
        $output .= '<hr>';
        return $output;
    }

    private function getAccountMeters()
    {
        $output = '';
        $method = '/wp-sp-accountMeters/';
        $formData = ['account' => $this->account];

        $curl = new wwd_db($method, 'POST', $formData);
        $response = $curl->exec();
        $err = $curl->error();
        $curl->close();

        if ($err) {
            $output = $err;
        } else {
            $odd = new wwd_oddrow('oddrow');
            $json = json_decode($response, true);
            $data = $json["value"];

            $output = '<div class="container">';
            $output .= '<div class="row large">'
                . '<div class="col">Lateral</div>'
                . '<div class="col">Meter ID</div>'
                . '<div class="col">Geographic</div>'
                . '</div>';
            foreach ($data as $row) {
                $output .= $this->meterRow($row, $odd->getClass());
            }

            $output .= '</div><hr>'; // container
        }
        return $output;
    }

    public function render()
    {
        $output = '';

        if ($this->isAuth) {
            $output .= $this->getAccountAddress();
            $output .= $this->getAccountContacts();
            $output .= $this->getAccountLaterals();
            $output .= $this->getAccountMeters();

            $Result = $output;
        } else {
            $authMessage = new wwd_auth_msg();

            $Result = '<hr>' . $authMessage->notAuthorized() . '<hr>';
        }
        return $Result;
    }
}

$wwd_account = new wwdAccount();
