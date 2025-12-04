<?php
require_once __DIR__ . '/../../config/config.php';

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

    public function deleteCategory($id)
    {
        $sql = "DELETE FROM danh_muc WHERE id_danh_muc = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function updateCategory($id, $name)
    {
        $sql = "UPDATE danh_muc SET ten_danh_muc = ? WHERE id_danh_muc = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$name, $id]);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM danh_muc WHERE id_danh_muc = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
