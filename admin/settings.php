<?php
// System Settings Page - requires admin privileges
require_once '../src/settings/core.php';

// Require admin access
require_admin();

// Check session validity
if (!is_session_valid()) {
    logout_user();
    header("Location: login/login.php?message=session_expired");
    exit();
}

// Get admin user information
$admin_name = get_user_name();
$admin_email = get_user_email();
$admin_id = get_user_id();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="public/css/admin.css" rel="stylesheet">
</head>
<body>
    <!-- Admin Header -->
    <div class="admin-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="mb-0">
                        <i class="fas fa-cog me-3"></i>
                        System Settings
                    </h1>
                    <p class="mb-0 mt-2 opacity-75">Configure system preferences</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="dashboard.php" class="btn btn-light me-2">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="index.php" class="btn btn-light me-2">
                        <i class="fas fa-home"></i> Back to Site
                    </a>
                    <a href="login/logout.php" class="btn btn-outline-light">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <div class="row">
            <!-- Settings Navigation -->
            <div class="col-md-3">
                <div class="admin-menu">
                    <h4 class="mb-3">
                        <i class="fas fa-cogs text-primary"></i> Settings
                    </h4>
                    <a href="#general" class="menu-item active" data-section="general">
                        <i class="fas fa-sliders-h me-2"></i> General Settings
                    </a>
                    <a href="#email" class="menu-item" data-section="email">
                        <i class="fas fa-envelope me-2"></i> Email Settings
                    </a>
                    <a href="#security" class="menu-item" data-section="security">
                        <i class="fas fa-shield-alt me-2"></i> Security Settings
                    </a>
                    <a href="#payment" class="menu-item" data-section="payment">
                        <i class="fas fa-credit-card me-2"></i> Payment Settings
                    </a>
                    <a href="#backup" class="menu-item" data-section="backup">
                        <i class="fas fa-database me-2"></i> Backup & Restore
                    </a>
                    <a href="#maintenance" class="menu-item" data-section="maintenance">
                        <i class="fas fa-tools me-2"></i> Maintenance
                    </a>
                </div>
            </div>

            <!-- Settings Content -->
            <div class="col-md-9">
                <!-- General Settings -->
                <div id="general-section" class="settings-section">
                    <div class="admin-card">
                        <h4><i class="fas fa-sliders-h me-2"></i>General Settings</h4>
                        <form id="generalSettingsForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="siteName" class="form-label">Site Name</label>
                                        <input type="text" class="form-control" id="siteName" value="Taste of Africa">
                                    </div>
                                    <div class="mb-3">
                                        <label for="siteDescription" class="form-label">Site Description</label>
                                        <textarea class="form-control" id="siteDescription" rows="3">Authentic African Cuisine</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="siteEmail" class="form-label">Contact Email</label>
                                        <input type="email" class="form-control" id="siteEmail" value="info@tasteofafrica.com">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="sitePhone" class="form-label">Contact Phone</label>
                                        <input type="tel" class="form-control" id="sitePhone" value="+1 (555) 123-4567">
                                    </div>
                                    <div class="mb-3">
                                        <label for="siteAddress" class="form-label">Address</label>
                                        <textarea class="form-control" id="siteAddress" rows="3">123 African Street, City, State 12345</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="timezone" class="form-label">Timezone</label>
                                        <select class="form-select" id="timezone">
                                            <option value="UTC">UTC</option>
                                            <option value="America/New_York">Eastern Time</option>
                                            <option value="America/Chicago">Central Time</option>
                                            <option value="America/Denver">Mountain Time</option>
                                            <option value="America/Los_Angeles">Pacific Time</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save General Settings
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Email Settings -->
                <div id="email-section" class="settings-section" style="display: none;">
                    <div class="admin-card">
                        <h4><i class="fas fa-envelope me-2"></i>Email Settings</h4>
                        <form id="emailSettingsForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="smtpHost" class="form-label">SMTP Host</label>
                                        <input type="text" class="form-control" id="smtpHost" value="smtp.gmail.com">
                                    </div>
                                    <div class="mb-3">
                                        <label for="smtpPort" class="form-label">SMTP Port</label>
                                        <input type="number" class="form-control" id="smtpPort" value="587">
                                    </div>
                                    <div class="mb-3">
                                        <label for="smtpUsername" class="form-label">SMTP Username</label>
                                        <input type="text" class="form-control" id="smtpUsername">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="smtpPassword" class="form-label">SMTP Password</label>
                                        <input type="password" class="form-control" id="smtpPassword">
                                    </div>
                                    <div class="mb-3">
                                        <label for="smtpEncryption" class="form-label">Encryption</label>
                                        <select class="form-select" id="smtpEncryption">
                                            <option value="tls">TLS</option>
                                            <option value="ssl">SSL</option>
                                            <option value="none">None</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <button type="button" class="btn btn-info" onclick="testEmail()">
                                            <i class="fas fa-paper-plane"></i> Test Email
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Email Settings
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Security Settings -->
                <div id="security-section" class="settings-section" style="display: none;">
                    <div class="admin-card">
                        <h4><i class="fas fa-shield-alt me-2"></i>Security Settings</h4>
                        <form id="securitySettingsForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="sessionTimeout" class="form-label">Session Timeout (minutes)</label>
                                        <input type="number" class="form-control" id="sessionTimeout" value="60">
                                    </div>
                                    <div class="mb-3">
                                        <label for="maxLoginAttempts" class="form-label">Max Login Attempts</label>
                                        <input type="number" class="form-control" id="maxLoginAttempts" value="5">
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="requireStrongPasswords" checked>
                                            <label class="form-check-label" for="requireStrongPasswords">
                                                Require Strong Passwords
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="enableTwoFactor" checked>
                                            <label class="form-check-label" for="enableTwoFactor">
                                                Enable Two-Factor Authentication
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="logUserActivity" checked>
                                            <label class="form-check-label" for="logUserActivity">
                                                Log User Activity
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <button type="button" class="btn btn-warning" onclick="clearLogs()">
                                            <i class="fas fa-trash"></i> Clear Activity Logs
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Security Settings
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Payment Settings -->
                <div id="payment-section" class="settings-section" style="display: none;">
                    <div class="admin-card">
                        <h4><i class="fas fa-credit-card me-2"></i>Payment Settings</h4>
                        <form id="paymentSettingsForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="currency" class="form-label">Default Currency</label>
                                        <select class="form-select" id="currency">
                                            <option value="USD">USD - US Dollar</option>
                                            <option value="EUR">EUR - Euro</option>
                                            <option value="GBP">GBP - British Pound</option>
                                            <option value="CAD">CAD - Canadian Dollar</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="taxRate" class="form-label">Tax Rate (%)</label>
                                        <input type="number" class="form-control" id="taxRate" value="8.5" step="0.1">
                                    </div>
                                    <div class="mb-3">
                                        <label for="shippingCost" class="form-label">Default Shipping Cost</label>
                                        <input type="number" class="form-control" id="shippingCost" value="5.99" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="enablePayPal" checked>
                                            <label class="form-check-label" for="enablePayPal">
                                                Enable PayPal
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="enableStripe" checked>
                                            <label class="form-check-label" for="enableStripe">
                                                Enable Stripe
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="enableCashOnDelivery">
                                            <label class="form-check-label" for="enableCashOnDelivery">
                                                Enable Cash on Delivery
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Payment Settings
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Backup & Restore -->
                <div id="backup-section" class="settings-section" style="display: none;">
                    <div class="admin-card">
                        <h4><i class="fas fa-database me-2"></i>Backup & Restore</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Database Backup</h5>
                                <p class="text-muted">Create a backup of your database</p>
                                <button class="btn btn-success" onclick="createBackup()">
                                    <i class="fas fa-download"></i> Create Backup
                                </button>
                            </div>
                            <div class="col-md-6">
                                <h5>Database Restore</h5>
                                <p class="text-muted">Restore from a backup file</p>
                                <input type="file" class="form-control mb-2" id="backupFile" accept=".sql">
                                <button class="btn btn-warning" onclick="restoreBackup()">
                                    <i class="fas fa-upload"></i> Restore Backup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Maintenance -->
                <div id="maintenance-section" class="settings-section" style="display: none;">
                    <div class="admin-card">
                        <h4><i class="fas fa-tools me-2"></i>System Maintenance</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Cache Management</h5>
                                <button class="btn btn-info me-2" onclick="clearCache()">
                                    <i class="fas fa-broom"></i> Clear Cache
                                </button>
                                <button class="btn btn-info" onclick="optimizeDatabase()">
                                    <i class="fas fa-database"></i> Optimize Database
                                </button>
                            </div>
                            <div class="col-md-6">
                                <h5>System Status</h5>
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i> System is running normally
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="public/js/settings.js"></script>
</body>
</html>
