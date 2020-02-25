
document.addEventListener("DOMContentLoaded", function (event) {
    //forms elements
    let x = document.querySelectorAll("input");
    x.forEach((item) => {
        if(item.hasAttribute("type")) {
            let t = item.getAttribute("type");
            if(t == "checkbox" || t == "radio")
                return;
            if (!item.classList.contains("input") && !item.classList.contains("form-input") && !item.classList.contains("file-upload-field")) {
                return;
            }
            if (item.classList.contains("skip-check") || (item.dataset.formCheck && item.dataset.formCheck == "false")) {
                return;
            }
            if(t == "file") {
                item.addEventListener("change", () => {
                    item.parentElement.setAttribute("data-text", item.value.replace(/.*(\/|\\)/, ''));
                    if (item.value) {
                        item.parentElement.classList.add("has-val");
                        if (item.parentElement.hasAttribute('required')) {
                            item.parentElement.success();
                        }
                    } else {
                        item.parentElement.classList.remove("has-val");
                        if (item.parentElement.hasAttribute('required')) {
                            item.parentElement.displayError("This field is required!");
                        }
                    }
                });

            }
        }
        item.addEventListener("keyup", inputUpdate);
        item.addEventListener("focusout", inputUpdate);
        item.displayError = function(err) {
            if(item.id && item.dataset.formCheck == "true") {
                document.getElementById(item.id + "-error").innerHTML = err;
                document.getElementById(item.id + "-error").style.visibility = "visible";
            }
            item.error();
        }
        item.error = function () {
            item.classList.add("invalid-val");
            item.classList.remove("valid-val");
        }
        item.success = function () {
            if (item.dataset.formCheck == "true")
                item.displayError("<br>");
            item.classList.add("valid-val");
            item.classList.remove("invalid-val");
        }
        if (item.id && item.dataset.formCheck == "true") {
            item.insertAdjacentHTML('afterend', '<div id="'+item.id+'-error" class="input-error"><br></div>')
        }
    });
    x = document.querySelectorAll("select[data-form-check='true']");
    x.forEach((item) => {
        if (!item.classList.contains("input") && !item.classList.contains("form-input") && !item.classList.contains("file-upload-field")) {
            return;
        }
        if (item.classList.contains("skip-check") || (item.dataset.formCheck && item.dataset.formCheck == "false")) {
            return;
        }
        item.addEventListener("change", selectUpdate);
        item.addEventListener("focusout", selectUpdate);
        
        item.displayError = function (err) {
            if (item.id && item.dataset.formCheck == "true") {
                document.getElementById(item.id + "-error").innerHTML = err;
                document.getElementById(item.id + "-error").style.visibility = "visible";
            }
            item.error();
        }
        item.error = function () {
            item.classList.add("invalid-val");
            item.classList.remove("valid-val");
        }
        item.success = function () {
            if (item.dataset.formCheck == "true")
                item.displayError("<br>");
            item.classList.add("valid-val");
            item.classList.remove("invalid-val");
        }
        if (item.id && item.dataset.formCheck == "true") {
            item.insertAdjacentHTML('afterend', '<div id="' + item.id + '-error" class="input-error"><br></div>')
        }
    });

    //navbar
    window.addEventListener("resize", navbarResize);
    navbarResize();


    //dropdowns

    document.querySelectorAll(".dropdown-btn").forEach((item) => {
        item.addEventListener("click", () => {
            let c = item.parentNode;
            c = c.querySelector(".dropdown-menu");
            if (c.classList.contains("show")) {
                c.classList.remove("show");
            } else {
                c.classList.add("show");
            }
            let h = c.offsetHeight;
            let t = c.getBoundingClientRect().top;
            let s = h + t;
            let wH = window.innerHeight;
            let bH = item.offsetHeight;
            if(s > wH) {
                //should appear to the top of the element, 3 is padding
                c.style.marginTop = "-" + (h+bH+2) + "px";
            } else {
                c.style.marginTop = ".125rem";
            }
        });
    });

    //tabs
    document.querySelectorAll(".tab").forEach((item) => {
        item.querySelectorAll(".list-group-item").forEach((menu) => {
            menu.addEventListener("click", (e) => {
                // menu click
                item.querySelector(".list-group-item.active").classList.remove("active");
                item.querySelector(".tab-pane.show").classList.remove("show");
                let tab = item.querySelector("#" + menu.dataset.toggle);
                tab.classList.add("show");
                tab.style.opacity = "0";
                setTimeout(() => {
                    tab.style.opacity = "1";
                }, 1);
                menu.classList.add("active");
            });
            if (window.location.hash && window.location.hash == "#" + menu.dataset.toggle) {
                // if user press F5, the current tab will be opened 
                menu.click();
            }
        });
    });

    if (document.querySelector(".layout-navbar.navbar").classList.contains('navbar-scroll')) {
        //scroll navbar, listen to scroll event
        var currentScroll;
        setTimeout(() => {
            currentScroll = (window.pageYOffset || document.documentElement.scrollTop) - (document.documentElement.clientTop || 0);
            var currentScrollToTheTop = 0;
            window.addEventListener("scroll", () => {
                var top = (window.pageYOffset || document.documentElement.scrollTop) - (document.documentElement.clientTop || 0);
                if(top > currentScroll) {
                    //down
                    currentScrollToTheTop -= ((!isNaN(top - currentScroll)) ? (top - currentScroll) : 0);
                    if (currentScrollToTheTop < 0) currentScrollToTheTop = 0;
                    if (currentScrollToTheTop > 100) currentScrollToTheTop = 100;
                    currentScroll = (window.pageYOffset || document.documentElement.scrollTop) - (document.documentElement.clientTop || 0);
                } else {
                    //up
                    currentScrollToTheTop += ((!isNaN(currentScroll - top)) ? (currentScroll - top) : 0);
                    if (currentScrollToTheTop < 0) currentScrollToTheTop = 0;
                    if (currentScrollToTheTop > 100) currentScrollToTheTop = 100;
                    currentScroll = (window.pageYOffset || document.documentElement.scrollTop) - (document.documentElement.clientTop || 0);
                }
                // TODO animation
                if(currentScrollToTheTop == 100) {
                    document.querySelector(".layout-navbar.navbar").classList.add("navbar-fixed");
                } else if (currentScrollToTheTop == 0) {
                    document.querySelector(".layout-navbar.navbar").classList.remove("navbar-fixed");
                }

            });
        }, 150);
    }

    //search boxes
    document.querySelectorAll(".search-box").forEach((item) => {
        let sw = item.querySelector(".search-wrapper");
        item.querySelector(".search-more").addEventListener("click", () => {
            if(sw.classList.contains("active")) {
                sw.classList.remove("active");
                item.querySelector(".search-advanced").classList.remove("show");
            } else {
                sw.classList.add("active");
                item.querySelector(".search-advanced").classList.add("show");
            }
        });
        item.querySelector(".btn-close").addEventListener("click", () => {
            sw.classList.remove("active");
            item.querySelector(".search-advanced").classList.remove("show");
        });
    });


    //sidenav
    document.querySelectorAll(".sidenav-item .sidenav-toggle").forEach((item) => {
        item.addEventListener("click", () => {
            if(!item.classList.contains("opened")) {
                let menu = item.parentElement.querySelector(".sidenav-menu");
                item.classList.add("opened");
                menu.style.maxHeight = (menu.childElementCount * 48)+"px";
            } else {
                let menu = item.parentElement.querySelector(".sidenav-menu");
                item.classList.remove("opened");
                menu.style.maxHeight = "0px";
            }
        });
    });

    document.querySelectorAll(".navbar-sidemenu-switch").forEach((item) => {
        item.addEventListener("click", () => {
            if (document.querySelector(".layout-sidenav")) {
                if(document.querySelector(".layout-sidenav").classList.contains("closed")) {
                    document.querySelector(".layout-sidenav").classList.remove("closed");
                } else {
                    document.querySelector(".layout-sidenav").classList.add("closed");
                }
            }
        });
        
    });
});


