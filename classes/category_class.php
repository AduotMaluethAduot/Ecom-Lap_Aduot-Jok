<?php

require_once '../database/database.php';

/**
 * Category class for handling category operations
 * Updated to use PDO database system
 */
class Category
{
    private $cat_id;
    private $cat_name;

    public function __construct($cat_id = null)
    {
        if ($cat_id) {
            $this->cat_id = $cat_id;
            $this->loadCategory();
        }
    }

    /**
     * Load category data by category ID
     */
    private function loadCategory($cat_id = null)
    {
        if ($cat_id) {
            $this->cat_id = $cat_id;
        }
        if (!$this->cat_id) {
            return false;
        }
        
        $result = fetchOne("SELECT * FROM categories WHERE cat_id = ?", [$this->cat_id]);
        
        if ($result) {
            $this->cat_id = $result['cat_id'];
            $this->cat_name = $result['cat_name'];
            return true;
        }
        return false;
    }

    /**
     * Get all categories
     * @return array|false - All categories if successful, false otherwise
     */
    public function getAllCategories()
    {
        return fetchAll("SELECT * FROM categories ORDER BY cat_name");
    }

    /**
     * Get category by ID
     * @param int $cat_id - Category ID
     * @return array|false - Category data if found, false otherwise
     */
    public function getCategoryById($cat_id)
    {
        return fetchOne("SELECT * FROM categories WHERE cat_id = ?", [$cat_id]);
    }

    /**
     * Get category by name
     * @param string $cat_name - Category name
     * @return array|false - Category data if found, false otherwise
     */
    public function getCategoryByName($cat_name)
    {
        return fetchOne("SELECT * FROM categories WHERE cat_name = ?", [$cat_name]);
    }

    /**
     * Create a new category
     * @param string $cat_name - Category name
     * @return int|false - Category ID if successful, false otherwise
     */
    public function createCategory($cat_name)
    {
        // Check if category already exists
        if ($this->getCategoryByName($cat_name)) {
            return false; // Category already exists
        }
        
        try {
            $result = executeQuery("INSERT INTO categories (cat_name) VALUES (?)", [$cat_name]);
            
            if ($result) {
                return getDB()->lastInsertId();
            }
        } catch (Exception $e) {
            error_log("Category creation failed: " . $e->getMessage());
        }
        return false;
    }

    /**
     * Update category information
     * @param int $cat_id - Category ID
     * @param string $cat_name - Updated category name
     * @return bool - True if successful, false otherwise
     */
    public function updateCategory($cat_id, $cat_name)
    {
        // Check if another category with the same name exists
        $existing = $this->getCategoryByName($cat_name);
        if ($existing && $existing['cat_id'] != $cat_id) {
            return false; // Another category with this name already exists
        }
        
        try {
            $result = executeQuery("UPDATE categories SET cat_name = ? WHERE cat_id = ?", [$cat_name, $cat_id]);
            return $result->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Category update failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete category
     * @param int $cat_id - Category ID
     * @return bool - True if successful, false otherwise
     */
    public function deleteCategory($cat_id)
    {
        try {
            $result = executeQuery("DELETE FROM categories WHERE cat_id = ?", [$cat_id]);
            return $result->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Category deletion failed: " . $e->getMessage());
            return false;
        }
    }

    // Getter methods
    public function getCategoryId() { return $this->cat_id; }
    public function getCategoryName() { return $this->cat_name; }
}