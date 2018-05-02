<?php
/**
 * User: jim carter
 * Date: 4/21/18
 */

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
            <input type="number" name="inp-pagesize" class="large-text" placeholder="<?php print $pagesize; ?>">

            <label for="inp-role">Role Required for Viewing:</label>
            <select name="inp-role">
                <?php wp_dropdown_roles($role); ?>
            </select>
            <hr>

            <label for="inp-page-account">Page Slug for Account:</label>
            <input type="text" name="inp-page-account" class="large-text" placeholder="<?php print $pageAccount; ?>">

            <label for="inp-page-lateral">Page Slug for Lateral:</label>
            <input type="text" name="inp-page-lateral" class="large-text" placeholder="<?php print $pageLateral; ?>">

            <label for="inp-page-meter">Page Slug for Meter:</label>
            <input type="text" name="inp-page-meter" class="large-text" placeholder="<?php print $pageMeter; ?>">

            <input type="submit" name="submit_admin_update" class="button button-primary" value="UPDATE SETTINGS">
        </form>
    </div>
    <hr>

    <?php
}

function wwdLatInfoOptions()
{
    add_menu_page('WWD Lateral Info Settings', 'WWD Lat Info', 'manage_options',
        'wwdLatInfoAdmin', 'wwdLatInfoAdminPage', '', 200);
}

add_action('admin_menu', 'wwdLatInfoOptions');

function wwd_lat_info_assets() {
    $root = plugin_dir_url( WWD_LAT_INFO_BASE );
    $url = $root . 'css/style.css';
    wp_enqueue_style('my-css-file', $url, '', time());
}

add_action('wp_enqueue_scripts','wwd_lat_info_assets');



