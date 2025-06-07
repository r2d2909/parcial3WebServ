<?php
class Conexion {
    private $hostBd = 'localhost';
    private $nameBd = 'uts';
    private $userBd = 'root';
    private $passBd = '';
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO(
                "mysql:host=$this->hostBd;dbname=$this->nameBd;charset=utf8",
                $this->userBd,
                $this->passBd
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Error al conectar la BDS: " . $e->getMessage();
            $this->pdo = null;
        }
    }

    public function obtenerConexion() {
        return $this->pdo;
    }
}
?>
