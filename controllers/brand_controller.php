<?php
// controllers/brand_controller.php
require_once __DIR__ . '/../classes/brand_class.php';

class BrandController {
    private $model;
    public function __construct() {
        $this->model = new Brand();
    }

    public function add_brand_ctr(array $kwargs) {
        $name = trim($kwargs['brand_name'] ?? '');
        $cat = (int)($kwargs['category_id'] ?? 0);
        $uid = (int)($kwargs['user_id'] ?? 0);
        if ($name==='' || $cat<=0 || $uid<=0) return ['success'=>false,'message'=>'Invalid input'];

        // uniqueness check (case-insensitive) for this user + category
        $existing = $this->model->getByCategoryAndUser($cat, $uid);
        foreach ($existing as $b) {
            if (strcasecmp($b['brand_name'], $name) === 0) {
                return ['success'=>false,'message'=>'Brand already exists for this category.'];
            }
        }
        $ok = $this->model->add(['brand_name'=>$name,'category_id'=>$cat,'user_id'=>$uid]);
        return $ok ? ['success'=>true,'message'=>'Brand added.'] : ['success'=>false,'message'=>'Failed to add brand.'];
    }

    public function fetch_user_brands_ctr(int $user_id) {
        return $this->model->getByUser($user_id);
    }

    public function update_brand_ctr(array $kwargs) {
        $id = (int)($kwargs['id'] ?? 0);
        $name = trim($kwargs['brand_name'] ?? '');
        $user_id = (int)($kwargs['user_id'] ?? 0);
        if ($id<=0 || $user_id<=0 || $name==='') return ['success'=>false,'message'=>'Invalid input'];

        // ensure uniqueness within same category for user: fetch brand to know its category
        // For simplicity, check all brands for user
        $all = $this->model->getByUser($user_id);
        $currentCat = null;
        foreach ($all as $b) {
            if ($b['id'] == $id) $currentCat = $b['category_id'];
        }
        if ($currentCat===null) return ['success'=>false,'message'=>'Brand not found.'];
        foreach ($all as $b) {
            if ($b['id'] != $id && (int)$b['category_id'] === (int)$currentCat && strcasecmp($b['brand_name'],$name)===0) {
                return ['success'=>false,'message'=>'Another brand with that name exists in this category.'];
            }
        }
        $ok = $this->model->update($id, $name, $user_id);
        return $ok ? ['success'=>true,'message'=>'Brand updated.'] : ['success'=>false,'message'=>'Update failed.'];
    }

    public function delete_brand_ctr(int $id, int $user_id) {
        if ($id<=0 || $user_id<=0) return ['success'=>false,'message'=>'Invalid input'];
        $ok = $this->model->delete($id, $user_id);
        return $ok ? ['success'=>true,'message'=>'Brand deleted.'] : ['success'=>false,'message'=>'Delete failed.'];
    }
}
