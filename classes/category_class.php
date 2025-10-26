<?php
// classes/category_class.php
require_once __DIR__ . '/db_connection.php';

class Category extends DBConnection {
    protected $pdo;

    public function __construct() {
        parent::__construct();
        $this->pdo = $this->getPDO();
    }

    // Add category - returns true on success, false on failure (duplicate handled by caller)
    public function add(array $data): bool {
        $sql = "INSERT INTO category (category_name, user_id) VALUES (:name, :user_id)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':name' => $data['category_name'],
            ':user_id' => $data['user_id']
        ]);
    }

    // Get all categories created by a user (ordered newest first)
    public function getByUser(int $user_id): array {
        $stmt = $this->pdo->prepare("SELECT id, category_name, created_at FROM category WHERE user_id = :uid ORDER BY created_at DESC");
        $stmt->execute([':uid' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update category name by id (ensures uniqueness should be handled in controller)
    public function update(int $id, string $newName, int $user_id): bool {
        $stmt = $this->pdo->prepare("UPDATE category SET category_name = :name WHERE id = :id AND user_id = :uid");
        return $stmt->execute([':name'=> $newName, ':id'=>$id, ':uid'=>$user_id]);
    }

    // Delete category by id for a specific user
    public function delete(int $id, int $user_id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM category WHERE id = :id AND user_id = :uid");
        return $stmt->execute([':id'=>$id, ':uid'=>$user_id]);
    }

    // Optional: get category by id for a user
    public function getById(int $id, int $user_id) {
        $stmt = $this->pdo->prepare("SELECT id, category_name FROM category WHERE id = :id AND user_id = :uid LIMIT 1");
        $stmt->execute([':id'=>$id, ':uid'=>$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
