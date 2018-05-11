/*
 * @Author WWD
 * @Copyright (c) 2018. Westlands Water District. (https://wwd.ca.gov)
 * This code is released under the GPL licence version 3 or later, available here
 * http://www.gnu.org/licenses/gpl.txt
 *
 */

function wwd_gotoLink( link ) {
    wwd_dimTable();
    location.href= link ;
}

function wwd_dimTable() {
    // Ref:
    // https://coderwall.com/p/ryargg/a-very-simple-loading-animation-in-5-lines-of-javascript
    //
    let elem = document.getElementById('wwd_table');
    elem.className += " loading";
    elem.innerHTML = '<h3>Loading</h3>';
    let i = 0;
    setInterval(function() {
        i = ++i % 5;
        elem.innerHTML = '<h3>Loading ' + Array( i + 1 ).join('.') + '</h3>';
    }, 500);
}

function wwd_menu_onclick() {
    //
    // Attach on click menu to the main-menu
    //
    let elem = document.getElementById('menu-main');
    elem.onclick = wwd_dimTable;
}

wwd_menu_onclick();

