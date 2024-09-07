require('./bootstrap');
require('./helpers');
require('./vue-components');

$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();
    $('.select2').select2();
});

document.addEventListener('DOMContentLoaded', function() {
    let currentTheme = document.body.getAttribute('data-theme');
    let newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    var navbar = document.querySelector('.navbar');
    if (navbar) {
        navbar.classList.remove('navbar-dark', 'navbar-light');
        navbar.classList.add(currentTheme === 'dark' ? 'navbar-dark' : 'navbar-light');
    }

    const themeSwitcher = document.getElementById('themeSwitcher');
    if(themeSwitcher){
        themeSwitcher.addEventListener('click', function(event) {
            event.preventDefault(); // Prevent the default action of the link
    
            currentTheme = document.body.getAttribute('data-theme');
            let newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
            navbar = document.querySelector('.navbar');
            if (navbar) {
                navbar.classList.remove('navbar-dark', 'navbar-light');
                navbar.classList.add(newTheme === 'dark' ? 'navbar-dark' : 'navbar-light');
            }
    
            // Toggle the data-theme attribute
            document.body.setAttribute('data-theme', newTheme);
    
            // Toggle the theme
            let bodyClassList = document.body.classList;
            bodyClassList.remove('light-theme', 'dark-theme');
            bodyClassList.add(newTheme  === 'dark' ? 'dark-theme' : 'light-theme');
    
            
            // Update the icons
            document.getElementById('lightIcon').classList.toggle('active');
            document.getElementById('lightIcon').classList.toggle('inactive');
            document.getElementById('darkIcon').classList.toggle('active');
            document.getElementById('darkIcon').classList.toggle('inactive');
    
            axios.post('/update-theme', { theme: newTheme })
                .then(response => {
                    if (!response.data.success) {
                        console.error('Failed to update theme on the server');
                    }
                })
                .catch(error => console.error('Error:', error));
        });


        // toggle dropdown or collapsable
        const toggleElements = document.querySelectorAll('[data-toggle="collapse"]');
        toggleElements.forEach(function(element) {
            element.addEventListener('click', function() {
                const arrowIcon = this.querySelector('.fa-chevron-right');
                
                if (arrowIcon) {
                    arrowIcon.classList.toggle('fa-chevron-down');
                    arrowIcon.classList.toggle('fa-chevron-right');
                }
            });
        });
    }


    
});