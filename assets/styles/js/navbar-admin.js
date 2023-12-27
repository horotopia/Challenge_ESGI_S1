document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('navbarAdminToggle');
    let isNavbarOpen = false;

    toggleBtn.addEventListener('click', () => {
        const navbar = document.getElementById('navbarAdmin');
        const navbarIcons = document.getElementsByClassName('nav-icon');

        if (isNavbarOpen) {
            navbar.style.width = '6rem';
            for (let i = 0; i < navbarIcons.length; i++) {
                const icon = navbarIcons[i];
                icon.style.width = '100%';
            }
            navbar.classList.remove('nav-open');
            navbar.classList.add('nav-closed');
        } else {
            navbar.style.width = '18rem';
            for (let i = 0; i < navbarIcons.length; i++) {
                const icon = navbarIcons[i];
                icon.style.width = '47px';
            }
            navbar.classList.remove('nav-closed');
            navbar.classList.add('nav-open');
        }

        isNavbarOpen = !isNavbarOpen;
    });
});