<?php

require_once '../classes/product_class.php';

/**
 * Product Controller - handles product operations
 */

/**
 * Get all products
 * @return array|false - All products if successful, false otherwise
 */
function get_all_products_ctr()
{
    $product = new Product();
    return $product->getAllProducts();
}

/**
 * Get products by category
 * @param int $cat_id - Category ID
 * @return array|false - Products in category if successful, false otherwise
 */
function get_products_by_category_ctr($cat_id)
{
    $product = new Product();
    return $product->getProductsByCategory($cat_id);
}

/**
 * Get products by brand
 * @param int $brand_id - Brand ID
 * @return array|false - Products in brand if successful, false otherwise
 */
function get_products_by_brand_ctr($brand_id)
{
    $product = new Product();
    return $product->getProductsByBrand($brand_id);
}

/**
 * Get product by ID
 * @param int $product_id - Product ID
 * @return array|false - Product data if found, false otherwise
 */
function get_product_by_id_ctr($product_id)
{
    $product = new Product();
    return $product->getProductById($product_id);
}

/**
 * Add a new product
 * @param array $product_data - Product data array
 * @return int|false - Product ID if successful, false otherwise
 */
function add_product_ctr($product_data)
{
    // Validate input
    if (empty($product_data['product_title']) || empty($product_data['product_price']) || 
        empty($product_data['product_cat']) || empty($product_data['product_brand'])) {
        return false;
    }
    
    // Trim and sanitize input
    $product_data['product_title'] = trim($product_data['product_title']);
    $product_data['product_desc'] = trim($product_data['product_desc'] ?? '');
    $product_data['product_keywords'] = trim($product_data['product_keywords'] ?? '');
    $product_data['product_image'] = trim($product_data['product_image'] ?? '');
    
    // Validate price
    $product_data['product_price'] = (float)$product_data['product_price'];
    if ($product_data['product_price'] <= 0) {
        return false;
    }
    
    // Check minimum length
    if (strlen($product_data['product_title']) < 2) {
        return false;
    }
    
    $product = new Product();
    return $product->createProduct($product_data);
}

/**
 * Update product
 * @param int $product_id - Product ID
 * @param array $product_data - Updated product data
 * @return bool - True if successful, false otherwise
 */
function update_product_ctr($product_id, $product_data)
{
    // Validate inputs
    if (empty($product_id) || empty($product_data['product_title']) || empty($product_data['product_price']) || 
        empty($product_data['product_cat']) || empty($product_data['product_brand'])) {
        return false;
    }
    
    // Trim and sanitize inputs
    $product_id = (int)$product_id;
    $product_data['product_title'] = trim($product_data['product_title']);
    $product_data['product_desc'] = trim($product_data['product_desc'] ?? '');
    $product_data['product_keywords'] = trim($product_data['product_keywords'] ?? '');
    $product_data['product_image'] = trim($product_data['product_image'] ?? '');
    
    // Validate price
    $product_data['product_price'] = (float)$product_data['product_price'];
    if ($product_data['product_price'] <= 0) {
        return false;
    }
    
    // Check minimum length
    if (strlen($product_data['product_title']) < 2) {
        return false;
    }
    
    $product = new Product();
    return $product->updateProduct($product_id, $product_data);
}

/**
 * Delete product
 * @param int $product_id - Product ID
 * @return bool - True if successful, false otherwise
 */
function delete_product_ctr($product_id)
{
    // Validate input
    if (empty($product_id)) {
        return false;
    }
    
    $product_id = (int)$product_id;
    
    $product = new Product();
    return $product->deleteProduct($product_id);
}

/**
 * View all products with pagination
 * @param int $limit - Number of products per page
 * @param int $offset - Offset for pagination
 * @return array|false - Array of products if successful, false otherwise
 */
function view_all_products_ctr($limit = 10, $offset = 0)
{
    $product = new Product();
    return $product->viewAllProducts($limit, $offset);
}

/**
 * Search products by query
 * @param string $query - Search query
 * @param int $limit - Number of results per page
 * @param int $offset - Offset for pagination
 * @return array|false - Array of products if successful, false otherwise
 */
function search_products_ctr($query, $limit = 10, $offset = 0)
{
    $product = new Product();
    return $product->searchProducts($query, $limit, $offset);
}

/**
 * Filter products by category
 * @param int $cat_id - Category ID
 * @param int $limit - Number of results per page
 * @param int $offset - Offset for pagination
 * @return array|false - Array of products if successful, false otherwise
 */
function filter_products_by_category_ctr($cat_id, $limit = 10, $offset = 0)
{
    $product = new Product();
    return $product->filterProductsByCategory($cat_id, $limit, $offset);
}

/**
 * Filter products by brand
 * @param int $brand_id - Brand ID
 * @param int $limit - Number of results per page
 * @param int $offset - Offset for pagination
 * @return array|false - Array of products if successful, false otherwise
 */
function filter_products_by_brand_ctr($brand_id, $limit = 10, $offset = 0)
{
    $product = new Product();
    return $product->filterProductsByBrand($brand_id, $limit, $offset);
}

/**
 * View single product with full details
 * @param int $product_id - Product ID
 * @return array|false - Product data if successful, false otherwise
 */
function view_single_product_ctr($product_id)
{
    $product = new Product();
    return $product->viewSingleProduct($product_id);
}

/**
 * Advanced search with multiple filters
 * @param array $filters - Array of filters
 * @param int $limit - Number of results per page
 * @param int $offset - Offset for pagination
 * @return array|false - Array of products if successful, false otherwise
 */
function advanced_search_ctr($filters, $limit = 10, $offset = 0)
{
    $product = new Product();
    return $product->advancedSearch($filters, $limit, $offset);
}

/**
 * Get total count of products for pagination
 * @param array $filters - Optional filters for counting
 * @return int - Total count of products
 */
function get_total_products_count_ctr($filters = [])
{
    $product = new Product();
    return $product->getTotalProductsCount($filters);
}