<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?=$CONFIG["basic"]["APP_NAME"]?> | <?=Route::getTitle()?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="<?=$CONFIG["basic"]["APP_URL"]?>res/css/home.css" />
    <link href="https://fonts.googleapis.com/css?family=Maven+Pro" rel="stylesheet">
    <!-- Chrome, Firefox OS and Opera -->
    <meta name="theme-color" content="#1A1A1D">
    <!-- Windows Phone -->
    <meta name="msapplication-navbutton-color" content="#1A1A1D">
    <!-- iOS Safari -->
    <meta name="apple-mobile-web-app-status-bar-style" content="#1A1A1D">
    <?=_ga(); ?>
</head>
<body>
    <div class="main">
        <div class="main__option" style="z-index: 100;">
            <div class="main__title">panx framework</div><br>
            <div class="main__links">
                <div class="main__label"><a href="https://panx.eu/docs/">Documentation</a></div>
                <div class="main__label"><a href="https://github.com/AlexKratky/panx-framework">Github</a></div>
            </div>
        </div>
    </div>
<!--
    <style>
    canvas {
        display: block;
        vertical-align: bottom;
    }

    #particles-js {
        position: absolute;
        width: 100%;
        height: 100%;
    }
    </style>
    <div id="particles-js"></div>
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
particlesJS("particles-js", {
  "particles": {
    "number": {
      "value": 250, //350
      "density": {
        "enable": true,
        "value_area": 789.1476416322727
      }
    },
    "color": {
      "value": "#ffffff"
    },
    "shape": {
      "type": "circle",
      "stroke": {
        "width": 0,
        "color": "#000000"
      },
      "polygon": {
        "nb_sides": 5
      },
      "image": {
        "src": "img/github.svg",
        "width": 100,
        "height": 100
      }
    },
    "opacity": {
      "value": 0.48927153781200905,
      "random": false,
      "anim": {
        "enable": true,
        "speed": 0.2,
        "opacity_min": 0,
        "sync": false
      }
    },
    "size": {
      "value": 2,
      "random": true,
      "anim": {
        "enable": true,
        "speed": 2,
        "size_min": 0,
        "sync": false
      }
    },
    "line_linked": {
      "enable": false,
      "distance": 150,
      "color": "#ffffff",
      "opacity": 0.4,
      "width": 1
    },
    "move": {
      "enable": true,
      "speed": 0.2,
      "direction": "none",
      "random": true,
      "straight": false,
      "out_mode": "out",
      "bounce": false,
      "attract": {
        "enable": false,
        "rotateX": 600,
        "rotateY": 1200
      }
    }
  },
  "interactivity": {
    "detect_on": "canvas",
    "events": {
      "onhover": {
        "enable": true,
        "mode": "bubble"
      },
      "onclick": {
        "enable": false,
        "mode": "push"
      },
      "resize": false
    },
    "modes": {
      "grab": {
        "distance": 400,
        "line_linked": {
          "opacity": 1
        }
      },
      "bubble": {
        "distance": 83.91608391608392,
        "size": 1,
        "duration": 3,
        "opacity": 1,
        "speed": 3
      },
      "repulse": {
        "distance": 200,
        "duration": 0.4
      },
      "push": {
        "particles_nb": 4
      },
      "remove": {
        "particles_nb": 2
      }
    }
  },
  "retina_detect": true
});
    </script>
    -->
</body>
</html>