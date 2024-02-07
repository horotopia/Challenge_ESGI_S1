var activeNavItems = document.querySelectorAll('.nav-link');
function handleClick() {
    activeNavItems.forEach(function (item) {
        item.classList.remove('active');
    });
    this.classList.add('active');
}

activeNavItems.forEach(function (item) {
    item.addEventListener('click', handleClick);
});
function toggleDropdown() {
    const dropdown = document.getElementById('myDropdown');
    dropdown.classList.toggle('hidden');
}

window.addEventListener('click', function (event) {
    const dropdown = document.getElementById('myDropdown');
    const button = document.getElementById('dropdownButton');

    if (event.target === button || event.target.closest('#myDropdown')) {
        return;
    }

    dropdown.classList.add('hidden');
});

document.addEventListener('DOMContentLoaded', function () {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach((alert) => {
        alert.classList.remove('hidden');
        setTimeout(() => {
            alert.classList.add('hidden');
        }, 2000);
    });
});
$(document).ready(function () {
    $('.select2').select2({ width: '32rem' });
    $('.select2-container--default .select2-selection--multiple')
        .css('padding-bottom', '0.75rem');
});