document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh dashboard every 30 seconds
    if (window.location.pathname.includes('index.php') || window.location.pathname === '/') {
        setInterval(function() {
            location.reload();
        }, 30000);
    }

    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#e74c3c';
                } else {
                    field.style.borderColor = '#ddd';
                }
            });

            if (!isValid) {
                e.preventDefault();
                showAlert('Please fill in all required fields.', 'error');
            }
        });
    });

    // Status update functionality
    const statusSelects = document.querySelectorAll('.status-select');
    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            const ticketId = this.dataset.ticketId;
            const newStatus = this.value;
            
            updateTicketStatus(ticketId, newStatus);
        });
    });

    // Priority color coding
    const priorityElements = document.querySelectorAll('[data-priority]');
    priorityElements.forEach(element => {
        const priority = element.dataset.priority;
        element.classList.add(`priority-${priority}`);
    });

    // Search functionality
    const searchInput = document.getElementById('ticket-search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const tickets = document.querySelectorAll('.ticket-item');
            
            tickets.forEach(ticket => {
                const title = ticket.querySelector('h4').textContent.toLowerCase();
                const description = ticket.querySelector('p').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || description.includes(searchTerm)) {
                    ticket.style.display = 'block';
                } else {
                    ticket.style.display = 'none';
                }
            });
        });
    }

    // Modal functionality
    const modals = document.querySelectorAll('.modal');
    const modalTriggers = document.querySelectorAll('[data-modal]');
    const modalCloses = document.querySelectorAll('.modal-close');

    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const modalId = this.dataset.modal;
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'flex';
            }
        });
    });

    modalCloses.forEach(close => {
        close.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                modal.style.display = 'none';
            }
        });
    });

    // Close modal when clicking outside
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
            }
        });
    });
});

function updateTicketStatus(ticketId, status) {
    fetch('ajax/update_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            ticket_id: ticketId,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Ticket status updated successfully!', 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showAlert('Error updating ticket status.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error updating ticket status.', 'error');
    });
}

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    
    const container = document.querySelector('.container');
    container.insertBefore(alertDiv, container.firstChild);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

function confirmAction(message) {
    return confirm(message);
}

// Real-time notifications (if WebSocket is implemented)
function initializeNotifications() {
    if ('Notification' in window) {
        Notification.requestPermission();
    }
}

function showNotification(title, body) {
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification(title, {
            body: body,
            icon: '/assets/images/icon.png'
        });
    }
}

// User Management Functions
function editUser(userId) {
    // This would typically open a modal or redirect to edit page
    const newRole = prompt('Enter new role (user, technician, admin):');
    if (newRole && ['user', 'technician', 'admin'].includes(newRole)) {
        updateUserRole(userId, newRole);
    }
}

function deleteUser(userId) {
    if (confirmAction('Are you sure you want to delete this user? This action cannot be undone.')) {
        // AJAX call to delete user
        fetch('ajax/delete_user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                user_id: userId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('User deleted successfully!', 'success');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showAlert('Error deleting user.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error deleting user.', 'error');
        });
    }
}

function updateUserRole(userId, role) {
    fetch('ajax/update_user_role.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            user_id: userId,
            role: role
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('User role updated successfully!', 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showAlert('Error updating user role.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error updating user role.', 'error');
    });
}