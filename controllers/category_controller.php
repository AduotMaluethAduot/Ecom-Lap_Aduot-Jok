<?php

require_once '../classes/category_class.php';

/**
 * Category Controller - handles category operations
 */

/**
 * Get all categories
 * @return array|false - All categories if successful, false otherwise
 */
function get_all_categories_ctr()
{
    $category = new Category();
    return $category->getAllCategories();
}

/**
 * Get category by ID
 * @param int $cat_id - Category ID
 * @return array|false - Category data if found, false otherwise
 */
function get_category_by_id_ctr($cat_id)
{
    $category = new Category();
    return $category->getCategoryById($cat_id);
}

/**
 * Add a new category
 * @param string $cat_name - Category name
 * @return int|false - Category ID if successful, false otherwise
 */
function add_category_ctr($cat_name)
{
    // Validate input
    if (empty($cat_name)) {
        return false;
    }
    
    // Trim and sanitize input
    $cat_name = trim($cat_name);
    
    // Check minimum length
    if (strlen($cat_name) < 2) {
        return false;
    }
    
    $category = new Category();
    return $category->createCategory($cat_name);
}

/**
 * Update category
 * @param int $cat_id - Category ID
 * @param string $cat_name - Updated category name
 * @return bool - True if successful, false otherwise
 */
function update_category_ctr($cat_id, $cat_name)
{
    // Validate inputs
    if (empty($cat_id) || empty($cat_name)) {
        return false;
    }
    
    // Trim and sanitize inputs
    $cat_id = (int)$cat_id;
    $cat_name = trim($cat_name);
    
    // Check minimum length
    if (strlen($cat_name) < 2) {
        return false;
    }
    
    $category = new Category();
    return $category->updateCategory($cat_id, $cat_name);
}

/**
 * Delete category
 * @param int $cat_id - Category ID
 * @return bool - True if successful, false otherwise
 */
function delete_category_ctr($cat_id)
{
    // Validate input
    if (empty($cat_id)) {
        return false;
    }
    
    $cat_id = (int)$cat_id;
    
    $category = new Category();
    return $category->deleteCategory($cat_id);
}