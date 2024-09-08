require('./bootstrap');
require('./helpers');
require('./vue-components');

$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();
    $('.select2').select2();
});

function switchTheme(theme) {
    const lightIcon = document.getElementById('lightIcon');
    const darkIcon = document.getElementById('darkIcon');
    const navbar = document.querySelector('.navbar');
    const lightThemeStyleSheet = document.getElementById('light-theme');
    const darkThemeeStyleSheet = document.getElementById('dark-theme');

    // Toggle the class on the navbar
    if (navbar) {
        navbar.classList.remove('navbar-dark', 'navbar-light');
        navbar.classList.add(theme === 'dark' ? 'navbar-dark' : 'navbar-light');
    }

    // Toggle the theme classes on the body
    document.body.classList.remove('light-theme', 'dark-theme');
    document.body.classList.add(theme === 'dark' ? 'dark-theme' : 'light-theme');

    // Enable/Disable the appropriate stylesheets
    lightThemeStyleSheet.disabled = theme === 'dark';
    darkThemeeStyleSheet.disabled = theme === 'light';


    if (theme === 'light') {
        // Show light icon, hide dark icon
        lightIcon.classList.add('active');
        lightIcon.classList.remove('inactive');
        darkIcon.classList.add('inactive');
        darkIcon.classList.remove('active');
    } else {
        // Show dark icon, hide light icon
        darkIcon.classList.add('active');
        darkIcon.classList.remove('inactive');
        lightIcon.classList.add('inactive');
        lightIcon.classList.remove('active');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    let currentTheme = document.body.getAttribute('data-theme');
    switchTheme(currentTheme);

    const themeSwitcher = document.getElementById('themeSwitcher');
    if(themeSwitcher){
        themeSwitcher.addEventListener('click', function(event) {
            event.preventDefault();
        
            currentTheme = document.body.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            switchTheme(newTheme);
            document.body.setAttribute('data-theme', newTheme);

            axios.post('/update-theme', { theme: newTheme })
                .then(response => {
                    if (!response.data.success) {
                        console.error('Failed to update theme on the server');
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    }

    // Toggle dropdown, toggle down and right icon
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    dropdownToggles.forEach(function (dropdownToggle) {
        if (dropdownToggle.classList.contains('active')) {
            dropdownToggle.classList.add('right');
        } else {
            dropdownToggle.classList.remove('right');
            dropdownToggle.classList.add('collapsed');
        }
    
        dropdownToggle.addEventListener('click', function () {
            if (dropdownToggle.classList.contains('collapsed')) {
                
                dropdownToggle.classList.add('right');
            } else {
                dropdownToggle.classList.remove('right');
            }
        });
    });




});