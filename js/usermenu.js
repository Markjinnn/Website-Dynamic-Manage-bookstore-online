document.querySelector('.user-name').addEventListener('click', function() {
    var dropdown = document.querySelector('.dropdown');
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
});
window.onclick = function(event) {
    if (!event.target.matches('.user-name')) {
        var dropdowns = document.querySelectorAll('.dropdown');
        dropdowns.forEach(function(dropdown) {
            dropdown.style.display = 'none';
        });
    }
};
