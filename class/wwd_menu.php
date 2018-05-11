<?php
/**
 * @Author WWD
 * @Copyright (c) 2018. Westlands Water District. (https://wwd.ca.gov)
 * This code is released under the GPL licence version 3 or later, available here
 * http://www.gnu.org/licenses/gpl.txt
 *
 */

class wwd_menu
{

    // Part of this class was implemented with the help of the following:
    // Ref: https://www.daggerhart.com/dynamically-add-item-to-wordpress-menus/
    /**
     * wwd_menu constructor.
     */
    public function __construct()
    {
        add_filter('wp_get_nav_menu_items', array($this, 'execute'), 20, 2);
    }

    /**
     * Simple helper function for make menu item objects
     *
     * @param $title - menu item title
     * @param $url - menu item url
     * @param $order - where the item should appear in the menu
     * @param int $parent - the item's parent item
     * @return \stdClass
     */
    private function custom_nav_menu_item($title, $url, $order, $parent = 0)
    {
        $item = new stdClass();
        $item->ID = 1000000 + $order + $parent;
        $item->db_id = $item->ID;
        $item->title = $title;
        $item->url = $url;
        $item->menu_order = $order;
        $item->menu_item_parent = $parent;
        $item->type = '';
        $item->object = '';
        $item->object_id = '';
        $item->classes = array();
        $item->target = '';
        $item->attr_title = '';
        $item->description = '';
        $item->xfn = '';
        $item->status = '';
        return $item;
    }

    /*
     * The purpose of the following methods are to add a Login or Logout menu
     * to the main menu.
     */
    private function logoutURL()
    {
        $url = wp_logout_url(get_home_url(null, '', null));
        return $url;
    }

    private function loginURL()
    {
        // Ref: https://codex.wordpress.org/Function_Reference/wp_login_url
        $url = wp_login_url(get_home_url(null, '', null));
        return $url;
    }

    public function execute($items, $menu)
    {
        if ($menu->slug == 'main') {
            if (get_current_user_id()) {
                $items[] = $this->custom_nav_menu_item('logout', $this->logoutURL(), 99);
            } else {
                $items[] = $this->custom_nav_menu_item('login', $this->loginURL(), 99);
            }
        }
        return $items;
    }
}

$wwd_menu = new wwd_menu();
