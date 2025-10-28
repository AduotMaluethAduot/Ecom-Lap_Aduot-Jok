<?php
/**
 * Database Connection Manager
 * Centralized database connection using PDO
 * Usage: require_once 'db/database.php';
 */

// Load configuration
require_once __DIR__ . '/../config/config.php';

class Database
{
    private static $instance = null;
    private $connection;
    
    // Database configuration
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $charset = DB_CHARSET;
    
    private function __construct()
    {
        $this->connect();
    }
    
    /**
     * Get singleton instance
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Create database connection
     */
    private function connect()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        
        try {
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
    
    /**
     * Get the PDO connection
     */
    public function getConnection()
    {
        return $this->connection;
    }
    
    /**
     * Prevent cloning
     */
    private function __clone() {}
    
    /**
     * Prevent unserialization
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}

/**
 * Global function to get database connection
 * Usage: $pdo = getDB();
 */
function getDB()
{
    return Database::getInstance()->getConnection();
}

/**
 * Global function to execute prepared statement
 * Usage: $result = executeQuery("SELECT * FROM customer WHERE email = ?", [$email]);
 */
function executeQuery($sql, $params = [])
{
    $pdo = getDB();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/**
 * Global function to fetch single row
 * Usage: $user = fetchOne("SELECT * FROM customer WHERE id = ?", [$id]);
 */
function fetchOne($sql, $params = [])
{
    $stmt = executeQuery($sql, $params);
    return $stmt->fetch();
}

/**
 * Global function to fetch all rows
 * Usage: $users = fetchAll("SELECT * FROM customer");
 */
function fetchAll($sql, $params = [])
{
    $stmt = executeQuery($sql, $params);
    return $stmt->fetchAll();
}
?>
