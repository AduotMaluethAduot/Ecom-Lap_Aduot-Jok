// Login Page JavaScript
$(document).ready(function() {
    // Handle login form submission
    $('#login-form').on('submit', function(e) {
        e.preventDefault();
        
        const email = $('#email').val();
        const password = $('#password').val();
        
        if (!email || !password) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please fill in all fields'
            });
            return;
        }
        
        // Show loading
        Swal.fire({
            title: 'Logging in...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Submit form data
        $.ajax({
            url: '../actions/login_customer_action.php',
            type: 'POST',
            data: {
                email: email,
                password: password
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Login successful',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        // Redirect based on user role
                        if (response.user_data.is_admin) {
                            window.location.href = '../admin/dashboard.php';
                        } else if (response.user_data.role === 'owner' || response.user_data.role === 'restaurant_owner') {
                            window.location.href = '../customer/owner_dashboard.php';
                        } else {
                            // Check if there's a redirect URL stored in session
                            const urlParams = new URLSearchParams(window.location.search);
                            const redirectUrl = urlParams.get('redirect');
                            if (redirectUrl) {
                                window.location.href = decodeURIComponent(redirectUrl);
                            } else {
                                window.location.href = '../customer/customer_dashboard.php';
                            }
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Failed',
                        text: response.message || 'Invalid credentials'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again.'
                });
            }
        });
    });
    
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
});