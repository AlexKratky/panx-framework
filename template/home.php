<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <title>panx framework</title>
    <link rel="stylesheet" href="/res/css/panx-design.css">
    <script src="/res/js/panx-design.js"></script>
    <link rel="shortcut icon" href="//panx.eu/res/img/favicon.png" type="image/x-icon">
    <meta name="theme-color" content="#0055f4">
    <!-- Windows Phone -->
    <meta name="msapplication-navbutton-color" content="#0055f4">
    <!-- iOS Safari -->
    <meta name="apple-mobile-web-app-status-bar-style" content="#0055f4">
    <link rel="stylesheet" href="/res/css/panx.css">
    <script src="/res/js/panx.js"></script>
</head>
<body>
    <div class="layout-wrapper">
        <div class="layout-inner">
            <div class="layout-navbar navbar navbar-fixed">
                <div class="navbar-title">
                    <div class="navbar-logo">
                        <img src="//panx.eu/res/img/logo.svg" alt="PD">
                    </div>
                    <div class="navbar-text">
                        panx
                    </div>
                </div>
                <div class="navbar-items">
                    <a href="#features">
                        <div class="navbar-item">
                            Features
                        </div>
                    </a>
                    <a href="#components">
                        <div class="navbar-item">
                            Components
                        </div>
                    </a>
                    <a href="#extensions">
                        <div class="navbar-item">
                            Extensions
                        </div>
                    </a>
                    <a href="/docs">
                        <div class="navbar-item">
                            Documentation
                        </div>
                    </a>
                </div>
                <div class="navbar-mobile">
                    <i class="icon ion-md-menu"></i>
                </div>
            </div>
            <div class="layout-container">
                
                <div class="layout-content">

                    <div class="main-img main">

                        
                        <div class="row" style="max-width: 1400px; margin: auto auto; position: relative;">
                            <div class="main-title">
                                The PHP micro framework
                            </div>
                            <div class="main-subtitle">
                                panx is lightweight php framework with many features. indeed.
                            </div>
                            <div class="main-options">
                                <a href="#features"><button class="btn mobile-full-width btn-primary btn-panx">View features</button></a>
                                <a href="https://github.com/AlexKratky/panx-framework"><button class="btn mobile-full-width btn-secondary">Github</button></a>
                            </div>
                            <div class="main-vector"><img src="//panx.eu/res/img/stargate.svg"></div>
                        </div>
                    </div>

                    <!--<div class="row" style="max-width: 1200px; margin: auto auto; text-align: center;">
                        <h1>„There's a difference between knowing the path and walking the path.“</h1>
                        <img src="morpheus.svg" style="width: 128px;"><br>
                        <h3>but panx will guide you through the path of web development</h3>
                    </div>-->

                    <div class="row" style="max-width: 1400px; margin: auto auto; " id="features">
                        <h1>Features</h1>
                        <div class="col-3 col-s-6">
                            <div class="box-preview">
                                <div class="box-icon"><i class="icon ion-md-speedometer"></i></div>
                                <div class="box-title">Lightweight</div>
                                <div class="box-subtitle">panx framework is super fast</div>
                            </div>
                        </div>
                        <div class="col-3 col-s-6">
                            <div class="box-preview">
                                <div class="box-icon"><i class="icon ion-md-airplane"></i></div>
                                <div class="box-title">Routing</div>
                                <div class="box-subtitle">effortless routing system</div>
                            </div>
                        </div>
                        <div class="col-3 col-s-6">
                            <div class="box-preview">
                                <div class="box-icon"><i class="icon ion-md-lock"></i></div>
                                <div class="box-title">Auth</div>
                                <div class="box-subtitle">integrated authentification system</div>
                            </div>
                        </div>
                        <div class="col-3 col-s-6">
                            <div class="box-preview">
                                <div class="box-icon"><i class="icon ion-md-cube"></i></div>
                                <div class="box-title">API</div>
                                <div class="box-subtitle">API endpoint & REST API</div>
                            </div>
                        </div>

                        <div class="col-3 col-s-6">
                            <div class="box-preview">
                                <div class="box-icon"><i class="icon ion-md-globe"></i></div>
                                <div class="box-title">Multi language</div>
                                <div class="box-subtitle">distribute your web in many languages</div>
                            </div>
                        </div>
                        <div class="col-3 col-s-6">
                            <div class="box-preview">
                                <div class="box-icon"><i class="icon ion-md-desktop"></i></div>
                                <div class="box-title">Template systems</div>
                                <div class="box-subtitle">support for template systems - by default Latte</div>
                            </div>
                        </div>
                        <div class="col-3 col-s-6">
                            <div class="box-preview">
                                <div class="box-icon"><i class="icon ion-md-browsers"></i></div>
                                <div class="box-title">Forms</div>
                                <div class="box-subtitle">safe forms using FormX</div>
                            </div>
                        </div>
                        <div class="col-3 col-s-6">
                            <div class="box-preview">
                                <div class="box-icon"><i class="icon ion-md-bug"></i></div>
                                <div class="box-title">Debugging</div>
                                <div class="box-subtitle">painless debugging using Tracy</div>
                            </div>
                        </div>
                    </div>

                    <div class="row" style="max-width: 1400px; margin: auto auto;" id="components">
                        <h1>Components</h1>
                        <div class="grid grid-3 grid-s-2">
                            <div >
                                <div class="card">
                                    <div class="card-title">RouteX</div>
                                    <div class="card-text">
                                        Fast & easy routing system.
                                    </div>
                                    <div class="card-actions">
                                        <a href="https://github.com/AlexKratky/RouteX"><button class="btn mobile-full-width btn-primary">Documentation</button></a>
                                        <a href="https://github.com/AlexKratky/RouteX"><button class="btn mobile-full-width btn-secondary">View on Github</button></a>
                                    </div>
                                </div>
                            </div>
                            <div >
                                <div class="card">
                                    <div class="card-title">AuthX</div>
                                    <div class="card-text">
                                        Simple authentification system.
                                    </div>
                                    <div class="card-actions">
                                        <a href="https://github.com/AlexKratky/AuthX"><button class="btn mobile-full-width btn-primary">Documentation</button></a>
                                        <a href="https://github.com/AlexKratky/AuthX"><button class="btn mobile-full-width btn-secondary">View on Github</button></a>
                                    </div>
                                </div>
                            </div>
                            <div >
                                <div class="card">
                                    <div class="card-title">APIX</div>
                                    <div class="card-text">
                                        Creates API endpoints, controls rate limits and so on.
                                    </div>
                                    <div class="card-actions">
                                        <a href="https://github.com/AlexKratky/APIX"><button class="btn mobile-full-width btn-primary">Documentation</button></a>
                                        <a href="https://github.com/AlexKratky/APIX"><button class="btn mobile-full-width btn-secondary">View on Github</button></a>
                                    </div>
                                </div>
                            </div>

                            <div >
                                <div class="card">
                                    <div class="card-title">FormX</div>
                                    <div class="card-text">
                                        Safe and easy to validate forms.
                                    </div>
                                    <div class="card-actions">
                                        <a href="#"><button class="btn mobile-full-width btn-primary">Documentation</button></a>
                                        <a href="#"><button class="btn mobile-full-width btn-secondary">View on Github</button></a>
                                    </div>
                                </div>
                            </div>
                            <div >
                                <div class="card">
                                    <div class="card-title">CacheX</div>
                                    <div class="card-text">
                                        Cache system.
                                    </div>
                                    <div class="card-actions">
                                        <a href="https://github.com/AlexKratky/CacheX"><button class="btn mobile-full-width btn-primary">Documentation</button></a>
                                        <a href="https://github.com/AlexKratky/CacheX"><button class="btn mobile-full-width btn-secondary">View on Github</button></a>
                                    </div>
                                </div>
                            </div>
                            <div >
                                <div class="card">
                                    <div class="card-title">RestAPI</div>
                                    <div class="card-text">
                                        Create REST APIs only by providing information about SQL tables.
                                    </div>
                                    <div class="card-actions">
                                        <a href="#"><button class="btn mobile-full-width btn-primary">Documentation</button></a>
                                        <a href="#"><button class="btn mobile-full-width btn-secondary">View on Github</button></a>
                                    </div>
                                </div>
                            </div>

                            <div >
                                <div class="card">
                                    <div class="card-title">URL</div>
                                    <div class="card-text">
                                        Simple work with URL - get its part, etc.
                                    </div>
                                    <div class="card-actions">
                                        <a href="https://github.com/AlexKratky/URL"><button class="btn mobile-full-width btn-primary">Documentation</button></a>
                                        <a href="https://github.com/AlexKratky/URL"><button class="btn mobile-full-width btn-secondary">View on Github</button></a>
                                    </div>
                                </div>
                            </div>
                            <div >
                                <div class="card">
                                    <div class="card-title">PaginationX</div>
                                    <div class="card-text">
                                        Split content to multiple pages. The data source can be a file or SQL table.
                                    </div>
                                    <div class="card-actions">
                                        <a href="https://github.com/AlexKratky/PaginationX"><button class="btn mobile-full-width btn-primary">Documentation</button></a>
                                        <a href="https://github.com/AlexKratky/PaginationX"><button class="btn mobile-full-width btn-secondary">View on Github</button></a>
                                    </div>
                                </div>
                            </div>
                            <div >
                                <div class="card">
                                    <div class="card-title">LoggerX</div>
                                    <div class="card-text">
                                        Log your data to files.
                                    </div>
                                    <div class="card-actions">
                                        <a href="https://github.com/AlexKratky/LoggerX"><button class="btn mobile-full-width btn-primary">Documentation</button></a>
                                        <a href="https://github.com/AlexKratky/LoggerX"><button class="btn mobile-full-width btn-secondary">View on Github</button></a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="stripe-box">
                        <div class="row" style="max-width: 1100px; margin: auto auto;">
                            <h1>panx-design</h1>
                            <div class="stripe-box-text">
                                A modern UI kit for web developers. This web is build with this kit. Find more <a href="#">here</a>.
                            </div>
                            <a href="#">
                                <div class="row stripe-box-img"  style="max-width: 1000px; margin: auto auto;">
                                    <img src="//panx.eu/res/img/panx-design.png" style="max-width: 100%; border-radius: 10px;">
                                </div>
                            </a>
                        </div>
                    </div>
                    
                    <div class="row" style="max-width: 1400px; margin: auto auto;" id="extensions">
                        <h1>Extensions</h1>
                        <div class="grid">
                            <div>
                                <div class="card">
                                    <div class="card-title">Auth</div>
                                    <div class="card-text">
                                        This extension contains pre-styled user forms, routes and so on. By using this extension you will not have to create own forms and routes.
                                        Auth extension uses default Auth class. 
                                    </div>
                                    <div class="card-actions">
                                        <a href="https://github.com/AlexKratky/panx-auth"><button class="btn mobile-full-width btn-primary">View on Github</button></a>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="card">
                                    <div class="card-title">TotalAdmin</div>
                                    <div class="card-text">
                                        Manage users' roles and permissions in simple administration.
                                    </div>
                                    <div class="card-actions">
                                        <a href="https://github.com/AlexKratky/panx-TotalAdmin"><button class="btn mobile-full-width btn-primary">View on Github</button></a>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="card">
                                    <div class="card-title">PostManager</div>
                                    <div class="card-text">
                                        Manage your posts using this extension. PostManager works with markdown files.
                                    </div>
                                    <div class="card-actions">
                                        <a href="https://github.com/AlexKratky/panx-postmanager"><button class="btn mobile-full-width btn-primary">View on Github</button></a>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="card">
                                    <div class="card-title">InfinityScroll</div>
                                    <div class="card-text">
                                        Extension that contains a javascript file that processes the infinity scroll.
                                    </div>
                                    <div class="card-actions">
                                        <a href="https://github.com/AlexKratky/panx-infinityscroll"><button class="btn mobile-full-width btn-primary">View on Github</button></a>
                                    </div>
                                </div>
                            </div>
                            <div style="text-align: center; grid-column-start: 1; grid-column-end: 3;">
                                <a href="https://panx.eu/panx/download/extensions"><button class="btn mobile-full-width btn-primary btn-rounded">Marketplace</button></a>
                            </div>
                        </div>

                    </div>

                    <h1>With <span style="color: var(--danger)">❤</span> by <a href="http://alexkratky.com/" style="color: var(--primary)">Alex Kratky</a></h1>
                    

                    <div class="row" style="margin-top: 100px;">
                        <div class="col-12 np">
                            <nav class="footer">
                                <div class="row" style="max-width: 1200px; margin: auto auto;">
                                    <div class="col-4 col-s-12">
                                        <div>
                                            <a href="/" class="footer-brand">panx-framework</a>
                                        </div>
                                        <a class="footer-social" href="javascript:void(0)">
                                            <i class="ion ion-logo-twitter"></i>
                                        </a>
                                        &nbsp; &nbsp;
                                        <a class="footer-social" href="https://github.com/AlexKratky/panx-framework">
                                            <i class="ion ion-logo-github"></i>
                                        </a>
                                        &nbsp; &nbsp;
                                        <a class="footer-social" href="https://packagist.org/packages/alexkratky/panx" title="packagist">
                                            <i class="ion ion-md-link"></i>
                                        </a>
                                    </div>
                    
                                    <div class="col-8 np">
                                        <div class="row">
                                            <div class="col-4 col-s-6">
                                                <div class="footer-text">Components</div>
                                                <a href="https://packagist.org/packages/alexkratky/routex" class="footer-link">RouteX</a>
                                                <a href="https://packagist.org/packages/alexkratky/authx" class="footer-link">AuthX</a>
                                                <a href="https://packagist.org/packages/alexkratky/apix" class="footer-link">APIX</a>
                                                <a href="javascript:void(0)" class="footer-link">FormX</a>
                                                <a href="https://packagist.org/packages/alexkratky/cachex" class="footer-link">CacheX</a>
                                                <a href="javascript:void(0)" class="footer-link">RestAPI</a>
                                                <a href="https://packagist.org/packages/alexkratky/url" class="footer-link">URL</a>
                                                <a href="https://packagist.org/packages/alexkratky/paginationx" class="footer-link">PaginationX</a>
                                                <a href="https://packagist.org/packages/alexkratky/loggerx" class="footer-link">LoggerX</a>
                                            </div>
                                            <div class="col-4 col-s-6">
                                                <div class="footer-text">Extensions</div>
                                                <a href="https://github.com/AlexKratky/panx-auth" class="footer-link">Auth</a>
                                                <a href="https://github.com/AlexKratky/panx-TotalAdmin" class="footer-link">TotalAdmin</a>
                                                <a href="https://github.com/AlexKratky/panx-postmanager" class="footer-link">PostManager</a>
                                                <a href="https://github.com/AlexKratky/panx-infinityscroll" class="footer-link">InfinityScroll</a>
                                            </div>
                                            <div class="col-4 col-s-6">
                                                <div class="footer-text">Documentation</div>
                                                <a href="https://panx.eu/docs/intro" class="footer-link">Intro</a>
                                                <a href="https://panx.eu/docs/intro#composer" class="footer-link">Installation</a>
                                                <a href="https://panx.eu/docs/getting-started" class="footer-link">Getting started</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 footer-copyright">
                                        &copy; panx-framework 2019.
                                    </div>
                                </div>
                            </nav>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>
