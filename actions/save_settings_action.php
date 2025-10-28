<?php
// Save settings action
require_once '../src/settings/core.php';

// Check if user is logged in and is admin
if (!is_user_logged_in() || !is_user_admin()) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}

// Get POST data
$settings_type = isset($_POST['settings_type']) ? $_POST['settings_type'] : '';
$settings_data = isset($_POST['settings_data']) ? $_POST['settings_data'] : '';

// Validate input
if (empty($settings_type)) {
    echo json_encode(['status' => 'error', 'message' => 'Settings type is required']);
    exit();
}

if (empty($settings_data)) {
    echo json_encode(['status' => 'error', 'message' => 'Settings data is required']);
    exit();
}

// Validate settings type
$valid_types = ['general', 'email', 'security', 'payment'];
if (!in_array($settings_type, $valid_types)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid settings type']);
    exit();
}

try {
    // For demo purposes, we'll simulate saving settings
    // In a real application, you would save to database or config file
    
    // Parse the settings data
    parse_str($settings_data, $settings_array);
    
    // Validate settings based on type
    $validation_result = validateSettings($settings_type, $settings_array);
    if (!$validation_result['valid']) {
        echo json_encode([
            'status' => 'error',
            'message' => $validation_result['message']
        ]);
        exit();
    }
    
    // Simulate saving (in real app, save to database)
    $success = true;
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => ucfirst($settings_type) . ' settings saved successfully'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to save settings'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to save settings: ' . $e->getMessage()
    ]);
}

// Validate settings function
function validateSettings($type, $settings) {
    switch ($type) {
        case 'general':
            if (empty($settings['siteName'])) {
                return ['valid' => false, 'message' => 'Site name is required'];
            }
            if (empty($settings['siteEmail'])) {
                return ['valid' => false, 'message' => 'Site email is required'];
            }
            if (!filter_var($settings['siteEmail'], FILTER_VALIDATE_EMAIL)) {
                return ['valid' => false, 'message' => 'Invalid email format'];
            }
            break;
            
        case 'email':
            if (empty($settings['smtpHost'])) {
                return ['valid' => false, 'message' => 'SMTP host is required'];
            }
            if (empty($settings['smtpPort'])) {
                return ['valid' => false, 'message' => 'SMTP port is required'];
            }
            if (!is_numeric($settings['smtpPort']) || $settings['smtpPort'] < 1 || $settings['smtpPort'] > 65535) {
                return ['valid' => false, 'message' => 'Invalid SMTP port'];
            }
            break;
            
        case 'security':
            if (empty($settings['sessionTimeout'])) {
                return ['valid' => false, 'message' => 'Session timeout is required'];
            }
            if (!is_numeric($settings['sessionTimeout']) || $settings['sessionTimeout'] < 1) {
                return ['valid' => false, 'message' => 'Invalid session timeout'];
            }
            if (empty($settings['maxLoginAttempts'])) {
                return ['valid' => false, 'message' => 'Max login attempts is required'];
            }
            if (!is_numeric($settings['maxLoginAttempts']) || $settings['maxLoginAttempts'] < 1) {
                return ['valid' => false, 'message' => 'Invalid max login attempts'];
            }
            break;
            
        case 'payment':
            if (empty($settings['taxRate'])) {
                return ['valid' => false, 'message' => 'Tax rate is required'];
            }
            if (!is_numeric($settings['taxRate']) || $settings['taxRate'] < 0 || $settings['taxRate'] > 100) {
                return ['valid' => false, 'message' => 'Invalid tax rate'];
            }
            if (empty($settings['shippingCost'])) {
                return ['valid' => false, 'message' => 'Shipping cost is required'];
            }
            if (!is_numeric($settings['shippingCost']) || $settings['shippingCost'] < 0) {
                return ['valid' => false, 'message' => 'Invalid shipping cost'];
            }
            break;
    }
    
    return ['valid' => true, 'message' => 'Settings are valid'];
}
?>
