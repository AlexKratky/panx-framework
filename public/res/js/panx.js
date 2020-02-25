document.addEventListener('DOMContentLoaded', function () {
    window.addEventListener("scroll", () => {
        if(window.scrollY > 0) {
            document.getElementsByClassName("navbar")[0].style.background = "linear-gradient(30deg, rgba(15,0,69,1) 0%, rgba(0, 85, 244, 1) 100%)";
        } else {
            document.getElementsByClassName("navbar")[0].style.background = "linear-gradient(30deg, rgba(15,0,69,0) 0%, rgba(0, 85, 244, 0) 100%)";
        }
    });
    let y = window.scrollY;
    if (y !== 0) {
        document.getElementsByClassName("navbar")[0].style.background = "linear-gradient(30deg, rgba(15,0,69,1) 0%, rgba(0, 85, 244, 1) 100%)";

    } else {
        document.getElementsByClassName("navbar")[0].style.background = "linear-gradient(30deg, rgba(15,0,69,0) 0%, rgba(0, 85, 244, 0) 100%)";
        console.log(y);
        

    }
}, false);