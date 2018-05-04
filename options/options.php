<?php
/**
 * User: jim carter
 * Date: 4/21/18
 */

class wwd_lat_info_assets {

    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'exec_wp_enqueue_scripts'));
        add_action('admin_menu', array( $this, 'exec_Options' ));
    }

    function exec_Options()
    {
        add_menu_page('WWD Lateral Info Settings', 'WWD Lat Info', 'manage_options',
            'wwdLatInfoAdmin', array($this,'wwdLatInfoAdminPage'), '', 200);
    }

    function wwdLatInfoAdminPage()
    {
        if ( array_key_exists('submit_admin_update', $_POST ) ) {
            update_option('wwd-apikey', $_POST['inp-apikey']);
            update_option('wwd-apiurl', $_POST['inp-apiurl']);
            update_option('wwd-pagesize', $_POST['inp-pagesize']);
            update_option('wwd-role', $_POST['inp-role']);

            update_option('wwd-page-lateral', $_POST['inp-page-lateral']);
            update_option('wwd-page-meter', $_POST['inp-page-meter']);
            update_option('wwd-page-account', $_POST['inp-page-account']);

            ?>
            <div>Updated!!</div>
            <?php
        }

        $apikey = get_option('wwd-apikey', '');
        $apiurl = get_option('wwd-apiurl', '');
        $pagesize = get_option('wwd-pagesize', '');
        $role = get_option('wwd-role');

        $pageLateral = get_option('wwd-page-lateral');
        $pageMeter = get_option('wwd-page-meter');
        $pageAccount = get_option('wwd-page-account');

        ?>
        <div class="wrap">
            <h2>API Settings:</h2>
            <form method="post" action="">
                <label for="inp-apiurl">Base URL:</label>
                <textarea name="inp-apiurl" class="large-text"><?php print $apiurl; ?></textarea>
                <label for="inp-apikey">KEY:</label>
                <textarea name="inp-apikey" class="large-text"><?php print $apikey; ?></textarea>
                <label for="inp-pagesize">Page Size:</label>
                <input type="number" name="inp-pagesize" class="large-text" placeholder="Number" value="<?php print $pagesize; ?>">

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

    function exec_wp_enqueue_scripts() {
        $root = plugin_dir_url( WWD_LAT_INFO_BASE );
        $url = $root . 'css/style.css';
        wp_enqueue_style('my-css-file', $url, '', time());
    }
}

$wwd_lat_info_assets = new wwd_lat_info_assets();

