<?php
// Use the enhanced session management from core.php
require_once '../src/settings/core.php';

// Use the secure logout function from core.php
logout_user();

// Redirect to login page with a logout message
header('Location: login.php?message=logged_out');
exit();

