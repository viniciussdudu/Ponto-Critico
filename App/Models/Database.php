<?php

namespace App\Models;

use PDO;
use PDOException;

class Database {
    private static $instancia = null;

    public static function conectar() {
        if (self::$instancia === null) {
            try {
                $host = 'localhost';
                $port = '5432'; // Porta padrão do PostgreSQL
                $db   = 'ponto_critico';
                $user = 'postgres'; // Coloque o seu usuário do Postgres aqui
                $pass = '2077'; // Coloque a sua senha do Postgres aqui

                $dsn = "pgsql:host=$host;port=$port;dbname=$db";
                
                self::$instancia = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                die("Erro catastrófico na conexão com o banco: " . $e->getMessage());
            }
        }
        return self::$instancia;
    }
}