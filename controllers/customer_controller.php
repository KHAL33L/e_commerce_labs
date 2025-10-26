<?php
// controllers/customer_controller.php
require_once __DIR__ . '/../classes/customer_class.php';

class CustomerController {
    private $customerModel;

    public function __construct() {
        $this->customerModel = new Customer();
    }

    // wrapper for registration
    public function register_customer_ctr(array $kwargs) {
        // (existing register logic)
        $email = filter_var($kwargs['customer_email'], FILTER_VALIDATE_EMAIL);
        if (!$email) {
            return ['success'=>false, 'message'=>'Invalid email address.'];
        }

        if ($this->customerModel->emailExists($email)) {
            return ['success'=>false, 'message'=>'Email already registered.'];
        }

        // Hash password
        $pwHash = password_hash($kwargs['customer_pass'], PASSWORD_DEFAULT);

        $payload = [
            'customer_name' => substr(trim($kwargs['customer_name']), 0, 100),
            'customer_email' => $email,
            'customer_pass' => $pwHash,
            'customer_country' => substr(trim($kwargs['customer_country']), 0, 30),
            'customer_city' => substr(trim($kwargs['customer_city']), 0, 30),
            'customer_contact' => substr(trim($kwargs['customer_contact']), 0, 15),
            'customer_image' => $kwargs['customer_image'] ?? null,
            'user_role' => $kwargs['user_role'] ?? 2
        ];

        $ok = $this->customerModel->add($payload);
        if ($ok) {
            return ['success'=>true, 'message'=>'Registration successful.'];
        } else {
            return ['success'=>false, 'message'=>'Registration failed.'];
        }
    }

    public function check_email_ctr(string $email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['exists' => false, 'valid' => false];
        }
        $exists = $this->customerModel->emailExists($email);
        return ['exists' => $exists, 'valid' => true];
    }

    // login wrapper, returns ['success'=>bool, 'message'=>string, 'user'=>array|null]
    public function login_customer_ctr(string $email, string $password) {
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$email) {
            return ['success'=>false, 'message'=>'Invalid email format.'];
        }

        $user = $this->customerModel->getByEmail($email);
        if (!$user) {
            return ['success'=>false, 'message'=>'No account found for that email.'];
        }

        // Note: stored password should be hashed via password_hash during registration
        if (!isset($user['customer_pass']) || !password_verify($password, $user['customer_pass'])) {
            return ['success'=>false, 'message'=>'Incorrect password.'];
        }

        // Login success - return user data (avoid returning password hash)
        unset($user['customer_pass']);
        return ['success'=>true, 'message'=>'Login successful.', 'user'=>$user];
    }
}
