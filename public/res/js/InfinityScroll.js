var page = 1;
var container;
var callback;
var URI;
var loading = false;

var loadNext = function (first = false) {
    if(loading)
        return;
    loading = true;
    console.log("Starting loading: " + page);
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            let r = JSON.parse(this.responseText);
            if(callback === null) {
                container.insertAdjacentHTML('beforeend', r.data);
            } else {
                callback(r.data);
            }
            if(r.current_page < r.total_pages) {
                loading = false;
            }
            var body = document.body,
                html = document.documentElement;
            var height = Math.max(body.scrollHeight, body.offsetHeight,
                html.clientHeight, html.scrollHeight, html.offsetHeight);
            console.log(window.innerHeight, document.body.clientHeight);
            if (window.innerHeight > document.body.clientHeight) {
                setTimeout(() => {
                    loadNext(true);
                }, 1);
            }
            //console.log(r);
        
        }
    };
    xhttp.open("GET", URI + page, true);
    page++;
    xhttp.send();
}

function initInfinityScroll(U, cb = null, c = null, p = 1) {
    page = p;
    URI = U;
    callback = cb;
    if (c === null) {
        var scriptTag = document.getElementsByTagName('script');
        scriptTag = scriptTag[scriptTag.length - 1];
        c = scriptTag.parentNode;
    
    }
    container = c;
 
    window.addEventListener('scroll', function (e) {
        var body = document.body,
            html = document.documentElement;

        var height = Math.max(body.scrollHeight, body.offsetHeight,
            html.clientHeight, html.scrollHeight, html.offsetHeight);


        var doc = document.documentElement;
        var left = (window.pageXOffset || doc.scrollLeft) - (doc.clientLeft || 0);
        var top = (window.pageYOffset || doc.scrollTop) - (doc.clientTop || 0);
        if (top + window.innerHeight >= height * 0.9) {
            //console.log(top + window.innerHeight, height * 0.9);
            loadNext();
        }
    });

    window.addEventListener('resize', function (e) {
        var body = document.body,
            html = document.documentElement;

        var height = Math.max(body.scrollHeight, body.offsetHeight,
            html.clientHeight, html.scrollHeight, html.offsetHeight);


        var doc = document.documentElement;
        var left = (window.pageXOffset || doc.scrollLeft) - (doc.clientLeft || 0);
        var top = (window.pageYOffset || doc.scrollTop) - (doc.clientTop || 0);
        if (top + window.innerHeight >= height * 0.9) {
            //console.log(top + window.innerHeight, height * 0.9);
            loadNext();
        }
    });
    loadNext(true);
}