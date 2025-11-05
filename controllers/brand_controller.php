<?php

require_once '../classes/brand_class.php';

/**
 * Brand Controller - handles brand operations
 */

/**
 * Get all brands
 * @return array|false - All brands if successful, false otherwise
 */
function get_all_brands_ctr()
{
    $brand = new Brand();
    return $brand->getAllBrands();
}

/**
 * Get brands by category
 * @param int $cat_id - Category ID
 * @return array|false - Brands in category if successful, false otherwise
 */
function get_brands_by_category_ctr($cat_id)
{
    $brand = new Brand();
    return $brand->getBrandsByCategory($cat_id);
}

/**
 * Get brand by ID
 * @param int $brand_id - Brand ID
 * @return array|false - Brand data if found, false otherwise
 */
function get_brand_by_id_ctr($brand_id)
{
    $brand = new Brand();
    return $brand->getBrandById($brand_id);
}

/**
 * Add a new brand
 * @param string $brand_name - Brand name
 * @param int $cat_id - Category ID
 * @return int|false - Brand ID if successful, false otherwise
 */
function add_brand_ctr($brand_name, $cat_id)
{
    // Validate input
    if (empty($brand_name) || empty($cat_id)) {
        return false;
    }
    
    // Trim and sanitize input
    $brand_name = trim($brand_name);
    $cat_id = (int)$cat_id;
    
    // Check minimum length
    if (strlen($brand_name) < 2) {
        return false;
    }
    
    $brand = new Brand();
    return $brand->createBrand($brand_name, $cat_id);
}

/**
 * Update brand
 * @param int $brand_id - Brand ID
 * @param string $brand_name - Updated brand name
 * @param int $cat_id - Updated category ID
 * @return bool - True if successful, false otherwise
 */
function update_brand_ctr($brand_id, $brand_name, $cat_id)
{
    // Validate inputs
    if (empty($brand_id) || empty($brand_name) || empty($cat_id)) {
        return false;
    }
    
    // Trim and sanitize inputs
    $brand_id = (int)$brand_id;
    $brand_name = trim($brand_name);
    $cat_id = (int)$cat_id;
    
    // Check minimum length
    if (strlen($brand_name) < 2) {
        return false;
    }
    
    $brand = new Brand();
    return $brand->updateBrand($brand_id, $brand_name, $cat_id);
}

/**
 * Delete brand
 * @param int $brand_id - Brand ID
 * @return bool - True if successful, false otherwise
 */
function delete_brand_ctr($brand_id)
{
    // Validate input
    if (empty($brand_id)) {
        return false;
    }
    
    $brand_id = (int)$brand_id;
    
    $brand = new Brand();
    return $brand->deleteBrand($brand_id);
}