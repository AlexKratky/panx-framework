</div>
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
let x = document.querySelector("a[href*='"+(window.location.pathname.replace(/\/\/+/g, '/'))+"']");
if(x) {
    x.style.color = "#fff";
}
</script>