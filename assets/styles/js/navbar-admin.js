document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('navbarAdminToggle');
    let isNavbarOpen = false;

    toggleBtn.addEventListener('click', () => {
        const navbar = document.getElementById('navbarAdmin');
        const navLinks = document.getElementsByClassName('nav-link');
        const mainContent = document.getElementById('mainContent');
        const ellipseOne = document.getElementById('ellipse1');
        const ellipseThree = document.getElementById('ellipse3');
        const ellipseFive = document.getElementById('ellipse5');
        const ellipseEight = document.getElementById('ellipse8');
        const burgerMenu = document.getElementById('burger-menu');

        if (isNavbarOpen) {
            navbar.style.width = '4rem';
            mainContent.style.marginLeft = '4rem';
            mainContent.style.transition = "all .2s";
            burgerMenu.style.rotate = '0deg';
            burgerMenu.style.transition = "all .2s";
            ellipseOne.style.opacity = "1";
            ellipseOne.style.transition = "all .2s";
            ellipseThree.style.opacity = "1";
            ellipseThree.style.transition = "all .2s";
            ellipseFive.style.opacity = "1";
            ellipseFive.style.transition = "all .2s";
            ellipseEight.style.opacity = "1";
            ellipseEight.style.transition = "all .2s";
            for(let i = 0; i < navLinks.length; i++) {
                const link = navLinks[i];
                link.style.justifyContent = 'center';
                link.style.transition = "all .2s";
            }
            navbar.classList.remove('nav-open');
            navbar.classList.add('nav-closed');
            navbar.style.transition = "all .2s";
        } else {
            navbar.style.width = '18rem';
            mainContent.style.marginLeft = '18rem';
            mainContent.style.transition = "all .2s";
            burgerMenu.style.rotate = '90deg';
            burgerMenu.style.transition = "all .2s";
            ellipseOne.style.opacity = "0";
            ellipseOne.style.transition = "all .2s";
            ellipseThree.style.opacity = "0";
            ellipseThree.style.transition = "all .2s";
            ellipseFive.style.opacity = "0";
            ellipseFive.style.transition = "all .2s";
            ellipseEight.style.opacity = "0";
            ellipseEight.style.transition = "all .2s";
            for(let i = 0; i < navLinks.length; i++) {
                const link = navLinks[i];
                link.style.justifyContent = 'flex-start';
                link.style.transition = "all .2s";
            }
            navbar.classList.remove('nav-closed');
            navbar.classList.add('nav-open');
            navbar.style.transition = "all .2s";
        }

        isNavbarOpen = !isNavbarOpen;
    });
});


