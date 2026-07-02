document.addEventListener('DOMContentLoaded', function() {

    const rotationBox = document.querySelector('.icon-rotation-box');
    const icons = rotationBox ? rotationBox.querySelectorAll('i') : [];
    let currentIndex = 0;

    if (icons.length > 0) {
        function rotateIcons() {
            icons.forEach(icon => icon.style.opacity = '0');
            
            icons[currentIndex].style.opacity = '1';
            
            currentIndex = (currentIndex + 1) % icons.length;
        }

        rotateIcons();

        setInterval(rotateIcons, 3000); 
    }
    
    const toggleBtn = document.querySelector('.contact-toggle-btn');
    if (toggleBtn) {
        toggleBtn.classList.add('pulse-active');
    }

});