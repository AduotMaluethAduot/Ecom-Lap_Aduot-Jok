<?php

require_once '../database/database.php';

/**
 * Product class for handling product operations
 * Updated to use PDO database system
 */
class Product
{
    private $product_id;
    private $product_cat;
    private $product_brand;
    private $product_title;
    private $product_price;
    private $product_desc;
    private $product_image;
    private $product_keywords;

    public function __construct($product_id = null)
    {
        if ($product_id) {
            $this->product_id = $product_id;
            $this->loadProduct();
        }
    }

    /**
     * Load product data by product ID
     */
    private function loadProduct($product_id = null)
    {
        if ($product_id) {
            $this->product_id = $product_id;
        }
        if (!$this->product_id) {
            return false;
        }
        
        $result = fetchOne("SELECT * FROM products WHERE product_id = ?", [$this->product_id]);
        
        if ($result) {
            $this->product_id = $result['product_id'];
            $this->product_cat = $result['product_cat'];
            $this->product_brand = $result['product_brand'];
            $this->product_title = $result['product_title'];
            $this->product_price = $result['product_price'];
            $this->product_desc = $result['product_desc'];
            $this->product_image = $result['product_image'];
            $this->product_keywords = $result['product_keywords'];
            return true;
        }
        return false;
    }

    /**
     * Get all products with their categories and brands
     * @return array|false - All products with category and brand info if successful, false otherwise
     */
    public function getAllProducts()
    {
        return fetchAll("
            SELECT p.*, c.cat_name, b.brand_name 
            FROM products p 
            LEFT JOIN categories c ON p.product_cat = c.cat_id 
            LEFT JOIN brands b ON p.product_brand = b.brand_id 
            ORDER BY c.cat_name, b.brand_name, p.product_title
        ");
    }

    /**
     * Get products by category
     * @param int $cat_id - Category ID
     * @return array|false - Products in category if successful, false otherwise
     */
    public function getProductsByCategory($cat_id)
    {
        return fetchAll("
            SELECT p.*, c.cat_name, b.brand_name 
            FROM products p 
            LEFT JOIN categories c ON p.product_cat = c.cat_id 
            LEFT JOIN brands b ON p.product_brand = b.brand_id 
            WHERE p.product_cat = ? 
            ORDER BY b.brand_name, p.product_title
        ", [$cat_id]);
    }

    /**
     * Get products by brand
     * @param int $brand_id - Brand ID
     * @return array|false - Products in brand if successful, false otherwise
     */
    public function getProductsByBrand($brand_id)
    {
        return fetchAll("
            SELECT p.*, c.cat_name, b.brand_name 
            FROM products p 
            LEFT JOIN categories c ON p.product_cat = c.cat_id 
            LEFT JOIN brands b ON p.product_brand = b.brand_id 
            WHERE p.product_brand = ? 
            ORDER BY p.product_title
        ", [$brand_id]);
    }

    /**
     * Get product by ID
     * @param int $product_id - Product ID
     * @return array|false - Product data if found, false otherwise
     */
    public function getProductById($product_id)
    {
        return fetchOne("
            SELECT p.*, c.cat_name, b.brand_name 
            FROM products p 
            LEFT JOIN categories c ON p.product_cat = c.cat_id 
            LEFT JOIN brands b ON p.product_brand = b.brand_id 
            WHERE p.product_id = ?
        ", [$product_id]);
    }

    /**
     * Create a new product
     * @param array $product_data - Product data array
     * @return int|false - Product ID if successful, false otherwise
     */
    public function createProduct($product_data)
    {
        $required_fields = ['product_cat', 'product_brand', 'product_title', 'product_price'];
        
        // Validate required fields
        foreach ($required_fields as $field) {
            if (!isset($product_data[$field]) || empty($product_data[$field])) {
                return false;
            }
        }
        
        try {
            $sql = "INSERT INTO products (product_cat, product_brand, product_title, product_price, product_desc, product_image, product_keywords) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $params = [
                $product_data['product_cat'],
                $product_data['product_brand'],
                $product_data['product_title'],
                $product_data['product_price'],
                $product_data['product_desc'] ?? '',
                $product_data['product_image'] ?? '',
                $product_data['product_keywords'] ?? ''
            ];
            
            $result = executeQuery($sql, $params);
            
            if ($result) {
                return getDB()->lastInsertId();
            }
        } catch (Exception $e) {
            error_log("Product creation failed: " . $e->getMessage());
        }
        return false;
    }

    /**
     * Update product information
     * @param int $product_id - Product ID
     * @param array $product_data - Updated product data
     * @return bool - True if successful, false otherwise
     */
    public function updateProduct($product_id, $product_data)
    {
        $required_fields = ['product_cat', 'product_brand', 'product_title', 'product_price'];
        
        // Validate required fields
        foreach ($required_fields as $field) {
            if (!isset($product_data[$field]) || empty($product_data[$field])) {
                return false;
            }
        }
        
        try {
            $sql = "UPDATE products SET product_cat = ?, product_brand = ?, product_title = ?, product_price = ?, product_desc = ?, product_image = ?, product_keywords = ? WHERE product_id = ?";
            $params = [
                $product_data['product_cat'],
                $product_data['product_brand'],
                $product_data['product_title'],
                $product_data['product_price'],
                $product_data['product_desc'] ?? '',
                $product_data['product_image'] ?? '',
                $product_data['product_keywords'] ?? '',
                $product_id
            ];
            
            $result = executeQuery($sql, $params);
            return $result->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Product update failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete product
     * @param int $product_id - Product ID
     * @return bool - True if successful, false otherwise
     */
    public function deleteProduct($product_id)
    {
        try {
            $result = executeQuery("DELETE FROM products WHERE product_id = ?", [$product_id]);
            return $result->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Product deletion failed: " . $e->getMessage());
            return false;
        }
    }

    // Getter methods
    public function getProductId() { return $this->product_id; }
    public function getProductCat() { return $this->product_cat; }
    public function getProductBrand() { return $this->product_brand; }
    public function getProductTitle() { return $this->product_title; }
    public function getProductPrice() { return $this->product_price; }
    public function getProductDesc() { return $this->product_desc; }
    public function getProductImage() { return $this->product_image; }
    public function getProductKeywords() { return $this->product_keywords; }

    /**
     * View all products with pagination
     * @param int $limit - Number of products per page
     * @param int $offset - Offset for pagination
     * @return array|false - Array of products if successful, false otherwise
     */
    public function viewAllProducts($limit = 10, $offset = 0)
    {
        try {
            $db = getDB();
            $sql = "SELECT p.*, c.cat_name, b.brand_name 
                    FROM products p 
                    LEFT JOIN categories c ON p.product_cat = c.cat_id 
                    LEFT JOIN brands b ON p.product_brand = b.brand_id 
                    ORDER BY p.product_id DESC 
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in viewAllProducts: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Search products by query
     * @param string $query - Search query
     * @param int $limit - Number of results per page
     * @param int $offset - Offset for pagination
     * @return array|false - Array of products if successful, false otherwise
     */
    public function searchProducts($query, $limit = 10, $offset = 0)
    {
        try {
            $db = getDB();
            $searchTerm = '%' . $query . '%';
            
            $sql = "SELECT p.*, c.cat_name, b.brand_name 
                    FROM products p 
                    LEFT JOIN categories c ON p.product_cat = c.cat_id 
                    LEFT JOIN brands b ON p.product_brand = b.brand_id 
                    WHERE p.product_title LIKE :query 
                    OR p.product_desc LIKE :query 
                    OR p.product_keywords LIKE :query 
                    OR c.cat_name LIKE :query 
                    OR b.brand_name LIKE :query
                    ORDER BY p.product_title ASC 
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':query', $searchTerm);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in searchProducts: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Filter products by category
     * @param int $cat_id - Category ID
     * @param int $limit - Number of results per page
     * @param int $offset - Offset for pagination
     * @return array|false - Array of products if successful, false otherwise
     */
    public function filterProductsByCategory($cat_id, $limit = 10, $offset = 0)
    {
        try {
            $db = getDB();
            $sql = "SELECT p.*, c.cat_name, b.brand_name 
                    FROM products p 
                    LEFT JOIN categories c ON p.product_cat = c.cat_id 
                    LEFT JOIN brands b ON p.product_brand = b.brand_id 
                    WHERE p.product_cat = :cat_id 
                    ORDER BY p.product_title ASC 
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':cat_id', $cat_id, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in filterProductsByCategory: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Filter products by brand
     * @param int $brand_id - Brand ID
     * @param int $limit - Number of results per page
     * @param int $offset - Offset for pagination
     * @return array|false - Array of products if successful, false otherwise
     */
    public function filterProductsByBrand($brand_id, $limit = 10, $offset = 0)
    {
        try {
            $db = getDB();
            $sql = "SELECT p.*, c.cat_name, b.brand_name 
                    FROM products p 
                    LEFT JOIN categories c ON p.product_cat = c.cat_id 
                    LEFT JOIN brands b ON p.product_brand = b.brand_id 
                    WHERE p.product_brand = :brand_id 
                    ORDER BY p.product_title ASC 
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':brand_id', $brand_id, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in filterProductsByBrand: " . $e->getMessage());
            return false;
        }
    }

    /**
     * View single product with full details
     * @param int $product_id - Product ID
     * @return array|false - Product data if successful, false otherwise
     */
    public function viewSingleProduct($product_id)
    {
        try {
            $db = getDB();
            $sql = "SELECT p.*, c.cat_name, b.brand_name 
                    FROM products p 
                    LEFT JOIN categories c ON p.product_cat = c.cat_id 
                    LEFT JOIN brands b ON p.product_brand = b.brand_id 
                    WHERE p.product_id = :product_id";
            
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in viewSingleProduct: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Advanced search with multiple filters
     * @param array $filters - Array of filters (query, category, brand, min_price, max_price)
     * @param int $limit - Number of results per page
     * @param int $offset - Offset for pagination
     * @return array|false - Array of products if successful, false otherwise
     */
    public function advancedSearch($filters, $limit = 10, $offset = 0)
    {
        try {
            $db = getDB();
            $sql = "SELECT p.*, c.cat_name, b.brand_name 
                    FROM products p 
                    LEFT JOIN categories c ON p.product_cat = c.cat_id 
                    LEFT JOIN brands b ON p.product_brand = b.brand_id 
                    WHERE 1=1";
            
            $params = [];
            
            // Add search query filter
            if (!empty($filters['query'])) {
                $sql .= " AND (p.product_title LIKE :query 
                         OR p.product_desc LIKE :query 
                         OR p.product_keywords LIKE :query 
                         OR c.cat_name LIKE :query 
                         OR b.brand_name LIKE :query)";
                $params[':query'] = '%' . $filters['query'] . '%';
            }
            
            // Add category filter
            if (!empty($filters['category'])) {
                $sql .= " AND p.product_cat = :category";
                $params[':category'] = $filters['category'];
            }
            
            // Add brand filter
            if (!empty($filters['brand'])) {
                $sql .= " AND p.product_brand = :brand";
                $params[':brand'] = $filters['brand'];
            }
            
            // Add price range filters
            if (!empty($filters['min_price'])) {
                $sql .= " AND p.product_price >= :min_price";
                $params[':min_price'] = $filters['min_price'];
            }
            
            if (!empty($filters['max_price'])) {
                $sql .= " AND p.product_price <= :max_price";
                $params[':max_price'] = $filters['max_price'];
            }
            
            $sql .= " ORDER BY p.product_title ASC LIMIT :limit OFFSET :offset";
            
            $stmt = $db->prepare($sql);
            
            // Bind all parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in advancedSearch: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get total count of products for pagination
     * @param array $filters - Optional filters for counting
     * @return int - Total count of products
     */
    public function getTotalProductsCount($filters = [])
    {
        try {
            $db = getDB();
            $sql = "SELECT COUNT(*) as total 
                    FROM products p 
                    LEFT JOIN categories c ON p.product_cat = c.cat_id 
                    LEFT JOIN brands b ON p.product_brand = b.brand_id 
                    WHERE 1=1";
            
            $params = [];
            
            // Add same filters as advancedSearch
            if (!empty($filters['query'])) {
                $sql .= " AND (p.product_title LIKE :query 
                         OR p.product_desc LIKE :query 
                         OR p.product_keywords LIKE :query 
                         OR c.cat_name LIKE :query 
                         OR b.brand_name LIKE :query)";
                $params[':query'] = '%' . $filters['query'] . '%';
            }
            
            if (!empty($filters['category'])) {
                $sql .= " AND p.product_cat = :category";
                $params[':category'] = $filters['category'];
            }
            
            if (!empty($filters['brand'])) {
                $sql .= " AND p.product_brand = :brand";
                $params[':brand'] = $filters['brand'];
            }
            
            if (!empty($filters['min_price'])) {
                $sql .= " AND p.product_price >= :min_price";
                $params[':min_price'] = $filters['min_price'];
            }
            
            if (!empty($filters['max_price'])) {
                $sql .= " AND p.product_price <= :max_price";
                $params[':max_price'] = $filters['max_price'];
            }
            
            $stmt = $db->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return (int)$result['total'];
        } catch (PDOException $e) {
            error_log("Error in getTotalProductsCount: " . $e->getMessage());
            return 0;
        }
    }
}