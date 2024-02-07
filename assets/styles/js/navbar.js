let dropdowns = document.getElementsByClassName("navbar-dropdown");
let menus = document.getElementsByClassName("navbar-dropdown-menu");
let navbarToggles = document.getElementsByClassName("navbar-dropdown-toggle");

for (let i = 0; i < dropdowns.length; i++) {
    if (innerWidth > 768) {
        navbarToggles[i].addEventListener("mouseover", () => {
            for (let j = 0; j < dropdowns.length; j++) {
                dropdowns[j].classList.remove("open");
            }
            dropdowns[i].classList.add("open");
        });
        menus[i].addEventListener("mouseleave", () => {
            dropdowns[i].classList.remove("open");
        })
    } else {
        navbarToggles[i].addEventListener("click", () => {
            for (let j = 0; j < dropdowns.length; j++) {
                if (i !== j) {
                    dropdowns[j].classList.remove("open");
                }
            }
            dropdowns[i].classList.toggle("open");
        });
    }
}

const contentWrap = document.getElementById("jumbotron")
const navbar = document.getElementById("navbar");
const navbarNav = document.getElementById("navbar-nav");
const navbarRight = document.getElementById("navbar-right");
let navHeight = navbar.getBoundingClientRect().height;

document.addEventListener('DOMContentLoaded', (event) => {
    contentWrap.style.marginTop = navHeight + "px";
});

if (innerWidth < 768) {
    let toggle = document.getElementById("navbar-toggle");
    let initialHeight = navbar.getBoundingClientRect().height;
    let open = false;
    let initialTransition = window.getComputedStyle(navbar).transition;

    navbar.style.transition = "none";
    navbar.style.height = "65px";
    navbarNav.style.display = "none";
    navbarRight.style.display = "none";

    document.addEventListener('DOMContentLoaded', (event) => {
        contentWrap.style.marginTop = "65px";
    });

    requestAnimationFrame(() => {
        navbar.style.transition = initialTransition;
    })

    toggle.addEventListener("click", () => {
        open = !open;

        if (open) {
            navbar.style.height = initialHeight + "px";
            contentWrap.style.marginTop = initialHeight + "px";
            navbarNav.style.display = "flex";
            navbarRight.style.display = "flex";
        } else {
            navbar.style.height = "65px";
            contentWrap.style.marginTop = "65px";
            navbarNav.style.display = "none";
            navbarRight.style.display = "none";
        }
    })
}
