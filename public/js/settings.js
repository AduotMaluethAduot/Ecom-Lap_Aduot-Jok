// System Settings JavaScript
$(document).ready(function() {
    // Handle settings navigation
    $('.menu-item').on('click', function(e) {
        e.preventDefault();
        
        // Remove active class from all menu items
        $('.menu-item').removeClass('active');
        
        // Add active class to clicked item
        $(this).addClass('active');
        
        // Hide all sections
        $('.settings-section').hide();
        
        // Show selected section
        const section = $(this).data('section');
        $('#' + section + '-section').show();
    });
    
    // Handle form submissions
    $('#generalSettingsForm').on('submit', function(e) {
        e.preventDefault();
        saveSettings('general', $(this).serialize());
    });
    
    $('#emailSettingsForm').on('submit', function(e) {
        e.preventDefault();
        saveSettings('email', $(this).serialize());
    });
    
    $('#securitySettingsForm').on('submit', function(e) {
        e.preventDefault();
        saveSettings('security', $(this).serialize());
    });
    
    $('#paymentSettingsForm').on('submit', function(e) {
        e.preventDefault();
        saveSettings('payment', $(this).serialize());
    });
    
    // Save settings function
    function saveSettings(type, data) {
        Swal.fire({
            title: 'Saving Settings...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: '../actions/save_settings_action.php',
            type: 'POST',
            data: {
                settings_type: type,
                settings_data: data
            },
            dataType: 'json',
            success: function(response) {
                Swal.close();
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        text: 'Settings have been saved successfully',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to save settings'
                });
            }
        });
    }
    
    // Test email function
    window.testEmail = function() {
        const smtpHost = $('#smtpHost').val();
        const smtpPort = $('#smtpPort').val();
        const smtpUsername = $('#smtpUsername').val();
        const smtpPassword = $('#smtpPassword').val();
        
        if (!smtpHost || !smtpPort || !smtpUsername || !smtpPassword) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Information',
                text: 'Please fill in all SMTP settings before testing'
            });
            return;
        }
        
        Swal.fire({
            title: 'Testing Email...',
            text: 'Sending test email',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: '../actions/test_email_action.php',
            type: 'POST',
            data: {
                smtp_host: smtpHost,
                smtp_port: smtpPort,
                smtp_username: smtpUsername,
                smtp_password: smtpPassword
            },
            dataType: 'json',
            success: function(response) {
                Swal.close();
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Email Sent!',
                        text: 'Test email has been sent successfully'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Email Failed',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to send test email'
                });
            }
        });
    };
    
    // Clear logs function
    window.clearLogs = function() {
        Swal.fire({
            title: 'Clear Activity Logs?',
            text: 'This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, clear logs!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../actions/clear_logs_action.php',
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Cleared!',
                                text: 'Activity logs have been cleared',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to clear logs'
                        });
                    }
                });
            }
        });
    };
    
    // Create backup function
    window.createBackup = function() {
        Swal.fire({
            title: 'Creating Backup...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: '../actions/create_backup_action.php',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                Swal.close();
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Backup Created!',
                        text: 'Database backup has been created successfully',
                        confirmButtonText: 'Download',
                        showCancelButton: true,
                        cancelButtonText: 'Close'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.open(response.download_url);
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Backup Failed',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to create backup'
                });
            }
        });
    };
    
    // Restore backup function
    window.restoreBackup = function() {
        const fileInput = document.getElementById('backupFile');
        const file = fileInput.files[0];
        
        if (!file) {
            Swal.fire({
                icon: 'warning',
                title: 'No File Selected',
                text: 'Please select a backup file to restore'
            });
            return;
        }
        
        Swal.fire({
            title: 'Restore Backup?',
            text: 'This will replace all current data with the backup data!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, restore!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('backup_file', file);
                
                Swal.fire({
                    title: 'Restoring Backup...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                $.ajax({
                    url: '../actions/restore_backup_action.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        Swal.close();
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Restored!',
                                text: 'Database has been restored successfully'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Restore Failed',
                                text: response.message
                            });
                        }
                    },
                    error: function() {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to restore backup'
                        });
                    }
                });
            }
        });
    };
    
    // Clear cache function
    window.clearCache = function() {
        Swal.fire({
            title: 'Clear Cache?',
            text: 'This will clear all cached data',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, clear cache!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../actions/clear_cache_action.php',
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Cache Cleared!',
                                text: 'All cached data has been cleared',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to clear cache'
                        });
                    }
                });
            }
        });
    };
    
    // Optimize database function
    window.optimizeDatabase = function() {
        Swal.fire({
            title: 'Optimize Database?',
            text: 'This will optimize database tables for better performance',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, optimize!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Optimizing Database...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                $.ajax({
                    url: '../actions/optimize_database_action.php',
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        Swal.close();
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Optimized!',
                                text: 'Database has been optimized successfully',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function() {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to optimize database'
                        });
                    }
                });
            }
        });
    };
});