</html>
<link
    href="https://fonts.googleapis.com/css?family=Montserrat:100,200,300,400,500,600,700,800,900|Poppins:100,200,300,400,500,600,700,800,900&display=swap"
    rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans:400,700&display=swap" rel="stylesheet">


<!--https://ionicons.com/-->
<link href="https://unpkg.com/ionicons@4.5.10-0/dist/css/ionicons.min.css" rel="stylesheet">
<!--OTHER-->
<script>
        let links = ["features", "components", "extensions"];

        document.querySelectorAll('a[href*="#"]').forEach(item => {
            item.addEventListener("click", (e) => {
                console.log(item.hash);
                
                if (item.hash == "" || item.hash == "#") { //# 
                    event.preventDefault();
                    return;
                }
                if(links.indexOf(item.hash.replace("#", "")) === -1) return;
                
                    var target = document.getElementById(item.hash.replace("#", ""));
                    console.log(target);
                    
                    if (target) {
                        // Only prevent default if animation is actually gonna happen
                        event.preventDefault();
                        
                        target.scrollIntoView({ behavior: "smooth" });
                        
                    }
                
            })
        })
        window.addEventListener("resize", checkForMobileImg);
        var m = false;
        function checkForMobileImg() {
            let wrapper = document.querySelector(".stripe-box-img");
            let img = document.querySelector(".stripe-box-img img");
            if(window.innerWidth <= 680) {
                if(!m) {
                    img.src = "//panx.eu/res/img/panx-design-mobile.png";
                    img.style.maxWidth = "70%";
                    wrapper.style.textAlign = "center";
                    m = true;
                }
            } else {
                if(m) {
                    img.src = "//panx.eu/res/img/panx-design.png";
                    img.style.maxWidth = "100%";
                    wrapper.style.textAlign = "left";
                    m = false;
                }
            }
        }
        checkForMobileImg();
</script>