document.addEventListener('DOMContentLoaded', function() {
    const wrapper = document.querySelector('.books-wrapper');
    const prevBtn = document.querySelector('.slider-nav.prev');
    const nextBtn = document.querySelector('.slider-nav.next');
    const bookItems = document.querySelectorAll('.book');
    
    let position = 0;
    const itemWidth = 25; // 25% per item
    const maxPosition = -(Math.ceil(bookItems.length / 4) - 1) * 100;
    
    let autoSlideInterval = setInterval(slideNext, 3000);
    
    function slideNext() {
        position = Math.max(position - 100, maxPosition);
        updateSliderPosition();
        if (position <= maxPosition) {
            setTimeout(() => {
                position = 0;
                wrapper.style.transition = 'none';
                updateSliderPosition();
                setTimeout(() => {
                    wrapper.style.transition = 'transform 1s ease';
                }, 1000);
            }, 500);
        }
    }
    
    function slidePrev() {
        position = Math.min(position + 100, 0);
        updateSliderPosition();
    }
    
    function updateSliderPosition() {
        wrapper.style.transform = `translateX(${position}%)`;
    }
    
    prevBtn.addEventListener('click', () => {
        clearInterval(autoSlideInterval);
        slidePrev();
        autoSlideInterval = setInterval(slideNext, 5000);
    });
    
    nextBtn.addEventListener('click', () => {
        clearInterval(autoSlideInterval);
        slideNext();
        autoSlideInterval = setInterval(slideNext, 5000);
    });
    
    wrapper.addEventListener('mouseenter', () => clearInterval(autoSlideInterval));
    wrapper.addEventListener('mouseleave', () => {
        autoSlideInterval = setInterval(slideNext, 5000);
    });
});