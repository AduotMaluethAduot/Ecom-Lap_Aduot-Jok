<?php

require_once '../database/database.php';

/**
 * Brand class for handling brand operations
 * Updated to use PDO database system
 */
class Brand
{
    private $brand_id;
    private $brand_name;
    private $cat_id;

    public function __construct($brand_id = null)
    {
        if ($brand_id) {
            $this->brand_id = $brand_id;
            $this->loadBrand();
        }
    }

    /**
     * Load brand data by brand ID
     */
    private function loadBrand($brand_id = null)
    {
        if ($brand_id) {
            $this->brand_id = $brand_id;
        }
        if (!$this->brand_id) {
            return false;
        }
        
        $result = fetchOne("SELECT * FROM brands WHERE brand_id = ?", [$this->brand_id]);
        
        if ($result) {
            $this->brand_id = $result['brand_id'];
            $this->brand_name = $result['brand_name'];
            $this->cat_id = $result['cat_id'] ?? null;
            return true;
        }
        return false;
    }

    /**
     * Get all brands with their categories
     * @return array|false - All brands with category info if successful, false otherwise
     */
    public function getAllBrands()
    {
        return fetchAll("
            SELECT b.*, c.cat_name 
            FROM brands b 
            LEFT JOIN categories c ON b.cat_id = c.cat_id 
            ORDER BY c.cat_name, b.brand_name
        ");
    }

    /**
     * Get brands by category
     * @param int $cat_id - Category ID
     * @return array|false - Brands in category if successful, false otherwise
     */
    public function getBrandsByCategory($cat_id)
    {
        return fetchAll("
            SELECT b.*, c.cat_name 
            FROM brands b 
            LEFT JOIN categories c ON b.cat_id = c.cat_id 
            WHERE b.cat_id = ? 
            ORDER BY b.brand_name
        ", [$cat_id]);
    }

    /**
     * Get brand by ID
     * @param int $brand_id - Brand ID
     * @return array|false - Brand data if found, false otherwise
     */
    public function getBrandById($brand_id)
    {
        return fetchOne("
            SELECT b.*, c.cat_name 
            FROM brands b 
            LEFT JOIN categories c ON b.cat_id = c.cat_id 
            WHERE b.brand_id = ?
        ", [$brand_id]);
    }

    /**
     * Get brand by name and category
     * @param string $brand_name - Brand name
     * @param int $cat_id - Category ID
     * @return array|false - Brand data if found, false otherwise
     */
    public function getBrandByNameAndCategory($brand_name, $cat_id)
    {
        return fetchOne("SELECT * FROM brands WHERE brand_name = ? AND cat_id = ?", [$brand_name, $cat_id]);
    }

    /**
     * Create a new brand
     * @param string $brand_name - Brand name
     * @param int $cat_id - Category ID
     * @return int|false - Brand ID if successful, false otherwise
     */
    public function createBrand($brand_name, $cat_id)
    {
        // Check if brand already exists in this category
        if ($this->getBrandByNameAndCategory($brand_name, $cat_id)) {
            return false; // Brand already exists in this category
        }
        
        try {
            $result = executeQuery("INSERT INTO brands (brand_name, cat_id) VALUES (?, ?)", [$brand_name, $cat_id]);
            
            if ($result) {
                return getDB()->lastInsertId();
            }
        } catch (Exception $e) {
            error_log("Brand creation failed: " . $e->getMessage());
        }
        return false;
    }

    /**
     * Update brand information
     * @param int $brand_id - Brand ID
     * @param string $brand_name - Updated brand name
     * @param int $cat_id - Updated category ID
     * @return bool - True if successful, false otherwise
     */
    public function updateBrand($brand_id, $brand_name, $cat_id)
    {
        // Check if another brand with the same name exists in the same category
        $existing = $this->getBrandByNameAndCategory($brand_name, $cat_id);
        if ($existing && $existing['brand_id'] != $brand_id) {
            return false; // Another brand with this name already exists in this category
        }
        
        try {
            $result = executeQuery("UPDATE brands SET brand_name = ?, cat_id = ? WHERE brand_id = ?", [$brand_name, $cat_id, $brand_id]);
            return $result->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Brand update failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete brand
     * @param int $brand_id - Brand ID
     * @return bool - True if successful, false otherwise
     */
    public function deleteBrand($brand_id)
    {
        try {
            $result = executeQuery("DELETE FROM brands WHERE brand_id = ?", [$brand_id]);
            return $result->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Brand deletion failed: " . $e->getMessage());
            return false;
        }
    }

    // Getter methods
    public function getBrandId() { return $this->brand_id; }
    public function getBrandName() { return $this->brand_name; }
    public function getCategoryId() { return $this->cat_id; }
}