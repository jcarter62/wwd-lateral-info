

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
