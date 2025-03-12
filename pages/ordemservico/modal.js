function showModal() {
    document.getElementById("modal").style.display = "flex";
}

function closeModal() {
    document.getElementById("modal").style.display = "none";
}

// Close the modal if the user clicks outside of it
window.onclick = function(event) {
    var modal = document.getElementById("modal");
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

// Get elements
const openModalButton = document.getElementById('openModal');
const modal = document.getElementById('modal');
const closeModalButtons = document.querySelectorAll('[id^="closeModal"]');
const pages = document.querySelectorAll('.modal-page');
let currentPage = 0;

// Event Listeners for opening and closing the modal
openModalButton.addEventListener('click', () => {
    modal.style.display = 'flex';
    currentPage = 0;  // Reset to the first page when opening
    showPage(currentPage);
});

closeModalButtons.forEach(button => {
    button.addEventListener('click', () => {
        modal.style.display = 'none';
        currentPage = 0;  // Reset the modal to the first page after closing
    });
});

// Event listeners for navigation
document.getElementById('btnCabecote').addEventListener('click', () => showSpecificPage('cabecote'));
document.getElementById('btnMotor').addEventListener('click', () => showSpecificPage('motorPage'));
document.getElementById('btnVirabrequim').addEventListener('click', () => showSpecificPage('virabrequimPage'));
document.getElementById('btnEmbreagem').addEventListener('click', () => showSpecificPage('embreagemPage'));
document.getElementById('btnBombas').addEventListener('click', () => showSpecificPage('bombasPage'));

// Back to main menu button
document.querySelectorAll('#backToMenu').forEach(button => {
    button.addEventListener('click', (event) => {
        event.preventDefault(); // Prevent default link behavior
        showPage(0); // Show the main menu page (assuming page 0 is the main menu)
    });
});

// Show a specific page based on its ID
function showSpecificPage(pageId) {
    pages.forEach(page => page.style.display = 'none');  // Hide all pages
    document.getElementById(pageId).style.display = 'block';  // Show the specific page
}

// Show the current page
function showPage(pageIndex) {
    pages.forEach((page, index) => {
        page.style.display = index === pageIndex ? 'block' : 'none';
    });
}

// Change page by incrementing or decrementing currentPage
function changePage(step) {
    currentPage += step;
    if (currentPage < 0) currentPage = 0;
    if (currentPage >= pages.length) currentPage = pages.length - 1;
    showPage(currentPage);
}