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
        ?>
        <div>Updated!!</div>
        <?php
    }

    $apikey = get_option('wwd-apikey', '');
    $apiurl = get_option('wwd-apiurl', '');

    ?>
    <div class="wrap">
        <h2>API Settings:</h2>
        <form method="post" action="">
            <label for="inp-apiurl">Base URL:</label>
            <textarea name="inp-apiurl" class="large-text"><?php print $apiurl; ?></textarea>
            <label for="inp-apikey">KEY:</label>
            <textarea name="inp-apikey" class="large-text"><?php print $apikey; ?></textarea>
            <input type="submit" name="submit_admin_update" class="button button-primary" value="UPDATE SCRIPTS">
        </form>
    </div>
    <hr>
    <div>
        <h3>
            &ast;
            This plugin requires users
            to be a member of the
            role &quot;<bold>wwdlist</bold>&quot;.
        </h3>

    </div>

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



