<?php
// classes/customer_class.php
require_once __DIR__ . '/db_connection.php';

class Customer extends DBConnection {
    protected $pdo;

    public function __construct(){
        parent::__construct();
        $this->pdo = $this->getPDO();
    }

    // Check if email exists
    public function emailExists(string $email): bool {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as c FROM customer WHERE customer_email = :email");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        return ($row && $row['c'] > 0);
    }

    // Get customer row by email
    public function getByEmail(string $email) {
        $stmt = $this->pdo->prepare("SELECT * FROM customer WHERE customer_email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Add a new customer. $data is associative array matching DB fields.
    public function add(array $data): bool {
        $sql = "INSERT INTO customer 
            (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact, customer_image, user_role)
            VALUES (:name, :email, :pass, :country, :city, :contact, :image, :role)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':name'    => $data['customer_name'],
            ':email'   => $data['customer_email'],
            ':pass'    => $data['customer_pass'],
            ':country' => $data['customer_country'],
            ':city'    => $data['customer_city'],
            ':contact' => $data['customer_contact'],
            ':image'   => $data['customer_image'] ?? null,
            ':role'    => $data['user_role'] ?? 2  // default to 2 (customer)
        ]);
    }

    // Optionally you may want a getById method (useful later)
    public function getById(int $id) {
        $stmt = $this->pdo->prepare("SELECT * FROM customer WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
