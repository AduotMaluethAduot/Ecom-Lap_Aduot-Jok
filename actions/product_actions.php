<?php
// Product Actions - Handle all product-related requests
require_once '../controllers/product_controller.php';
require_once '../controllers/category_controller.php';
require_once '../controllers/brand_controller.php';

/**
 * Handle product display requests
 */
function handleProductRequest($action, $params = [])
{
    switch ($action) {
        case 'view_all':
            return handleViewAllProducts($params);
        case 'view_single':
            return handleViewSingleProduct($params);
        case 'search':
            return handleSearchProducts($params);
        case 'filter_category':
            return handleFilterByCategory($params);
        case 'filter_brand':
            return handleFilterByBrand($params);
        case 'advanced_search':
            return handleAdvancedSearch($params);
        default:
            return ['status' => 'error', 'message' => 'Invalid action'];
    }
}

/**
 * Handle view all products request
 */
function handleViewAllProducts($params)
{
    $page = isset($params['page']) ? (int)$params['page'] : 1;
    $limit = isset($params['limit']) ? (int)$params['limit'] : 10;
    $offset = ($page - 1) * $limit;
    
    $products = view_all_products_ctr($limit, $offset);
    $total_count = get_total_products_count_ctr();
    
    if ($products !== false) {
        return [
            'status' => 'success',
            'data' => $products,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($total_count / $limit),
                'total_products' => $total_count,
                'limit' => $limit
            ]
        ];
    } else {
        return ['status' => 'error', 'message' => 'Failed to fetch products'];
    }
}

/**
 * Handle view single product request
 */
function handleViewSingleProduct($params)
{
    $product_id = isset($params['product_id']) ? (int)$params['product_id'] : 0;
    
    if (empty($product_id)) {
        return ['status' => 'error', 'message' => 'Product ID is required'];
    }
    
    $product = view_single_product_ctr($product_id);
    
    if ($product !== false) {
        return [
            'status' => 'success',
            'data' => $product
        ];
    } else {
        return ['status' => 'error', 'message' => 'Product not found'];
    }
}

/**
 * Handle search products request
 */
function handleSearchProducts($params)
{
    $query = isset($params['query']) ? trim($params['query']) : '';
    $page = isset($params['page']) ? (int)$params['page'] : 1;
    $limit = isset($params['limit']) ? (int)$params['limit'] : 10;
    $offset = ($page - 1) * $limit;
    
    if (empty($query)) {
        return ['status' => 'error', 'message' => 'Search query is required'];
    }
    
    $products = search_products_ctr($query, $limit, $offset);
    $filters = ['query' => $query];
    $total_count = get_total_products_count_ctr($filters);
    
    if ($products !== false) {
        return [
            'status' => 'success',
            'data' => $products,
            'query' => $query,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($total_count / $limit),
                'total_products' => $total_count,
                'limit' => $limit
            ]
        ];
    } else {
        return ['status' => 'error', 'message' => 'Search failed'];
    }
}

/**
 * Handle filter by category request
 */
function handleFilterByCategory($params)
{
    $cat_id = isset($params['cat_id']) ? (int)$params['cat_id'] : 0;
    $page = isset($params['page']) ? (int)$params['page'] : 1;
    $limit = isset($params['limit']) ? (int)$params['limit'] : 10;
    $offset = ($page - 1) * $limit;
    
    if (empty($cat_id)) {
        return ['status' => 'error', 'message' => 'Category ID is required'];
    }
    
    $products = filter_products_by_category_ctr($cat_id, $limit, $offset);
    $filters = ['category' => $cat_id];
    $total_count = get_total_products_count_ctr($filters);
    
    if ($products !== false) {
        return [
            'status' => 'success',
            'data' => $products,
            'category_id' => $cat_id,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($total_count / $limit),
                'total_products' => $total_count,
                'limit' => $limit
            ]
        ];
    } else {
        return ['status' => 'error', 'message' => 'Failed to filter by category'];
    }
}

/**
 * Handle filter by brand request
 */
function handleFilterByBrand($params)
{
    $brand_id = isset($params['brand_id']) ? (int)$params['brand_id'] : 0;
    $page = isset($params['page']) ? (int)$params['page'] : 1;
    $limit = isset($params['limit']) ? (int)$params['limit'] : 10;
    $offset = ($page - 1) * $limit;
    
    if (empty($brand_id)) {
        return ['status' => 'error', 'message' => 'Brand ID is required'];
    }
    
    $products = filter_products_by_brand_ctr($brand_id, $limit, $offset);
    $filters = ['brand' => $brand_id];
    $total_count = get_total_products_count_ctr($filters);
    
    if ($products !== false) {
        return [
            'status' => 'success',
            'data' => $products,
            'brand_id' => $brand_id,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($total_count / $limit),
                'total_products' => $total_count,
                'limit' => $limit
            ]
        ];
    } else {
        return ['status' => 'error', 'message' => 'Failed to filter by brand'];
    }
}

/**
 * Handle advanced search request
 */
function handleAdvancedSearch($params)
{
    $filters = [];
    $page = isset($params['page']) ? (int)$params['page'] : 1;
    $limit = isset($params['limit']) ? (int)$params['limit'] : 10;
    $offset = ($page - 1) * $limit;
    
    // Build filters array
    if (!empty($params['query'])) {
        $filters['query'] = trim($params['query']);
    }
    if (!empty($params['category'])) {
        $filters['category'] = (int)$params['category'];
    }
    if (!empty($params['brand'])) {
        $filters['brand'] = (int)$params['brand'];
    }
    if (!empty($params['min_price'])) {
        $filters['min_price'] = (float)$params['min_price'];
    }
    if (!empty($params['max_price'])) {
        $filters['max_price'] = (float)$params['max_price'];
    }
    
    $products = advanced_search_ctr($filters, $limit, $offset);
    $total_count = get_total_products_count_ctr($filters);
    
    if ($products !== false) {
        return [
            'status' => 'success',
            'data' => $products,
            'filters' => $filters,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($total_count / $limit),
                'total_products' => $total_count,
                'limit' => $limit
            ]
        ];
    } else {
        return ['status' => 'error', 'message' => 'Advanced search failed'];
    }
}

/**
 * Get categories and brands for filters
 */
function getFilterOptions()
{
    $categories = get_all_categories_ctr();
    $brands = get_all_brands_ctr();
    
    return [
        'status' => 'success',
        'data' => [
            'categories' => $categories !== false ? $categories : [],
            'brands' => $brands !== false ? $brands : []
        ]
    ];
}
?>
