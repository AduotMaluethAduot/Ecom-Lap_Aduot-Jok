<?php
// AJAX filter products
require_once 'product_actions.php';

// Get filter parameters
$category = isset($_GET['category']) ? (int)$_GET['category'] : '';
$brand = isset($_GET['brand']) ? (int)$_GET['brand'] : '';
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Build search parameters
$search_params = [
    'page' => $page,
    'limit' => 12
];

// Add filters if provided
if (!empty($category)) {
    $search_params['category'] = $category;
}
if (!empty($brand)) {
    $search_params['brand'] = $brand;
}
if (!empty($min_price)) {
    $search_params['min_price'] = $min_price;
}
if (!empty($max_price)) {
    $search_params['max_price'] = $max_price;
}

try {
    // Perform search
    $result = handleProductRequest('advanced_search', $search_params);
    
    if ($result['status'] === 'success') {
        echo json_encode([
            'status' => 'success',
            'data' => [
                'products' => $result['data'],
                'pagination' => $result['pagination']
            ]
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to filter products'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Filter failed'
    ]);
}
?>
