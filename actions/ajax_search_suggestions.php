<?php
// AJAX search suggestions
require_once 'product_actions.php';

// Get search query
$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($query) || strlen($query) < 2) {
    echo json_encode(['status' => 'error', 'message' => 'Query too short']);
    exit();
}

try {
    // Get search suggestions
    $result = handleProductRequest('search', [
        'query' => $query,
        'limit' => 5
    ]);
    
    if ($result['status'] === 'success') {
        $suggestions = [];
        foreach ($result['data'] as $product) {
            $suggestions[] = [
                'title' => $product['product_title'],
                'category' => $product['cat_name'],
                'brand' => $product['brand_name'],
                'id' => $product['product_id']
            ];
        }
        
        echo json_encode([
            'status' => 'success',
            'data' => $suggestions
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No suggestions found'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Search failed'
    ]);
}
?>
