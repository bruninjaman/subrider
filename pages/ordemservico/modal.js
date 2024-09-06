function showModal() {
    document.getElementById("medicoes1").style.display = "block";
}

function closeModal() {
    document.getElementById("medicoes1").style.display = "none";
}

// Close the modal if the user clicks outside of it
window.onclick = function(event) {
    var modal = document.getElementById("medicoes1");
    if (event.target == modal) {
        modal.style.display = "none";
    }
}