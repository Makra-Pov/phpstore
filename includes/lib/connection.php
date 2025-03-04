<?php
class Connection {
    // Default connection values
    private static $host = 'localhost';
    private static $dbname = 'ecommerce_db';
    private static $username = 'root';
    private static $password = 'root';
    private static $conn;

    // Establish the database connection
    private static function connect() {
        if (self::$conn === null) {
            try {
                self::$conn = new PDO(
                    "mysql:host=" . self::$host . ";dbname=" . self::$dbname,
                    self::$username,
                    self::$password
                );
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        return self::$conn;
    }

    // Fetch all rows from a table or query
    public static function getAll($table, $conditions = [], $params = []) {
        $sql = "SELECT * FROM $table";
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        $stmt = self::executeQuery($sql, $params);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : false;
    }

    // Fetch a single row from a table or query
    public static function getOne($table, $conditions = [], $params = []) {
        $sql = "SELECT * FROM $table";
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        $sql .= " LIMIT 1";
        $stmt = self::executeQuery($sql, $params);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }

    // Insert a new record into a table
    public static function insert($table, $data) {
        $columns = implode(", ", array_keys($data));
        $values = ":" . implode(", :", array_keys($data));
        $sql = "INSERT INTO $table ($columns) VALUES ($values)";
        return self::executeQuery($sql, $data);
    }

    // Update records in a table
    public static function update($table, $data, $conditions = [], $params = []) {
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "$key = :$key";
        }
        $sql = "UPDATE $table SET " . implode(", ", $set);
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        return self::executeQuery($sql, array_merge($data, $params));
    }

    // Delete records from a table
    public static function delete($table, $conditions = [], $params = []) {
        // Ensure the table name is valid
        if (empty($table)) {
            throw new InvalidArgumentException("Table name cannot be empty.");
        }
    
        // Start building the SQL query
        $sql = "DELETE FROM `$table`";
    
        // Add conditions if provided
        if (!empty($conditions)) {
            if (count($conditions) !== count($params)) {
                throw new InvalidArgumentException("Conditions and parameters do not match.");
            }
    
            // Correctly build WHERE clause using column names
            $sql .= " WHERE " . implode(" AND ", $conditions);
        } else {
            throw new InvalidArgumentException("Cannot delete without conditions.");
        }
    
        // Debugging - Log the actual SQL query
        error_log("Executing DELETE Query: $sql with params: " . json_encode($params));
    
        // Execute the query with parameters
        return self::executeQuery($sql, $params);
    }
    
    // Execute a query
    private static function executeQuery($sql, $params = []) {
        try {
            $stmt = self::connect()->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            die("Query execution failed: " . $e->getMessage());
        }
    }
}