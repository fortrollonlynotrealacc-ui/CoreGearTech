function logout() {
    // Send an AJAX request to the server to destroy the session
    fetch('logout.php', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.message === 'Logged out successfully') {
            // Redirect the user to the home page or login page after logout
            window.location.href = 'website.php';
        } else {
            alert('Logout failed: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while logging out.');
    });
}
