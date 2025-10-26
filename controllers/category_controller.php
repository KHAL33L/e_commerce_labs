<?php
// controllers/category_controller.php
require_once __DIR__ . '/../classes/category_class.php';

class CategoryController {
    private $model;

    public function __construct() {
        $this->model = new Category();
    }

    public function add_category_ctr(array $kwargs) {
        // Basic validation
        $name = trim($kwargs['category_name'] ?? '');
        $user_id = (int)($kwargs['user_id'] ?? 0);
        if ($name === '' || $user_id <= 0) {
            return ['success'=>false, 'message'=>'Category name and user required.'];
        }
        // Check uniqueness for this user
        $existing = $this->model->getByUser($user_id);
        foreach ($existing as $c) {
            if (strcasecmp($c['category_name'], $name) === 0) {
                return ['success'=>false, 'message'=>'You already have a category with that name.'];
            }
        }
        $ok = $this->model->add(['category_name'=>$name, 'user_id'=>$user_id]);
        return $ok ? ['success'=>true, 'message'=>'Category added.'] : ['success'=>false, 'message'=>'Could not add category.'];
    }

    public function fetch_user_categories_ctr(int $user_id) {
        return $this->model->getByUser($user_id);
    }

    public function update_category_ctr(array $kwargs) {
        $id = (int)($kwargs['id'] ?? 0);
        $name = trim($kwargs['category_name'] ?? '');
        $user_id = (int)($kwargs['user_id'] ?? 0);
        if ($id <= 0 || $name === '' || $user_id <= 0) {
            return ['success'=>false, 'message'=>'Invalid input.'];
        }
        // Ensure uniqueness (exclude current id)
        $existing = $this->model->getByUser($user_id);
        foreach ($existing as $c) {
            if ($c['id'] != $id && strcasecmp($c['category_name'], $name) === 0) {
                return ['success'=>false, 'message'=>'Another category with that name exists.'];
            }
        }
        $ok = $this->model->update($id, $name, $user_id);
        return $ok ? ['success'=>true, 'message'=>'Category updated.'] : ['success'=>false, 'message'=>'Update failed.'];
    }

    public function delete_category_ctr(int $id, int $user_id) {
        if ($id <= 0 || $user_id <= 0) {
            return ['success'=>false, 'message'=>'Invalid input.'];
        }
        $ok = $this->model->delete($id, $user_id);
        return $ok ? ['success'=>true, 'message'=>'Category deleted.'] : ['success'=>false, 'message'=>'Delete failed.'];
    }
}
