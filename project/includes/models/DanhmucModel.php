<?php
require_once '../../config/config.php';

class CategoryModel {
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function addCategory($name)
    {
        $sql = "INSERT INTO danh_muc (ten_danh_muc) VALUES (?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$name]);
    }

    public function getAll()
    {
        $sql = "SELECT * FROM danh_muc ORDER BY id_danh_muc DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
