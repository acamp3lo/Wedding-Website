'use strict';

function dismissMessages() {
    const messages = document.querySelectorAll('#messagesContainer > article');
    messages.forEach(message => {
        message.classList.add('opacity90');
        message.addEventListener('click', function() {
            message.remove();
        });
    });
}

function toggleNav() {
    const navToggle = document.querySelector('.nav-toggle');
    const navLinks = document.querySelector('#mainNav > ul');

    navToggle.addEventListener('click', () => {
        // Toggle the .active class on click
        navToggle.classList.toggle('active');
        navLinks.classList.toggle('active');
    });

    document.addEventListener('click', (event) => {
        // Check if the menu is currently open
        const isMenuOpen = navLinks.classList.contains('active');
        
        // Check if the click happened outside both the links drawer and the toggle button
        const clickedOutsideMenu = !navLinks.contains(event.target);
        const clickedOutsideToggle = !navToggle.contains(event.target);

        if (isMenuOpen && clickedOutsideMenu && clickedOutsideToggle) {
            navToggle.classList.remove('active');
            navLinks.classList.remove('active');
        }
    });
    
    // Close the menu if a user clicks a link
    document.querySelectorAll('#mainNav > ul a').forEach(link => {
        link.addEventListener('click', () => {
            navToggle.classList.remove('active');
            navLinks.classList.remove('active');
        });
    });
}

window.addEventListener('load', () => {
    dismissMessages();
    toggleNav();
});