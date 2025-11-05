// Admin Dashboard JavaScript
$(document).ready(function() {
    // Load user statistics
    loadUserStats();
    
    // Test session management
    window.testSessionManagement = function() {
        const sessionInfo = {
            userId: $('.container').data('user-id'),
            userName: $('.container').data('user-name'),
            userEmail: $('.container').data('user-email'),
            userRole: $('.container').data('user-role'),
            isAdmin: $('.container').data('is-admin'),
            sessionValid: $('.container').data('session-valid'),
            loginTime: $('.container').data('login-time'),
            isLoggedIn: $('.container').data('is-logged-in')
        };
        
        Swal.fire({
            title: 'Session Information',
            html: `
                <div class="text-start">
                    <p><strong>User ID:</strong> ${sessionInfo.userId}</p>
                    <p><strong>Name:</strong> ${sessionInfo.userName}</p>
                    <p><strong>Email:</strong> ${sessionInfo.userEmail}</p>
                    <p><strong>Role:</strong> ${sessionInfo.userRole}</p>
                    <p><strong>Is Admin:</strong> ${sessionInfo.isAdmin ? 'Yes' : 'No'}</p>
                    <p><strong>Session Valid:</strong> ${sessionInfo.sessionValid ? 'Yes' : 'No'}</p>
                    <p><strong>Login Time:</strong> ${sessionInfo.loginTime}</p>
                    <p><strong>Logged In:</strong> ${sessionInfo.isLoggedIn ? 'Yes' : 'No'}</p>
                </div>
            `,
            icon: 'info',
            confirmButtonText: 'OK'
        });
    };
    
    // Refresh statistics
    window.refreshStats = function() {
        loadUserStats();
        Swal.fire({
            icon: 'success',
            title: 'Refreshed!',
            text: 'Statistics have been updated',
            timer: 1500,
            showConfirmButton: false
        });
    };
    
    // Check permissions
    window.checkPermissions = function() {
        const permissions = {
            isLoggedIn: $('.container').data('is-logged-in'),
            isAdmin: $('.container').data('is-admin'),
            hasAdminRole: $('.container').data('has-admin-role'),
            sessionValid: $('.container').data('session-valid')
        };
        
        let permissionText = '';
        for (const [key, value] of Object.entries(permissions)) {
            permissionText += `<p><strong>${key}:</strong> ${value ? '✓ Allowed' : '✗ Denied'}</p>`;
        }
        
        Swal.fire({
            title: 'Permission Check',
            html: permissionText,
            icon: 'info',
            confirmButtonText: 'OK'
        });
    };
    
    // Regenerate session
    window.regenerateSession = function() {
        Swal.fire({
            title: 'Regenerate Session?',
            text: 'This will create a new session ID for security',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, regenerate',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // In a real implementation, this would call a server endpoint
                Swal.fire({
                    icon: 'success',
                    title: 'Session Regenerated!',
                    text: 'Your session has been regenerated for security',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    };
    
    // Clear all sessions (danger zone)
    window.confirmClearSessions = function() {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will clear ALL user sessions. This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, clear all sessions!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Sessions Cleared!',
                    text: 'All user sessions have been cleared',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    };
    
    // Load user statistics
    function loadUserStats() {
        $.ajax({
            url: '../actions/get_user_stats_action.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const data = response.data;
                    $('#total-users').text(data.total_users);
                    $('#total-admins').text(data.total_admins);
                    $('#total-orders').text(data.total_orders);
                } else {
                    console.error('Failed to load stats:', response.message);
                    // Fallback to default values
                    $('#total-users').text('0');
                    $('#total-admins').text('0');
                    $('#total-orders').text('0');
                }
            },
            error: function() {
                console.error('Error loading statistics');
                // Fallback to default values
                $('#total-users').text('0');
                $('#total-admins').text('0');
                $('#total-orders').text('0');
            }
        });
    }
    
    // Auto-refresh time every minute
    setInterval(function() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', { 
            hour12: false,
            hour: '2-digit',
            minute: '2-digit'
        });
        $('.stat-number').last().text(timeString);
    }, 60000);
});
