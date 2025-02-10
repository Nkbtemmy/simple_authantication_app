function showAddUserForm() {
    window.location.href = 'add_user.php';
}

function editUser(userId) {
    window.location.href = `edit_user.php?id=${userId}`;
}

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        fetch(`delete_user.php?id=${userId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error deleting user');
            }
        })
        .catch(error => {
            alert('Error deleting user');
        });
    }
} 