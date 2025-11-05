// Registration Page JavaScript
$(document).ready(function() {
    // Handle registration form submission
    $('#register-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            name: $('#name').val(),
            email: $('#email').val(),
            password: $('#password').val(),
            phone_number: $('#phone_number').val(),
            country: $('#country').val(),
            city: $('#city').val(),
            role: $('input[name="role"]:checked').val()
        };
        
        // Basic validation
        if (!formData.name || !formData.email || !formData.password || 
            !formData.phone_number || !formData.country || !formData.city) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please fill in all required fields'
            });
            return;
        }
        
        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(formData.email)) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please enter a valid email address'
            });
            return;
        }
        
        // Password validation
        if (formData.password.length < 6) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Password must be at least 6 characters long'
            });
            return;
        }
        
        // Show loading
        Swal.fire({
            title: 'Creating Account...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Submit form data
        $.ajax({
            url: '../actions/register_user_action.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = 'login.php?message=registration_success';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Registration Failed',
                        text: response.message
                    });
                }
            },
            error: function(xhr, status, error) {
                // Handle non-JSON responses (fallback)
                if (xhr.responseText.includes('registration_success')) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Registration successful! You can now login.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = 'login.php?message=registration_success';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred. Please try again.'
                    });
                }
            }
        });
    });
    
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
});
