

//Searh Icon
document.addEventListener("DOMContentLoaded", function () {
    // Get the search button and input field elements
    const searchButton = document.getElementById('search-btn');
    const searchInput = document.getElementById('search-input');

    // Add click event listener to the search button
    searchButton.addEventListener('click', function () {
        // Toggle the 'show' class on the search input field
        searchInput.classList.toggle('show');

        // Focus on the input field when it appears
        if (searchInput.classList.contains('show')) {
            searchInput.focus();
        }
    });
});

document.addEventListener("DOMContentLoaded", function() {
    const productsDropdown = document.getElementById("productsDropdown");
    const dropdownMenu = document.getElementById("dropdownMenu");

    productsDropdown.addEventListener("click", function(event) {
        event.preventDefault(); // Prevent default link behavior
        dropdownMenu.classList.toggle("show"); // Toggle the 'show' class
    });

    // Close dropdown if clicking outside
    window.addEventListener("click", function(event) {
        if (!event.target.closest('.dropdown')) {
            dropdownMenu.classList.remove("show"); // Hide dropdown
        }
    });
});

let lastScrollTop = 0; // Keep track of the last scroll position
const navbar = document.querySelector('.navbar');

window.addEventListener('scroll', function() {
    let currentScroll = window.pageYOffset || document.documentElement.scrollTop;

    if (currentScroll > lastScrollTop) {
        // Scrolling down
        navbar.style.top = "-80px"; // Hide the navbar (adjust based on your navbar height)
    } else {
        // Scrolling up
        navbar.style.top = "0";
    }

    lastScrollTop = currentScroll <= 0 ? 0 : currentScroll; // For Mobile or negative scrolling
});
const featuresContainer = document.querySelector('.features-container');
const featureItems = document.querySelectorAll('.feature-item');
const leftArrow = document.querySelector('.left-arrow');
const rightArrow = document.querySelector('.right-arrow');

 let currentIndex = 0;

  function updateCarousel() {
  const itemWidth = featureItems[0].offsetWidth; // Use offsetWidth to account for borders and padding
  featuresContainer.style.transform = `translateX(-${currentIndex * itemWidth}px)`;
} 

 rightArrow.addEventListener('click', () => {
  if (currentIndex < featureItems.length - 1) {
  currentIndex++;
 updateCarousel();
 }
});

leftArrow.addEventListener('click', () => {
if (currentIndex > 0) {
currentIndex--;
updateCarousel();
}
});

