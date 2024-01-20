<?php

class Database {

    public $conn;

    /**
     * Constructor for Database class
     * 
     * @param array $config
     */
    public function __construct($config) {

        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset=utf8";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        ];

        try {
            $this->conn = new PDO($dsn, $config['username'], $config['password'], $options);
        }
        catch (PDOException $e) {
            throw new Exception("Database connection failed: {$e->getMessage()}");
        }
    }

    /**
     * Query the database
     *
     * @param string $query
     * @return PDOStatement
     * @throws PDOException
     */
    public function query($query) {
        try{
            //at this moment no placeholders are used because no user input is being handled yet
            $stmt = $this->conn->prepare($query);   
            $stmt->execute();
            return $stmt;
        } 
        catch (PDOException $e) {
            throw new Exception("Query error: {$e->getMessage()}");
        }
    }
}