'use strict';

function revealOnScroll() {
    const observerOptions = {
        threshold: 0.5 // Trigger when 50% of the element is visible
    };

    const observer = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
        // Add the animation class
        entry.target.classList.add('isVisible');
        // Optional: Stop observing once revealed
        observer.unobserve(entry.target);
        }
    });
    }, observerOptions);

    // Target all elements with the class
    const targets = document.querySelectorAll('.revealOnScroll');
    targets.forEach(target => observer.observe(target));
}

window.addEventListener('load', () => {
    revealOnScroll();
});




/* function setPathLength() {
    const allPaths = document.querySelectorAll('#penaventosaSVG path');

    allPaths.forEach((path) => {
        const pathLength = path.getTotalLength();
        
        // Initial state: hidden
        path.style.strokeDasharray = pathLength;
        path.style.strokeDashoffset = pathLength;

        // Force a redraw/reflow
        path.getBoundingClientRect();

        // Add the animation class
        path.classList.add('svgPathAnimation');
    });
} */