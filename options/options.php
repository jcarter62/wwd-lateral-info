<?php
/**
 * @Author WWD
 * @Copyright (c) 2018. Westlands Water District. (https://wwd.ca.gov)
 * This code is released under the GPL licence version 3 or later, available here
 * http://www.gnu.org/licenses/gpl.txt
 *
 */

class wwd_lat_info_assets
{

    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'exec_wp_enqueue_scripts'));
        add_action('admin_menu', array($this, 'exec_Options'));
    }

    function exec_Options()
    {
        add_menu_page('WWD Lateral Info Settings', 'WWD Lat Info', 'manage_options',
            'wwdLatInfoAdmin', array($this, 'wwdLatInfoAdminPage'), '', 200);
    }

    function wwdLatInfoAdminPage()
    {
        $set = new wwd_settings();
        $userid = get_current_user_id();

        if (array_key_exists('submit_admin_update', $_POST)) {
            // Check to see if this is a valid source.
            if ( wp_verify_nonce($_POST['_wpnonce'], 'update-data_' . $userid )  ) {
                $set->save('wwd-apikey', $_POST['inp-apikey']);
                $set->save('wwd-apiurl', $_POST['inp-apiurl']);
                $set->save('wwd-role', $_POST['inp-role']);

                $set->save('wwd-page-lateral', $_POST['inp-page-lateral']);
                $set->save('wwd-page-meter', $_POST['inp-page-meter']);
                $set->save('wwd-page-account', $_POST['inp-page-account']);

                ?>
                <div>Updated!!</div>
                <?php
            }
        }

        $apikey = $set->get('wwd-apikey', '');
        $apiurl = $set->get('wwd-apiurl', '');
        $role = $set->get('wwd-role', '');

        $pageLateral = $set->get('wwd-page-lateral', '');
        $pageMeter = $set->get('wwd-page-meter', '');
        $pageAccount = $set->get('wwd-page-account', '');

        ?>
        <div class="wrap">
            <h2>API Settings:</h2>
            <form method="post" action="">
                <label for="inp-apiurl">Base URL:</label>
                <textarea name="inp-apiurl" class="large-text"><?php print $apiurl; ?></textarea>
                <label for="inp-apikey">KEY:</label>
                <textarea name="inp-apikey" class="large-text"><?php print $apikey; ?></textarea>

                <label for="inp-role">Role Required for Viewing:</label>
                <select name="inp-role">
                    <?php wp_dropdown_roles($role); ?>
                </select>
                <hr>

                <label for="inp-page-account">Page Slug for Account:</label>
                <input type="text" name="inp-page-account"
                       class="large-text"
                       placeholder="page slug"
                       value="<?php print $pageAccount; ?>">

                <label for="inp-page-lateral">Page Slug for Lateral:</label>
                <input type="text" name="inp-page-lateral"
                       class="large-text"
                       placeholder="page slug"
                       value="<?php print $pageLateral; ?>">

                <label for="inp-page-meter">Page Slug for Meter:</label>
                <input type="text" name="inp-page-meter"
                       class="large-text"
                       placeholder="page slug"
                       value="<?php print $pageMeter; ?>">

                <input type="submit" name="submit_admin_update"
                       class="button button-primary" value="UPDATE SETTINGS">

                <?php wp_nonce_field('update-data_' . $userid ) ?>
            </form>
            <hr>
            <h2>Plugin Short codes:</h2>
            <hr>
            <h3>[wwd-lat-list]</h3>
            Generates a list of laterals.
            <hr>
            <h3>[wwd-lateral]</h3>
            Required: <i>Query String id</i><br>
            Generates a list of meters for a lateral.
            The url is expected to include a query parameter id.
            <hr>
            <h3>[wwd-accounts]</h3>
            Generates a list of accounts, as well as a search box at top of list.
            The list includes account number and account name.
            <hr>
            <h3>[wwd-account]</h3>
            Required: <i>Query String id</i><br>
            Generates account details, including:
            <ul style="list-style-type: disc">
                <li>Account name and address</li>
                <li>Account Contacts</li>
                <li>Laterals this account is associated</li>
                <li>Meters this account is associated</li>
            </ul>
            <hr>
            <h3>[wwd-meters]</h3>
            Generates a list of meters, as well as a search box at top of list.
            The list includes meter id, Geographic location, and lateral associated.
            <hr>
            <h3>[wwd-meter]</h3>
            Required: <i>Query String id</i><br>
            Generates meter details, including:
            <ul style="list-style-type: disc">
                <li>Meter detail fields (id, geo, area, ...)</li>
                <li>List of accounts the meter is associated</li>
                <li>URL to google maps associated with Longitude and Latitude</li>
            </ul>
            <hr>
        </div>

        <?php
    }

    function exec_wp_enqueue_scripts()
    {
        $root = plugin_dir_url(WWD_LAT_INFO_BASE);
        $url = $root . 'css/style.css';

        wp_enqueue_style('my-css-file', $url, '', time());

        $jsURL = $root . 'js/wwd_scripts.js';
        wp_enqueue_script('my-js-file', $jsURL, '', null, true );
    }
}

$wwd_lat_info_assets = new wwd_lat_info_assets();

