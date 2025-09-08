<?php

namespace App\Config;

class Database
{
    private static $instance = null;
    private $connection;

    private $host = 'localhost';
    private $dbname = 'taller_php';
    private $username = 'postgres';
    private $password = '950430';
    private $port = '5432';

    private function __construct()
    {
        try {
            $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->dbname}";
            $this->connection = new \PDO($dsn, $this->username, $this->password);
            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            die('Error de conexión: ' . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function createTables()
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS empleados (
                id SERIAL PRIMARY KEY,
                nombre VARCHAR(100) NOT NULL,
                salario DECIMAL(10,2) NOT NULL,
                departamento VARCHAR(50) NOT NULL,
                email VARCHAR(100),
                foto VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS ventas (
                id SERIAL PRIMARY KEY,
                cliente VARCHAR(100) NOT NULL,
                producto VARCHAR(100) NOT NULL,
                cantidad INTEGER NOT NULL,
                precio_unitario DECIMAL(10,2) NOT NULL,
                fecha_venta DATE NOT NULL,
                imagen_producto VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ";

        try {
            $this->connection->exec($sql);
            
            // Agregar columnas si no existen (para compatibilidad con bases de datos existentes)
            $this->addColumnsIfNotExist();
            
            return true;
        } catch (\PDOException $e) {
            error_log('Error creando tablas: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Agregar columnas nuevas si no existen
     */
    private function addColumnsIfNotExist()
    {
        try {
            // Verificar si la columna email existe en empleados
            $result = $this->connection->query("
                SELECT column_name 
                FROM information_schema.columns 
                WHERE table_name='empleados' AND column_name='email'
            ");
            
            if ($result->rowCount() == 0) {
                $this->connection->exec("ALTER TABLE empleados ADD COLUMN email VARCHAR(100)");
            }

            // Verificar si la columna foto existe en empleados
            $result = $this->connection->query("
                SELECT column_name 
                FROM information_schema.columns 
                WHERE table_name='empleados' AND column_name='foto'
            ");
            
            if ($result->rowCount() == 0) {
                $this->connection->exec("ALTER TABLE empleados ADD COLUMN foto VARCHAR(255)");
            }

            // Verificar si la columna imagen_producto existe en ventas
            $result = $this->connection->query("
                SELECT column_name 
                FROM information_schema.columns 
                WHERE table_name='ventas' AND column_name='imagen_producto'
            ");
            
            if ($result->rowCount() == 0) {
                $this->connection->exec("ALTER TABLE ventas ADD COLUMN imagen_producto VARCHAR(255)");
            }

        } catch (\PDOException $e) {
            error_log('Error agregando columnas: ' . $e->getMessage());
        }
    }
}