function inputUpdate(e) {
    if (this.value) {
        this.classList.add("has-val");
        if (this.hasAttribute('required') && e.type == "focusout") {
            this.success();
        }
    } else {
        this.classList.remove("has-val");
        if (this.hasAttribute('required') && e.type == "focusout") {
            this.displayError("This field is required!");
        }
    }

}

function selectUpdate() {
    if (this.options[this.selectedIndex].value === '') {
        this.classList.remove("has-val");
        if (this.hasAttribute('required')) {
            this.displayError("This field is required!");

        }
    } else {
        this.classList.add("has-val");
        if (this.hasAttribute('required')) {
            this.success();
        }
    }
}

var totalWidth;
var sidenavClosed = false;
function navbarResize() {
    let n = document.querySelector(".layout-navbar");
    if(!totalWidth) {
        totalWidth = n.querySelector(".navbar-title").offsetWidth + n.querySelector(".navbar-items").offsetWidth;
        totalWidth += 200; //another 200px
        n.querySelector(".navbar-mobile").addEventListener("click", navbarToggle)
    } else {
        //should recalculate?
        if (n.querySelector(".navbar-title").offsetWidth + n.querySelector(".navbar-items").offsetWidth + 200 > totalWidth) {
            totalWidth = n.querySelector(".navbar-title").offsetWidth + n.querySelector(".navbar-items").offsetWidth + 200;
            return;
        }
    }
    if(totalWidth > window.innerWidth) {
        //menu must be closed
        n.querySelector(".navbar-items").style.display = "none";
        n.querySelector(".navbar-items").classList.add("navbar-items-mobile");
        n.querySelector(".navbar-mobile").style.display = "block";
        let c = n.querySelectorAll(".navbar-items > .navbar-item");
        c.forEach((item) => {
            item.classList.add("navbar-item-mobile");
        })

    } else {
        n.querySelector(".navbar-items").style.display = "block";

        n.querySelector(".navbar-items").classList.remove("navbar-items-mobile");
        n.querySelector(".navbar-mobile").style.display = "none";
        let c = n.querySelectorAll(".navbar-items > .navbar-item");
        c.forEach((item) => {
            item.classList.remove("navbar-item-mobile");
        })
    }

    //sidenav 
    if (!sidenavClosed && window.innerWidth < 1000) {
        //sidenav was not closed by script yet
        if (document.querySelector(".layout-sidenav")) {
            if(!document.querySelector(".layout-sidenav").classList.contains("closed")) {
                document.querySelector(".layout-sidenav").classList.add("closed");
            }
            sidenavClosed = true;
        }
    }
}

function navbarToggle() {
    let n = document.querySelector(".layout-navbar");
    let o = n.querySelector(".navbar-items").style.display == "block";
    document.querySelector("body").style.overflow = (o ? "auto" : "hidden");
    if(!o) {
        n.querySelector(".navbar-items").style.display = "block";
    }
    setTimeout(() => {
        n.querySelector(".navbar-items-mobile").style.maxHeight = (o ? "0px" : "calc(100vh - 64px)");
        
    }, 10);
    if(o) {
        setTimeout(() => {
            n.querySelector(".navbar-items").style.display = "none";
        }, 300);
    }
}


