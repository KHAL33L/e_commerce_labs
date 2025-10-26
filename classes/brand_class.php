<?php
// classes/brand_class.php
require_once __DIR__ . '/db_connection.php';

class Brand extends DBConnection {
    protected $pdo;
    public function __construct(){
        parent::__construct();
        $this->pdo = $this->getPDO();
    }

    public function add(array $data): bool {
        $sql = "INSERT INTO brand (brand_name, category_id, user_id) VALUES (:name, :cat, :uid)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':name' => $data['brand_name'],
            ':cat'  => $data['category_id'],
            ':uid'  => $data['user_id']
        ]);
    }

    public function getByUser(int $user_id): array {
        $stmt = $this->pdo->prepare("SELECT b.id, b.brand_name, b.category_id, b.created_at, c.category_name 
                                     FROM brand b 
                                     LEFT JOIN category c ON c.id = b.category_id
                                     WHERE b.user_id = :uid
                                     ORDER BY b.created_at DESC");
        $stmt->execute([':uid'=>$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByCategoryAndUser(int $cat_id, int $user_id): array {
        $stmt = $this->pdo->prepare("SELECT id, brand_name FROM brand WHERE category_id = :cat AND user_id = :uid ORDER BY brand_name");
        $stmt->execute([':cat'=>$cat_id, ':uid'=>$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update(int $id, string $newName, int $user_id): bool {
        $stmt = $this->pdo->prepare("UPDATE brand SET brand_name = :name WHERE id = :id AND user_id = :uid");
        return $stmt->execute([':name'=>$newName, ':id'=>$id, ':uid'=>$user_id]);
    }

    public function delete(int $id, int $user_id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM brand WHERE id = :id AND user_id = :uid");
        return $stmt->execute([':id'=>$id, ':uid'=>$user_id]);
    }
}
