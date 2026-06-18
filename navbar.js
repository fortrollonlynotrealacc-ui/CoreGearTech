function showLogoutModal() {
    const modal = document.getElementById("logout-modal");
    modal.style.display = "block";
}
function closeModal() {
    const modal = document.getElementById("logout-modal");
    modal.style.display = "none";
}
function logout() {
    window.location.href = "Website.php"; // Redirect to logout page
}


        