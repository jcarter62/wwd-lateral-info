<?php
/**
 * @Author WWD
 * @Copyright (c) 2018. Westlands Water District. (https://wwd.ca.gov)
 * This code is released under the GPL licence version 3 or later, available here
 * http://www.gnu.org/licenses/gpl.txt
 *
 */

// Wrapper to save and load options.  This class also
// performs sanitize or escape functions when saving and loading.
class wwd_settings
{
    public function get($option, $default = null) {
        $value = get_option($option, $default);
        $result = esc_html($value);
        return $result;
    }

    public function save($option, $value) {
        $evalue = esc_html($value);
        update_option($option, $evalue);
    }
}